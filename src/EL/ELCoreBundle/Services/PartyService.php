<?php

namespace EL\ELCoreBundle\Services;

use Symfony\Component\DependencyInjection\Container;
use Doctrine\ORM\EntityManager;
use EL\ELCoreBundle\Services\GameService;
use EL\ELCoreBundle\Entity\Party;
use EL\ELCoreBundle\Entity\Slot;
use EL\ELCoreBundle\Entity\Player;
use EL\ELCoreBundle\Services\SessionService;
use EL\ELCoreBundle\Model\Slug;
use EL\ELCoreBundle\Model\ELCoreException;
use EL\ELCoreBundle\Model\ELUserException;
use EL\ELCoreBundle\Form\Entity\PartyOptions;

class PartyService extends GameService
{
    
    const OK            = 0;
    const ENDED_PARTY   = 1;
    const NO_FREE_SLOT  = 2;
    const ALREADY_JOIN  = 3;
    const STARTED_PARTY = 4;
    const NOT_OK        = 10;
    
    /**
     * Number of seconds after host clicked Start,
     * and before game really starting
     * (To avoid start abuse)
     * 
     * @var integer in seconds
     */
    const DELAY_BEFORE_START = -1;
    
    
    /**
     * @var IllFlushItLaterService
     */
    private $illflushitlater;
    
    /**
     * @var SessionService
     */
    private $session;
    
    /**
     * @var Party
     */
    private $party;
    
    
    
