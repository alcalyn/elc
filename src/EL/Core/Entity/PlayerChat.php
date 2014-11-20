<?php

namespace EL\Core\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Player_Chat
 */
class PlayerChat
{
    /**
     * @var integer
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
