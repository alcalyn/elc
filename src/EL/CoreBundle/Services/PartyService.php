<?php

namespace EL\CoreBundle\Services;

use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use EL\CoreBundle\Event\PartyEvent;
use Doctrine\ORM\EntityManager;
use EL\CoreBundle\Services\GameService;
use EL\CoreBundle\Entity\Party;
use EL\CoreBundle\Entity\Slot;
use EL\CoreBundle\Entity\Player;
use EL\CoreBundle\Services\SessionService;
use EL\CoreBundle\Exception\ELCoreException;
use EL\CoreBundle\Exception\ELUserException;
use EL\CoreBundle\Form\Entity\PartyOptions;
use EL\AbstractGameBundle\Model\ELGameInterface;

class PartyService extends GameService
{
    /**
     * Number of seconds after host clicked Start,
     * and before game really starting
     * (To avoid start abuse)
     * 
     * @var integer in seconds
     */
    const DELAY_BEFORE_START = -1;
    
    
    /**
     * @var SessionService
     */
    private $session;
    
    /**
     * @var Party
     */
    private $party;
    
    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;
    
    /**
     * Constructor
     * 
     * @param \Doctrine\ORM\EntityManager $em
     * @param \EL\CoreBundle\Services\SessionService $session
     * @param \Symfony\Component\EventDispatcher\EventDispatcherInterface $eventDispatcher
     */
    public function __construct(EntityManager $em, SessionService $session, EventDispatcherInterface $eventDispatcher)
    {
        parent::__construct($em);
        
        $this->session          = $session;
        $this->eventDispatcher  = $eventDispatcher;
    }
    
    
    /**
     * @param \EL\CoreBundle\Entity\Party $party
     * @return \EL\CoreBundle\Services\PartyService
     */
    public function setParty(Party $party, Container $container = null)
    {
        $this->party = $party;
        $this->setGame($party->getGame(), $container);
        
        if ($party->getState() === Party::STARTING) {
            $this->checkDelayBeforeStart();
        }
        
        return $this;
    }
    
    
    /**
     * @param string $slugParty
     * @param string $slugGame
     * @param string $locale
     * @return \EL\CoreBundle\Services\PartyService
     */
    public function setPartyBySlug($slugParty, $slugGame, $locale, Container $container = null)
    {
        try {
            $party = $this->em
                    ->getRepository('CoreBundle:Party')
                    ->findByLang($locale, $slugParty, $slugGame)
            ;
        } catch (\Doctrine\ORM\NoResultException $e) {
            throw new ELCoreException('No party "'.$slugParty.'" for game "'.$slugGame.'"');
        }
        
        $this->setParty($party, $container);
        return $this;
    }
    
    
    /**
     * @return Party
     */
    public function getParty()
    {
        return $this->party;
    }
    
    /**
     * Create a new instance of Party
     * 
     * @param string $locale
     * 
     * @return Party
     */
    public function createParty($locale)
    {
        $party = new Party();
        $party
                ->setGame($this->getGame())
                ->setHost($this->session->getPlayer())
                ->setTitle($this->generateRandomTitle($locale))
        ;
        
        return $party;
    }
    
    /**
     * Init a party and slots configuration after player has created it
     * 
     * @param \EL\CoreBundle\Entity\Party $coreParty
     * @param \EL\CoreBundle\Services\ELGameInterface $gameInterface
     * @param \stdClass $extendedOptions
     */
    public function create(Party $coreParty, ELGameInterface $gameInterface, $extendedOptions)
    {
        $coreParty->setDateCreate(new \DateTime());
        
        // create slots from given slots configuration
        $slotsConfiguration = $gameInterface->getSlotsConfiguration($extendedOptions);
        $this->createSlots($slotsConfiguration);
        
        // Dispatch party creation event
        $event = new PartyEvent($this, $gameInterface, $extendedOptions);
        $this->eventDispatcher->dispatch(PartyEvent::PARTY_CREATED, $event);
    }
    
