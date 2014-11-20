<?php

namespace EL\Core\Entity;

/**
 * Player
 */
class Player implements \JsonSerializable
{
    /**
     * @var integer
     */
    protected $id;

    /**
     * @var string
     */
    protected $pseudo;

    /**
     * @var string
     */
    protected $passwordHash;

    /**
     * @var boolean
     */
    protected $invited;
    
    /**
     * @var boolean
     */
    protected $bot;

    /**
     * @var \DateTime
     */
    protected $dateCreate;


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
    
    /**
     * Implements JsonSerializable
     * 
     * @return array
     */
    public function jsonSerialize()
    {
        return array(
            'id'            => $this->getId(),
            'pseudo'        => $this->getPseudo(),
            'invited'       => $this->getInvited(),
            'bot'           => $this->getBot(),
        );
    }
    
    /**
     * Instances of this class will be serialized in session,
     * so be carefull when creating attributes having an instance
     * 
     * @return string[]
     */
    public function __sleep()
    {
        return array(
            'id',
            'pseudo',
            'passwordHash',
            'invited',
            'bot',
            'dateCreate',
        );
    }
}