    public function __construct(EntityManager $em, IllFlushItLaterService $illflushitlater, SessionService $session)
    {
        parent::__construct($em);
        
        $this->illflushitlater  = $illflushitlater;
        $this->session          = $session;
    }
    
    
    /**
     * @param \EL\ELCoreBundle\Entity\Party $party
     * @return \EL\ELCoreBundle\Services\PartyService
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
     * @param string $slug
     * @param string $locale
     * @return \EL\ELCoreBundle\Services\PartyService
     */
    public function setPartyBySlug($slug, $locale, Container $container = null)
    {
        $party = $this->em
                ->getRepository('ELCoreBundle:Party')
                ->findByLang($locale, $slug);
        
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
     * @param PartyOptions $partyOption
     * @return Party
     */
    public function createParty()
    {
        $party = new Party();
        $party
                ->setGame($this->getGame())
                ->setHost($this->session->getPlayer())
                ->setTitle($this->generateRandomTitle())
        ;
        
        $this->addSlug($party);
        
        return $party;
    }
    
    
    private function addSlug($party)
    {
        $slug = Slug::slug($party->getTitle());
        $party->setSlug($slug);
        
        $count_slug = $this->em
                ->getRepository('ELCoreBundle:Party')
                ->countSlug($slug)
        ;
        
        if (intval($count_slug) > 0) {
            $this->em->persist($party);
            $this->em->flush();
            $party->setSlug($slug.'-'.$party->getId());
        }
    }
    
    
    public function createSlots(array $slots_configuration, Party $party = null)
    {
        if (is_null($party)) {
            $this->needParty();
            $party = $this->getParty();
        }
        
        $position = 1;
        
        foreach ($slots_configuration['slots'] as $s) {
            $isHost =  isset($s['host']) && $s['host'];
            $isOpen = !isset($s['host']) || $s['host'];
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
        
        $this->em->flush();
    }
    
    
    /**
     * Check if player can join the party at slot $slot_index, or an other.
     * If join is true, the player join the party if he can.
     * If player has already join party, he just change slot.
     * 
     * throw ELUserException if player cant join,
     * else return true if he can join
     * 
     * @param \EL\ELCoreBundle\Entity\Player $player
     * @param integer $slot_index preference. If defined and free, join this slot. Else join first free slot.
     * @param boolean $join, false to not join even if possible
     * @param boolean $party to join
     * @return true if can join
     * @throws ELUserException if cannot join
     */
    public function join(Player $player = null, $slot_index = -1, $join = true, Party $party = null)
    {
        $this->needParty();
        
        $player = is_null($player) ? $this->session->getPlayer() : $player ;
        $party  = is_null($party) ? $this->getParty() : $party ;
        $state  = $party->getState();
        $room   = $party->getRoom();
        
        if ($state === Party::ENDED) {
            throw new ELUserException('cannot.join.party.ended');
        }
        
        $freeSlot       = null;
        $alreadyJoin    = null;
        $slots          = array();
        
        if ($state === Party::PREPARATION || $room) {
            $slots = $party->getSlots();
            
            foreach ($slots as $slot) {
                if ($slot->isFree()) {
                    $freeSlot = $slot;
                } else {
                    if ($slot->getPlayer() === $player) {
                        $alreadyJoin = $slot;
                    }
                }
                
                if (!is_null($freeSlot) && !is_null($alreadyJoin)) {
                    break;
                }
            }
        } else {
            throw new ELUserException('cannot.join.party.started');
        }
        
        if (!$freeSlot) {
            if (is_null($alreadyJoin)) {
                throw new ELUserException('cannot.join.nofreeslot');
            } else {
                throw new ELUserException('youhave.already.join');
            }
        }
        
        if ($join || !is_null($alreadyJoin)) {
            $changeSlot = false;
            
            if (($slot_index >= 0) && ($slot_index < count($slots))) {
                $slot = $slots[$slot_index];
                if ($slot->isFree()) {
                    $freeSlot = $slot;
                    $changeSlot = true;
                }
            }
            
            if (!is_null($alreadyJoin)) {
                if ($changeSlot) {
                    $alreadyJoin->setPlayer(null);
                    $freeSlot->setPlayer($player);
                    $this->illflushitlater->persist($alreadyJoin);
                    $this->illflushitlater->persist($freeSlot);
                    $this->illflushitlater->flush();
                }
            } else {
                $freeSlot->setPlayer($player);
                $this->illflushitlater->persist($freeSlot);
                $this->illflushitlater->flush();
            }
        }
        
        if (is_null($alreadyJoin)) {
            return true;
        } else {
            throw new ELUserException('youhave.already.join');
        }
    }
    
    
    public function canJoin(Player $player = null, $slot_index = -1, Party $party = null)
    {
        try {
            return $this->join($player, $slot_index, false, $party);
        } catch (ELUserException $e) {
            return $e;
        }
    }
    
    
    public function inParty(Player $player = null, Party $party = null)
    {
        if (is_null($player)) {
            $player = $this->session->getPlayer();
        }
        
        $party  = is_null($party) ? $this->getParty() : $party ;
        
        foreach ($party->getSlots() as $slot) {
            if ($slot->hasPlayer() && ($slot->getPlayer()->getId() === $player->getId())) {
                return true;
            }
        }
        
        return false;
    }
    
    
    
    /**
     * A player join a party on a free slot
     * 
     * @param \EL\ELCoreBundle\Entity\Player $player
     * @param \EL\ELCoreBundle\Entity\Slot $slot
     */
    public function affectPlayerToSlot(Player $player, Slot $slot)
    {
        $slot->setPlayer($player);
        $this->em->persist($slot);
        $this->em->flush();
    }
    
    
    /**
     * Ban a player on this party
     * 
     * @param integer $player_id to ban of this party
     */
    public function ban($player_id)
    {
        if (!$this->isHost()) {
            return false;
        }
        
        $this->quitParty($player_id);
        
        return true;
    }
    
    
    /**
     * Quit party by removing user from its slot.
     * 
     * @param integer $player_id, or nothing for current user
     * @return boolean
     *             true if user has quit,
     *             false if user was not in this party
     */
    public function quitParty($player_id = null)
    {
        if (is_null($player_id)) {
            $player_id = $this->session->getPlayer()->getId();
        }
        
        $slot = $this->em
            ->getRepository('ELCoreBundle:Slot')
            ->findOneByPlayerAndParty($player_id, $this->getParty()->getId())
        ;
        
        if ($slot) {
            $slot->setPlayer(null);
            $this->illflushitlater->persist($slot);
            $this->illflushitlater->flush($slot);
            return true;
        } else {
            return false;
        }
    }
    
    
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
            $this->illflushitlater->flush();
        }
    }
    
