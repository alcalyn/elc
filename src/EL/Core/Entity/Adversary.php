<?php

namespace EL\Core\Entity;

/**
 * Adversary
 */
class Adversary
{
    /**
     * @var integer
     */
    private $id;
    
    private $joueur0;
    
    private $joueur1;

    /**
     * @var \DateTime
     */
    private $dateCreate;

    /**
     * @var \DateTime
     */
    private $dateResponse;

    /**
     * @var integer
     * 0: attente de reponse
     * 1: accepté
     * 2: refusé
     * 3: bloqué
     */
    private $state;


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
     * Set dateCreate
     *
     * @param \DateTime $dateCreate
     * @return Adversary
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
     * Set dateResponse
     *
     * @param \DateTime $dateResponse
     * @return Adversary
     */
    public function setDateResponse($dateResponse)
    {
        $this->dateResponse = $dateResponse;
    
        return $this;
    }

    /**
     * Get dateResponse
     *
     * @return \DateTime
     */
    public function getDateResponse()
    {
        return $this->dateResponse;
    }

    /**
     * Set state
     *
     * @param integer $state
     * @return Adversary
     */
    public function setState($state)
    {
        $this->state = $state;
    
        return $this;
    }

    /**
     * Get state
     *
     * @return integer
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * Set joueur0
     *
     * @param string $joueur0
     * @return Adversary
     */
    public function setJoueur0($joueur0)
    {
        $this->joueur0 = $joueur0;
    
        return $this;
    }

    /**
     * Get joueur0
     *
     * @return string
     */
    public function getJoueur0()
    {
        return $this->joueur0;
    }

    /**
     * Set joueur1
     *
     * @param string $joueur1
     * @return Adversary
     */
    public function setJoueur1($joueur1)
    {
        $this->joueur1 = $joueur1;
    
        return $this;
    }

    /**
     * Get joueur1
     *
     * @return string
     */
    public function getJoueur1()
    {
        return $this->joueur1;
    }
}
