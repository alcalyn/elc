<?php

namespace EL\ELCoreBundle\Services;

use EL\ELCoreBundle\Entity\Game;
use EL\ELCoreBundle\Entity\Party;

class PartyService
{
    
    private $em;
    
    /**
     *
     * @var IllFlushItLaterService
     */
    private $illflushitlater;
    
    /**
     *
     * @var SessionService
     */
    private $session;
    
    /**
     *
     * @var SlugService
     */
    private $slug;
    
    /**
     *
     * @var Game
     */
    private $game = null;
    
    
    
    public function __construct($em, $illflushitlater, $session, $slug)
    {
        $this->em = $em;
        $this->illflushitlater = $illflushitlater;
        $this->session = $session;
        $this->slug = $slug;
    }
    
    
    public function setGame(Game $game)
    {
        $this->game = $game;
        return $this;
    }
    
    
    public function createParty($title, $open = true)
    {
        $party = new Party();
        $party
                ->setGame($this->game)
                ->setHost($this->session->getPlayer())
                ->setTitle($title)
                ->setSlug($this->slug->slug($title))
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
    
    
    public function generateRandomTitle()
    {
        $this->needGame();
        return $this->game->getTitle().' party '.rand(10000, 99999);
    }
    
    
    private function needGame()
    {
        if (is_null($this->game)) {
            throw new \Exception('PartyService : game must be defined');
        }
    }
}