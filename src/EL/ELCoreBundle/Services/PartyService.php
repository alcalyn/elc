<?php

namespace EL\ELCoreBundle\Services;

use EL\ELCoreBundle\Entity\Party;
use EL\ELCoreBundle\Services\GameService;
use EL\ELCoreBundle\Entity\Slot;
use EL\ELCoreBundle\Model\Slug;
use EL\ELCoreBundle\Entity\Player;
use EL\ELCoreBundle\Model\ELCoreException;


class PartyService extends GameService
{
    
    const OK            = 0;
    const ENDED_PARTY   = 1;
    const NO_FREE_SLOT  = 2;
    const ALREADY_JOIN  = 3;
    
    
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
     * @param string $title
     * @param type boolean
     * @return \EL\ELCoreBundle\Entity\Party
     */
    public function createParty($title, $open = true)
    {
        $party = new Party();
        $party
                ->setGame($this->getGame())
                ->setHost($this->session->getPlayer())
                ->setTitle($title)
                ->setSlug(Slug::slug($title))
                ->setOpen($open)
                ->setState(Party::PREPARATION);
        
        $this->em->persist($party);
        $this->em->flush();
        
        $party
                ->setSlug($party->getSlug().'-'.$party->getId());
        
        $this->illflushitlater->persist($party);
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
        }
        
        if ($alreadyJoin) {
            return self::ALREADY_JOIN;
        } else if ($freeSlot) {
            $this->affectPlayerToSlot($player, $freeSlot);
        } else {
            return self::NO_FREE_SLOT;
        }
    }
    
    
    
    /**
     * A player join a party on a free slot
     * 
     * @param \EL\ELCoreBundle\Entity\Player $player
     * @param \EL\ELCoreBundle\Entity\Slot $slot
     */
    private function affectPlayerToSlot(Player $player, Slot $slot)
    {
        $slot->setPlayer($player);
        $this->em->persist($slot);
        $this->em->flush();
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