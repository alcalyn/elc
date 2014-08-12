<?php

namespace EL\CoreBundle\Services;

use Doctrine\ORM\EntityManager;
use EL\CoreBundle\Entity\Player;
use EL\CoreBundle\Repository\PlayerRepository;

class PlayerService
{
    /**
     * @var EntityManager
     */
    private $em;
    
    /**
     * @param \Doctrine\ORM\EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }
    
    /**
     * Return player default page
     * 
     * @param \EL\CoreBundle\Services\Player $player
     * 
     * @return string
     */
    public function getLink(Player $player)
    {
        return '#'.strtolower($player->getPseudo());
    }
    
    /**
     * Get player by pseudo, case insensitive, or null if no player found
     * 
     * @param string $pseudo
     * 
     * @return Player|null
     */
    public function getPlayerByPseudoCI($pseudo)
    {
        return $this->getPlayerRepository()->getPlayerByPseudoCI(substr($pseudo, 1));
    }
    
    /**
     * @return PlayerRepository
     */
    public function getPlayerRepository()
    {
        return $this->em->getRepository('CoreBundle:Player');
    }
}
