<?php

namespace EL\Bundle\CoreBundle\Services;

use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use EL\Bundle\CoreBundle\Event\PartyEvent;
use EL\Bundle\CoreBundle\Event\PartyRemakeEvent;
use Doctrine\ORM\EntityManager;
use EL\Bundle\CoreBundle\Services\GameService;
use EL\Core\Entity\Party;
use EL\Core\Entity\Slot;
use EL\Core\Entity\Player;
use EL\Bundle\CoreBundle\Services\SessionService;
use EL\Bundle\CoreBundle\Exception\ELCoreException;
use EL\Bundle\CoreBundle\Exception\ELUserException;
use EL\Bundle\CoreBundle\AbstractGame\Model\ELGameInterface;

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
     * @param \EL\Bundle\CoreBundle\Services\SessionService $session
     * @param \Symfony\Component\EventDispatcher\EventDispatcherInterface $eventDispatcher
     */
    public function __construct(EntityManager $em, SessionService $session, EventDispatcherInterface $eventDispatcher)
    {
        parent::__construct($em);
        
        $this->session          = $session;
        $this->eventDispatcher  = $eventDispatcher;
    }
    
    
    /**
     * @param \EL\Core\Entity\Party $party
     * @return \EL\Bundle\CoreBundle\Services\PartyService
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
     * @return \EL\Bundle\CoreBundle\Services\PartyService
     */
    public function setPartyBySlug($slugParty, $slugGame, $locale, Container $container = null)
    {
        try {
            $party = $this->em
                    ->getRepository('Core:Party')
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
     * @param \EL\Core\Entity\Party $coreParty
     * @param ELGameInterface $gameInterface
     * @param \stdClass $extendedOptions
     */
    public function create(Party $coreParty, ELGameInterface $gameInterface, $extendedOptions)
    {
        $this->setParty($coreParty);
        $coreParty->setDateCreate(new \DateTime());
        
        // Dispatch party create after event
        $event = new PartyEvent($this, $gameInterface, $extendedOptions);
        $this->eventDispatcher->dispatch(PartyEvent::PARTY_CREATE_BEFORE, $event);
        
        // create slots from given slots configuration
        $slotsConfiguration = $gameInterface->getSlotsConfiguration($extendedOptions);
        $this->createSlots($slotsConfiguration, $coreParty);
        
        // Dispatch party create after event
        $this->eventDispatcher->dispatch(PartyEvent::PARTY_CREATE_AFTER, $event);
    }
    
    /**
     * Create slots from $slotsConfiguration
     * 
     * @param array $slotsConfiguration
     * @param Party $party
     */
    public function createSlots(array $slotsConfiguration, Party $party)
    {
        $position = 0;
        
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
     * @param \EL\Core\Entity\Player $player
     * @param integer $slotIndex preference. If defined and free, join this slot. Else join first free slot.
     * @param boolean $join false to not join even if possible
     * @param Party $party to join
     * 
     * @return PartyService
     * 
     * @throws ELUserException if cannot join
     */
    public function join(Player $player = null, $slotIndex = -1, $join = true, Party $party = null)
    {
        if (null === $player) {
            $player = $this->session->getPlayer();
        }
        
        if (null === $party) {
            $this->needParty();
            $party = $this->getParty();
        }
        
        $this->checkJoinOnPartyState($party);
        
        $slots              = $party->getSlots();
        $nextFreeSlot       = $this->getNextFreeSlot($slots);
        $alreadyJoinSlot    = $this->getPlayerSlot($slots, $player);
        
        $this->checkAlreadyJoinOrFull($nextFreeSlot, $alreadyJoinSlot, $slotIndex);
        
        // set nextFreeSlot to slotIndex if free and defined
        if ($slotIndex >= 0) {
            $this->checkSlotsExists($slots, $slotIndex);
            
            if ($slots->get($slotIndex)->isFree()) {
                $nextFreeSlot = $slots->get($slotIndex);
            } else {
                throw new ELUserException('slot.notfree');
            }
        }
            
        // Assign player to nextFreeSlot
        if ($join) {
            $this->doJoin($player, $nextFreeSlot, $alreadyJoinSlot);
        }
        
        return $this;
    }
    
    /**
     * Assign a player to a slot
     * 
     * @param \EL\Core\Entity\Player $player
     * @param \EL\Core\Entity\Slot $slot
     * @param \EL\Core\Entity\Slot $oldSlot the slot the player occupies actually
     */
    private function doJoin(Player $player, Slot $slot, Slot $oldSlot = null)
    {
        // Set player on his new slot
        $slot->setPlayer($player);
        $this->em->persist($slot);

        if ($oldSlot) {
            // If player change slot, update old slot
            $oldSlot->setPlayer(null);
            $this->em->persist($oldSlot);
        }
    }
    
    /**
     * Check if can join this party depending on its state
     * 
     * @param \EL\Core\Entity\Party $party
     * 
     * @throws ELUserException
     */
    private function checkJoinOnPartyState(Party $party)
    {
        $state = $party->getState();
        
        if ($state === Party::ENDED) {
            throw new ELUserException('cannot.join.party.ended');
        }
        
        if (($state !== Party::PREPARATION) && !$party->getRoom()) {
            throw new ELUserException('cannot.join.party.started');
        }
    }
    
    /**
     * Check if slot that index refers exists
     * 
     * @param Slot[] $slots
     * @param integer $slotIndex
     * 
     * @throws ELUserException
     */
    private function checkSlotsExists($slots, $slotIndex)
    {
        if ($slotIndex >= $slots->count()) {
            throw new ELUserException('slot.notexists');
        }
    }
    
    /**
     * Basic checks for join a party
     * 
     * @param Slot $nextFreeSlot
     * @param Slot $alreadyJoinSlot
     * @param integer $slotIndex if user want to join a specific slot
     * 
     * @throws ELUserException if any error
     */
    private function checkAlreadyJoinOrFull(Slot $nextFreeSlot = null, Slot $alreadyJoinSlot = null, $slotIndex = -1)
    {
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
    }
    
    /**
     * Return next free slots of an array of slots, or null if slots are full
     * 
     * @param Slot[] $slots
     * 
     * @return Slot|null
     */
    public function getNextFreeSlot($slots)
    {
        foreach ($slots as $slot) {
            if ($slot->isFree()) {
                return $slot;
            }
        }
        
        return null;
    }
    
    /**
     * Return the slot the player occupies
     * 
     * @param Slot[] $slots
     * @param Player $player
     * 
     * @return Slot|null
     */
    public function getPlayerSlot($slots, Player $player = null)
    {
        if (null === $player) {
            $player = $this->session->getPlayer();
        }
        
        foreach ($slots as $slot) {
            if (!$slot->isFree() && ($slot->getPlayer() === $player)) {
                return $slot;
            }
        }
        
        return null;
    }
    
    /**
     * Just check if can join or not, return a boolean
     * 
     * @param \EL\Core\Entity\Player $player
     * @param integer $slotIndex
     * @param \EL\Core\Entity\Party $party
     * 
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
     * @param \EL\Core\Entity\Player $player
     * @param \EL\Core\Entity\Party $party
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
     * @param \EL\Core\Entity\Player $player
     * @param \EL\Core\Entity\Party $party
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
            ->getRepository('Core:Slot')
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
     * @return \EL\Bundle\CoreBundle\Services\PartyService
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
     * @param \EL\Core\Entity\Player $player
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
     * @return boolean|null true, or throws ELUserException
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
            $this->doStart($party);
        }
    }
    
    
    /**
     * Start party and dispatch events.
     * 
     * @param \EL\Core\Entity\Party $party
     */
    private function doStart(Party $party)
    {
        if ($party->getState() === Party::PREPARATION) {
            // Dispatch party start before
            $event = new PartyEvent($this, $this->getGameInterface());
            $this->eventDispatcher->dispatch(PartyEvent::PARTY_START_BEFORE, $event);
            
            // Update party
            $party->setDateStarted(new \DateTime());
            $party->setState(Party::STARTING);
            
            // Dispatch party start after
            $this->eventDispatcher->dispatch(PartyEvent::PARTY_START_AFTER, $event);
        }
        
        if (self::DELAY_BEFORE_START <= 0) {
            $this->doActive($party);
        }
    }
    
    
    /**
     * Active party and dispatch events.
     * 
     * @param \EL\Core\Entity\Party $party
     */
    private function doActive(Party $party)
    {
        // Dispatch active before event
        $event = new PartyEvent($this, $this->getGameInterface());
        $this->eventDispatcher->dispatch(PartyEvent::PARTY_ACTIVE_BEFORE, $event);
        
        // Update party
        $party->setDateStarted(new \DateTime());
        $party->setState(Party::ACTIVE);
        
        // Dispatch active after event
        $this->eventDispatcher->dispatch(PartyEvent::PARTY_ACTIVE_AFTER, $event);
    }
    
    
    /**
     * Check if delay before start has ran out,
     * then start party really
     */
    public function checkDelayBeforeStart()
    {
        $party = $this->getParty();
        
        if ($party->getState() === Party::STARTING) {
            $startDate = clone $party->getDateStarted();
            $startDate->add(new \DateInterval('PT'.self::DELAY_BEFORE_START.'S'));
            $now = new \DateTime();
            
            if ($startDate < $now) {
                $this->doActive();
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
        $this->needGameInterface();
        
        $player         = $this->session->getPlayer();
        $oldCoreParty   = $this->getParty();
        
        // Check if party ended
        if ($oldCoreParty->getState() !== Party::ENDED) {
            throw new ELUserException('party.cannot.remake.notended');
        }
        
        // Check if party already remade, then join
        if (null !== $oldCoreParty->getRemake()) {
            $this->join($player, -1, true, $oldCoreParty->getRemake());
            return $oldCoreParty->getRemake();
        }
        
        $gameInterface = $this->getGameInterface();
        $oldExtendedParty = $gameInterface->loadParty($oldCoreParty);
        $extendedOptions = $gameInterface->getOptions($oldExtendedParty);
        
        // Dispatch party remake event
        $eventBefore = new PartyEvent($this, $gameInterface, $extendedOptions);
        $this->eventDispatcher->dispatch(PartyEvent::PARTY_REMAKE_BEFORE, $eventBefore);
        
        // Else create a clone core party
        $newCoreParty = $this->createPartyRemake($oldCoreParty);
        $newCoreParty->setHost($player);
        
        // Create new party (will dispatch party created event)
        $this->create($newCoreParty, $gameInterface, $extendedOptions);
        
        // Dispatch party remake event
        $eventAfter = new PartyRemakeEvent($this, $gameInterface, $extendedOptions, $oldCoreParty);
        $this->eventDispatcher->dispatch(PartyRemakeEvent::PARTY_REMAKE_AFTER, $eventAfter);
        
        // Persist new core party
        $this->em->persist($newCoreParty);
        
        // Return new party
        return $newCoreParty;
    }
    
    /**
     * Create a remake party from an old party
     * 
     * @param \EL\Core\Entity\Party $oldParty
     * 
     * @return Party
     */
    public function createPartyRemake(Party $oldParty)
    {
        $newParty = new Party();
        
        // Link the old party to the remade party
        $oldParty->setRemake($newParty);
        
        return $newParty
                ->setGame($oldParty->getGame())
                ->setTitle($oldParty->getTitle())
                ->setPrivate($oldParty->getPrivate())
                ->setRoom($oldParty->getRoom())
                ->setDisallowChat($oldParty->getDisallowChat())
                ->setDisallowObservers($oldParty->getDisallowObservers())
        ;
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
                ->getRepository('Core:Party')
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
        return $this->getGameInterface()->loadParty($this->getParty());
    }
    
    /**
     * @param string $locale
     */
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
