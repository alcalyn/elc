<?php

namespace EL\ELCoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Player
 *
 * @ORM\Table(name="el_core_player")
 * @ORM\Entity(repositoryClass="EL\ELCoreBundle\Repository\PlayerRepository")
 */
class Player implements \JsonSerializable
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
     * @var string
     *
     * @ORM\Column(name="pseudo", type="string", length=31, unique=true)
     */
    private $pseudo;

    /**
     * @var string
     *
     * @ORM\Column(name="password_hash", type="string", length=32, nullable=true)
     */
    private $passwordHash;

    /**
     * @var boolean
     *
     * @ORM\Column(name="invited", type="boolean")
     */
    private $invited;
    
    /**
     * @var boolean
     *
     * @ORM\Column(name="bot", type="boolean")
     */
    private $bot;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_create", type="datetime")
     */
    private $dateCreate;


    public function __construct()
    {
        $this
            ->setDateCreate(new \DateTime())
            ->setBot(false)
        ;
    }
    
    
    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set pseudo
     *
     * @param string $pseudo
     * @return Player
     */
    public function setPseudo($pseudo)
    {
        $this->pseudo = $pseudo;
    
        return $this;
    }

    /**
     * Get pseudo
     *
     * @return string 
     */
    public function getPseudo()
    {
        return $this->pseudo;
    }

    /**
     * Set passwordHash
     *
     * @param string $passwordHash
     * @return Player
     */
    public function setPasswordHash($passwordHash)
    {
        $this->passwordHash = $passwordHash;
    
        return $this;
    }

    /**
     * Get passwordHash
     *
     * @return string 
     */
    public function getPasswordHash()
    {
        return $this->passwordHash;
    }

    /**
     * Set invited
     *
     * @param boolean $invited
     * @return Player
     */
    public function setInvited($invited)
    {
        $this->invited = $invited;
    
        return $this;
    }

    /**
     * Get invited
     *
     * @return boolean 
     */
    public function getInvited()
    {
        return $this->invited;
    }

    /**
     * Set dateCreate
     *
     * @param \DateTime $dateCreate
     * @return Player
     */
    public function setDateCreate($dateCreate)
    {
        $this->dateCreate = $dateCreate;
    
        return $this;
    }

    /**
     * Get dateCreate
     *
     * @return \DateTime 
     */
    public function getDateCreate()
    {
        return $this->dateCreate;
    }

    /**
     * Set bot
     *
     * @param boolean $bot
     * @return Player
     */
    public function setBot($bot)
    {
        $this->bot = $bot;
    
        return $this;
    }

    /**
     * Get bot
     *
     * @return boolean 
     */
    public function getBot()
    {
        return $this->bot;
    }
    
    public function jsonSerialize()
    {
        return array(
            'id'            => $this->getId(),
            'pseudo'        => $this->getPseudo(),
            'invited'       => $this->getInvited(),
            'bot'           => $this->getBot(),
        );
    }
}
