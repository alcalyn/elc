<?php

namespace EL\ELCoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Player_Chat
 *
 * @ORM\Table(name="el_core_player_chat")
 * @ORM\Entity(repositoryClass="EL\ELCoreBundle\Repositories\Player_ChatRepository")
 */
class Player_Chat
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }
}