    /**
     * Create slots from $slotsConfiguration
     * 
     * @param array $slotsConfiguration
     */
    public function createSlots(array $slotsConfiguration)
    {
        $this->needParty();
        
        $party      = $this->getParty();
        $position   = 0;
        
        foreach ($slotsConfiguration['slots'] as $s) {
            $isHost =  isset($s['host']) && $s['host'];
            $isOpen = !isset($s['open']) || $s['open'];
            $score  =  isset($s['score']) ? $s['score'] : 0 ;
            
            $slot = new Slot();
            $slot
                    ->setParty($party)
                    ->setPosition($position++)
                    ->setScore($score)
                    ->setOpen($isOpen)
            ;
            
            if ($isHost) {
                $slot->setPlayer($this->session->getPlayer());
            }
            
            $this->em->persist($slot);
        }
    }
    
    
    /**
     * Return all players who are in this party
     * 
     * Load SCORES
     * 
     * @return array of players
     */
    public function getPlayers()
    {
        $players = array();
        
        foreach ($this->getParty()->getSlots() as $slot) {
            if ($slot->hasPlayer()) {
                $players []= $slot->getPlayer();
            }
        }
        
        return $players;
    }
    
    
    /**
     * Check if player can join the party at slot $slotIndex, or an other.
     * If join is true, the player join the party if he can.
     * If player has already join party, he just change slot.
     * 
     * throw ELUserException if player cant join,
     * else return true if he can join
     * 
     * @param \EL\CoreBundle\Entity\Player $player
     * @param integer $slotIndex preference. If defined and free, join this slot. Else join first free slot.
     * @param boolean $join, false to not join even if possible
     * @param boolean $party to join
     * 
     * @return PartyService
     * 
     * @throws ELUserException if cannot join
     */
    public function join(Player $player = null, $slotIndex = -1, $join = true, Party $party = null)
    {
        $this->needParty();
        
        $player         = is_null($player) ? $this->session->getPlayer() : $player ;
        $party          = is_null($party) ? $this->getParty() : $party ;
        $state          = $party->getState();
        
        if ($state === Party::ENDED) {
            throw new ELUserException('cannot.join.party.ended');
        }
        
        if (($state !== Party::PREPARATION) && !$party->getRoom()) {
            throw new ELUserException('cannot.join.party.started');
        }
        
        $slots              = $party->getSlots();
        $nextFreeSlot       = null;
        $alreadyJoinSlot    = null;
        
        // find next free slot and current player slot he maybe already joined
        foreach ($slots as $slot) {
            if ($slot->isFree()) {
                $nextFreeSlot = $slot;
            } else {
                if ($slot->getPlayer() === $player) {
                    $alreadyJoinSlot = $slot;
                }
            }

            if ($nextFreeSlot && $alreadyJoinSlot) {
                break;
            }
        }
        
        // Throw exception if there is no free slot
        if (!$nextFreeSlot) {
            if ($alreadyJoinSlot) {
                if ($slotIndex < 0) {
                    throw new ELUserException('youhave.already.join');
                } else {
                    throw new ELUserException('cannot.join.thisslot');
                }
            } else {
                throw new ELUserException('cannot.join.nofreeslot');
            }
        }
        
        // set nextFreeSlot to slotIndex if free and defined
        if ($slotIndex >= 0) {
            if ($slotIndex < $slots->count()) {
                if ($slots->get($slotIndex)->isFree()) {
                    $nextFreeSlot = $slots->get($slotIndex);
                } else {
                    throw new ELUserException('slot.notfree');
                }
            } else {
                throw new ELUserException('slot.notexists');
            }
        }
            
        // Assign player to nextFreeSlot
        if ($join) {
            $nextFreeSlot->setPlayer($player);
            $this->em->persist($nextFreeSlot);
            
            if ($alreadyJoinSlot) {
                $alreadyJoinSlot->setPlayer(null);
                $this->em->persist($alreadyJoinSlot);
            }
        }
        
        return $this;
    }
    