    /**
     * Change slots order
     * 
     * @param array $indexes containing new indexes, as :
     *                 0, 2, 1      => switch second and third slot
     *                 2, 0, 1, 3   => set the third slot at first position
     */
    public function reorderSlots(array $indexes)
    {
        $this->needParty();
        
        $slots = $this->getParty()->getSlots();
        
        $i = 0;
        foreach ($slots as $slot) {
            $new_position = intval($indexes[$i++]) + 1;
            $old_position = intval($slot->getPosition());
            
            if ($new_position !== $old_position) {
                $slot->setPosition($new_position);
                $this->illflushitlater->persist($slot);
            }
        }
        
        $this->illflushitlater->flush();
    }
    
    public function isHost(Player $player = null)
    {
        if (!$this->getParty()->hasHost()) {
            return false;
        }
        
        if (is_null($player)) {
            $player = $this->session->getPlayer();
        }
        
        return $this->getParty()->getHost()->getId() === $player->getId();
    }
    
    
    /**
     * Return true if party is ready to start
     * 
     * @param boolean $start, if true, start the party if ready
     * @return integer
     */
    public function start($start = true)
    {
        $party = $this->getParty();
        
        if ($party->getState() === Party::PREPARATION) {
            
            if ($this->getExtendedGame()->canStart($this) !== true) {
                throw new ELCoreException('Extended party canStart() must return true or throw ELUserException');
            }
            
            if ($start) {
                $party
                    ->setState(Party::STARTING)
                    ->setDateStarted(new \DateTime())
                ;
                
                if (self::DELAY_BEFORE_START <= 0) {
                    $party->setState(Party::ACTIVE);
                }
                
                $this->illflushitlater->persist($party);
                $this->illflushitlater->flush();
            }
            
            return true;
        } else {
            throw new ELUserException('cannot.start.already.started');
        }
    }
    
    
    /**
     * Start the party if ready, else return error code
     * 
     * @return integer
     */
    public function canStart()
    {
        try {
            return $this->canStart(false);
        } catch (ELUserException $e) {
            return false;
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
            $start_date = clone $party->getDateStarted();
            $start_date->add(new \DateInterval('PT'.self::DELAY_BEFORE_START.'S'));
            $now = new \DateTime();
            
            if ($start_date < $now) {
                $party
                    ->setState(Party::ACTIVE)
                    ->setDateStarted($start_date)
                ;
                
                $this->illflushitlater->persist($party);
                $this->illflushitlater->flush();
            }
        }
    }
    
    
    /**
     * End the party if currently active
     */
    public function end()
    {
        $party = $this->getParty();
        
        if ($party->getState() === Party::ACTIVE) {
            $party
                ->setState(Party::ENDED)
                ->setDateEnded(new \DateTime())
            ;
            
            return self::OK;
        } else {
            return self::NOT_OK;
        }
    }
    
    
    /**
     * Remake the current party by creating core party
     * and call extended party createRemake with new core party as argument
     * 
     * @param ELGameInterface $extended_party_service
     * @return Party
     */
    public function remake($extended_party_service)
    {
        $player = $this->session->getPlayer();
        $party  = $this->getParty();
        $remake = $party->getRemake();
        
        if (null !== $remake) {
            $this->join($player, -1, true, $remake);
            return $remake;
        }
        
        $clone_core_party       = $party->createRemake();
        
        $party->setRemake($clone_core_party);
        $clone_core_party->setHost($player);
        $this->addSlug($clone_core_party);
        
        $options                = $extended_party_service->loadOptions($this->getParty());
        $slots_configuration    = $extended_party_service->getSlotsConfiguration($options);
        
        $this->createSlots($slots_configuration, $clone_core_party);
        
        $this->illflushitlater->persist($clone_core_party);
        $this->illflushitlater->persist($party);
        $this->illflushitlater->flush();
        
        $extended_party_service->createRemake($party->getSlug(), $clone_core_party);
        
        return $clone_core_party;
    }
    
    
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
    
    
    public function generateRandomTitle()
    {
        $this->needGame();
        return $this->getGame()->getTitle().' party '.rand(10000, 99999);
    }
    
    protected function needParty()
    {
        if (is_null($this->getParty())) {
            throw new \Exception('PartyService : party must be defined');
        }
    }
}
