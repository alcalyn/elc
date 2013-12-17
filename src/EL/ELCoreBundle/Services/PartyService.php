<?php

namespace EL\ELCoreBundle\Services;

use EL\ELCoreBundle\Entity\Party;
use EL\ELCoreBundle\Services\GameService;
use EL\ELCoreBundle\Entity\Slot;
use EL\ELCoreBundle\Model\Slug;
use EL\ELCoreBundle\Entity\Player;
use EL\ELCoreBundle\Form\Entity\PartyOptions;
use EL\ELCoreBundle\Model\ELCoreException;


class PartyService extends GameService
{
    
    const OK            = 0;
    const ENDED_PARTY   = 1;
    const NO_FREE_SLOT  = 2;
    const ALREADY_JOIN  = 3;
    const STARTED_PARTY = 4;
    
    
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
    
    
    
    public function __construct($em, $illflushitlater, $session)
    {
        parent::__construct($em);
        
        $this->illflushitlater  = $illflushitlater;
        $this->session          = $session;
    }
    
    
    /**
     * @param \EL\ELCoreBundle\Entity\Party $party
     * @return \EL\ELCoreBundle\Services\PartyService
     */
    public function setParty(Party $party)
    {
        $this->party = $party;
        $this->setGame($party->getGame());
        return $this;
    }
    
    /**
     * @param string $slug
     * @param string $locale
     * @return \EL\ELCoreBundle\Services\PartyService
     */
    public function setPartyBySlug($slug, $locale)
    {
        $party = $this->em
                ->getRepository('ELCoreBundle:Party')
                ->findByLang($locale, $slug);
        
        $this->setParty($party);
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
    public function createParty($partyOption)
    {
        $party = new Party();
        $slug = Slug::slug($partyOption->getTitle());
        $party
                ->setGame($this->getGame())
                ->setHost($this->session->getPlayer())
                ->setTitle($partyOption->getTitle())
                ->setSlug($slug)
                ->setOpen(!$partyOption->getPrivate())
                ->setAllowChat(!$partyOption->getDisallowChat())
                ->setAllowObservers(!$partyOption->getDisallowObservers())
                ->setState(Party::PREPARATION)
        ;
        
        $count_slug = $this->em
        		->getRepository('ELCoreBundle:Party')
        		->countSlug($slug)
        ;
        
        if (intval($count_slug) > 0) {
        	$this->em->persist($party);
        	$this->em->flush();
        	$party->setSlug($slug.'-'.$party->getId());
        } else {
	        $this->illflushitlater->persist($party);
        }
        
        $this->illflushitlater->flush();
        
        return $party;
    }
    
    
    public function createSlots(array $slots_configuration)
    {
        $this->needParty();
        
        $position = 1;
        
        foreach ($slots_configuration['slots'] as $s) {
            $isHost =  isset($s['host']) && $s['host'];
            $isOpen = !isset($s['host']) || $s['host'];
            $score  =  isset($s['score']) ? $s['score'] : 0 ;
            
            $slot = new Slot();
            $slot
                    ->setParty($this->getParty())
                    ->setPosition($position++)
                    ->setScore($score)
                    ->setOpen($isOpen);
            
            if ($isHost) {
                $slot->setPlayer($this->session->getPlayer());
            }
            
            $this->em->persist($slot);
        }
        
        $this->em->flush();
    }
    
    
    /**
     * Check if player can join the party.
     * If join is true, the player join the party if he can.
     * 
     * if player cant join :
     *      return PartyService::ENDED_PARTY    if party has ended
     *      return PartyService::NO_FREE_SLOT   if party is full
     *      return PartyService::ALREADY_JOIN   if player joined this party yet
     *      return PartyService::STARTED_PARTY  if party has started and is not room
     * 
     * else return PartyService::ENDED_PARTY (0) if he can join,
     *      or has joined if $join is true
     * 
     * @param \EL\ELCoreBundle\Entity\Player $player
     * @param boolean $join
     * @return integer 0: ok, or error
     */
    public function canJoin(Player $player, $join = false)
    {
        $this->needParty();
        
        $party  = $this->getParty();
        $state  = $party->getState();
        $room   = $party->getRoom();
        
        if ($state === Party::ENDED) {
            return self::ENDED_PARTY;
        }
        
        $freeSlot       = null;
        $alreadyJoin    = false;
        
        if ($state === Party::PREPARATION || $room) {
            $slots = $party->getSlots();
            
            foreach ($slots as $slot) {
                if ($slot->isFree()) {
                    $freeSlot = $slot;
                } else {
                    if ($slot->getPlayer() == $player) {
                        $alreadyJoin = true;
                        break;
                    }
                }
            }
        } else {
            return self::STARTED_PARTY;
        }
        
        if ($alreadyJoin) {
            return self::ALREADY_JOIN;
        }
        
        if (!$freeSlot) {
            return self::NO_FREE_SLOT;
        }
        
        if ($join) {
            $this->affectPlayerToSlot($player, $freeSlot);
        }
        
        return self::OK;
    }
    
    
    public function explainJoinResult($result)
    {
    	$message = null;
    	
    	switch ($result) {
            case self::OK:
                $result = array(
                    'type'		=> 'info',
                    'message'	=> 'You have joined the party'
                );
                break;
            
            case self::ENDED_PARTY:
                $result = array(
                    'type'		=> 'danger',
                    'message'	=> 'Error, this party has ended'
                );
                break;
            
            case self::NO_FREE_SLOT:
                $result = array(
                    'type'		=> 'danger',
                    'message'	=> 'You cannot join the party, there is no free slot'
                );
                break;
            
            case self::ALREADY_JOIN:
                $result = array(
                    'type'		=> 'warning',
                    'message'	=> 'You have already join this party'
                );
                break;
            
            case self::STARTED_PARTY:
                $result = array(
                    'type'		=> 'danger',
                    'message'	=> 'This party has already started, and is not in room mode'
                );
                break;
            
            default:
                $result = array(
                    'type'		=> 'danger',
                    'message'	=> 'You cannot join the party, unknown error : #'.$result
                );
                break;
        }
        
        return $result;
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
     * @param \EL\ELCoreBundle\Entity\Player $player
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
     * @param Player $player, or nothing for current user
     * @return boolean
     * 			true if user has quit,
     * 			false if user was not in this party
     */
    public function quitParty($player_id = null)
    {
    	if (is_null($player_id)) {
    		$player_id = $this->session->getPlayer()->getId();
    	}
    	
    	$slot = $this->em
    		->getRepository('ELCoreBundle:Slot')
    		->findOneBy(array(
    			'player_id'	=> $player_id,
    			'party_id'	=> $this->getParty()->getId(),
    		));
    	
    	if ($slot) {
    		$slot->setPlayer();
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
    			->getSlot($index)
    	;
    	
    	if ($slot->getOpen() !== $open) {
    		$slot->setOpen($open);
    		$this->illflushitlater->flush();
    	}
    }
    
    public function isHost(Player $player = null)
    {
    	if (is_null($player)) {
    		$player = $this->session->getPlayer()->getId();
    	}
    	
    	return $this->getParty()->getHost()->getId() === $player->getId();
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