    /**
     * 
     * @param \EL\CoreBundle\Entity\Player $player
     * @param type $slotIndex
     * @param \EL\CoreBundle\Entity\Party $party
     * @return boolean if he can join
     */
    public function canJoin(Player $player = null, $slotIndex = -1, Party $party = null)
    {
        try {
            $this->join($player, $slotIndex, false, $party);
            return true;
        } catch (ELUserException $e) {
            return false;
        }
    }
    
    
    /**
     * Return the position index of $player (default is current)
     * in $party (default is current)
     * 
     * @param \EL\CoreBundle\Entity\Player $player
     * @param \EL\CoreBundle\Entity\Party $party
     * 
     * @return integer
     */
    public function position(Player $player = null, Party $party = null)
    {
        $this->needParty();
        
        if (null === $player) {
            $player = $this->session->getPlayer();
        }
        
        if (null === $party) {
            $party = $this->getParty();
        }
        
        $i = 0;
        
        foreach ($party->getSlots() as $slot) {
            if ($slot->hasPlayer() && ($slot->getPlayer()->getId() === $player->getId())) {
                return $i;
            } else {
                $i++;
            }
        }
        
        return -1;
    }
    
    
    /**
     * Return if $player (default is current) is in $party (default is current)
     * 
     * @param \EL\CoreBundle\Entity\Player $player
     * @param \EL\CoreBundle\Entity\Party $party
     * 
     * @return boolean
     */
    public function inParty(Player $player = null, Party $party = null)
    {
        return $this->position($player, $party) >= 0;
    }
    
    
    /**
     * Ban a player on this party
     * 
     * @param PartyService
     */
    public function ban($playerId)
    {
        if (!$this->isHost()) {
            throw new ELUserException('cannot.ban.nothost');
        }
        
        $this->quitParty($playerId);
        
        return $this;
    }
    
    
    /**
     * Quit party by removing user from its slot.
     * 
     * @param integer $playerId, or nothing for current user
     * @return boolean
     *             true if user has quit,
     *             false if user was not in this party
     */
    public function quitParty($playerId = null)
    {
        if (is_null($playerId)) {
            $playerId = $this->session->getPlayer()->getId();
        }
        
        $slot = $this->em
            ->getRepository('CoreBundle:Slot')
            ->findOneByPlayerAndParty($playerId, $this->getParty()->getId())
        ;
        
        if ($slot) {
            $slot->setPlayer(null);
            $this->em->persist($slot);
            return true;
        } else {
            return false;
        }
    }
    
    /**
     * Open or close a slot
     * 
     * @param integer $index
     * @param boolean $open
     * @return \EL\CoreBundle\Services\PartyService
     */
    public function openSlot($index, $open = true)
    {
        $this->needParty();
        
        $slot = $this
                ->getParty()
                ->getSlots()
                ->get($index)
        ;
        
        if ($slot->getOpen() !== $open) {
            $slot->setOpen($open);
        }
        
        return $this;
    }
    
    /**
     * Change slots order
     * 
     * @param array $indexes containing new indexes, as :
     *                 0, 2, 1      => switch second and third slot
     *                 2, 0, 1, 3   => set the third slot at first position
     * 
     * @return PartyService
     */
    public function reorderSlots(array $indexes)
    {
        $this->needParty();
        
        $slots = $this->getParty()->getSlots();
        
        $i = 0;
        foreach ($slots as $slot) {
            $newPosition = intval($indexes[$i++]);
            $oldPosition = intval($slot->getPosition());
            
            if ($newPosition !== $oldPosition) {
                $slot->setPosition($newPosition);
                $this->em->persist($slot);
            }
        }
        
        return $this;
    }
    
    /**
     * Check if $player is the host of this party
     * 
     * @param \EL\CoreBundle\Entity\Player $player
     * @return boolean
     */
    public function isHost(Player $player = null)
    {
        if (null === $this->getParty()->getHost()) {
            return false;
        }
        
        if (null === $player) {
            $player = $this->session->getPlayer();
        }
        
        return $this->getParty()->getHost() === $player;
    }
    
    
    /**
     * Return true if party is ready to start
     * 
     * @param boolean $start set to false to just check if party can be started
     * 
     * @return boolean true, or throws ELUserException
     * 
     * @throws ELUserException if party cannot be started
     */
    public function start($start = true)
    {
        $party  = $this->getParty();
        $player = $this->session->getPlayer();
        $isHost = is_object($party->getHost()) && ($player->getId() === $party->getHost()->getId());
        
        if (!$isHost) {
            throw new ELUserException('cannot.start.youarenothost');
        }
        
        if ($party->getState() !== Party::PREPARATION) {
            throw new ELUserException('cannot.start.already.started');
        }
            
        if ($start) {
            $party->setDateStarted(new \DateTime());
            $party->setState(Party::STARTING);
            
            $event = new PartyEvent($this, $this->getExtendedGame());
            $this->eventDispatcher->dispatch(PartyEvent::PARTY_STARTED, $event);
            
            if (self::DELAY_BEFORE_START <= 0) {
                $party->setState(Party::ACTIVE);
                $this->eventDispatcher->dispatch(PartyEvent::PARTY_ACTIVED, $event);
            }
        }
    }
    
    
    /**
     * Check if delay before start has ran out,
     * then start party really
     */
    public function checkDelayBeforeStart()
    {
        $party = $this->getParty();
        
        if ($this->getParty()->getState() === Party::STARTING) {
            $startDate = clone $party->getDateStarted();
            $startDate->add(new \DateInterval('PT'.self::DELAY_BEFORE_START.'S'));
            $now = new \DateTime();
            
            if ($startDate < $now) {
                $party
                    ->setState(Party::ACTIVE)
                    ->setDateStarted($startDate)
                ;
                
                $event = new PartyEvent($this, $this->getExtendedGame());
                $this->eventDispatcher->dispatch(PartyEvent::PARTY_ACTIVED, $event);
            }
        }
    }
    
    
    /**
     * End the party if currently active
     * 
     * @return boolean
     * 
     * @throws ELCoreException
     */
    public function end()
    {
        $party = $this->getParty();
        
        if ($party->getState() === Party::ACTIVE) {
            $party
                ->setState(Party::ENDED)
                ->setDateEnded(new \DateTime())
            ;
            
            return true;
        } else {
            throw new ELCoreException('Party cannot be ended because not active');
        }
    }
    
    
    /**
     * Remake the current party by creating core party
     * and call extended party createRemake with new core party as argument
     * 
     * @return Party
     * 
     * @throws ELUserException
     */
    public function remake()
    {
        $this->needExtendedGame();
        
        $player                 = $this->session->getPlayer();
        $oldCoreParty           = $this->getParty();
        
        // Check if party ended
        if ($oldCoreParty->getState() !== Party::ENDED) {
            throw new ELUserException('party.cannot.remake.notended');
        }
        
        // Check if party already remade, then join
        if (null !== $oldCoreParty->getRemake()) {
            $this->join($player, -1, true, $oldCoreParty->getRemake());
            return $oldCoreParty->getRemake();
        }
        
        // Else create a clone core party
        $remakeCoreParty = $oldCoreParty->createRemake();
        
        // And link the old party to the remade party, and set new host
        $oldCoreParty->setRemake($remakeCoreParty);
        $remakeCoreParty->setHost($player);
        
        // Create new party
        $oldExtendedParty = $this->getExtendedGame()->loadParty($oldCoreParty);
        $this->create($remakeCoreParty, $this->getExtendedGame(), $oldExtendedParty);
        // Create a RemakePartyEvent, trigger here for after if needed, and another before
        
        // Persist new party and old party
        $this->em->persist($remakeCoreParty);
        
        // Return new party
        return $remakeCoreParty;
    }
    
    /**
     * Return a list of player who are in remake party
     * 
     * @return array
     */
    public function getPlayersInRemakeParty()
    {
        $this->needParty();
        
        $party = $this->em
                ->getRepository('CoreBundle:Party')
                ->findPlayersInRemakeParty($this->getParty())
        ;
        
        $players = array();
        
        if (null === $party) {
            return $players;
        }
        
        if (null === $party->getRemake()) {
            return $players;
        }
        
        foreach ($party->getRemake()->getSlots() as $slot) {
            if ($slot->hasPlayer()) {
                $players []= $slot->getPlayer()->getId();
            }
        }
        
        return $players;
    }
    
    /**
     * Return the number of player in this party
     * 
     * @return int
     */
    public function getNbPlayer()
    {
        $this->needParty();
        
        $nb = 0;
        
        foreach ($this->getParty()->getSlots() as $slot) {
            if ($slot->hasPlayer()) {
                $nb++;
            }
        }
        
        return $nb;
    }
    
    /**
     * Load extended party
     * 
     * @return \stdClass
     */
    public function loadExtendedParty()
    {
        return $this->getExtendedGame()->loadParty($this->getParty());
    }
    
    public function generateRandomTitle($locale)
    {
        $this->needGame();
        return $this->getGame()->getTitle().' '.rand(10000, 99999);
    }
    
    protected function needParty()
    {
        if (is_null($this->getParty())) {
            throw new \Exception('PartyService : party must be defined');
        }
    }
}
