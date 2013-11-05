<?php

namespace EL\ELCoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Adversary
 *
 * @ORM\Table(name="el_core_adversary")
 * @ORM\Entity(repositoryClass="EL\ELCoreBundle\Repository\AdversaryRepository")
 */
class Adversary
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
     * @ORM\ManyToOne(targetEntity="EL\ELCoreBundle\Entity\Player")
     * @ORM\Column(nullable=false)
     */
    private $joueur0;
    
    /**
     * @ORM\ManyToOne(targetEntity="EL\ELCoreBundle\Entity\Player")
     * @ORM\Column(nullable=true)
     */
    private $joueur1;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_create", type="date")
     */
    private $dateCreate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_response", type="date")
     */
    private $dateResponse;

    /**
     * @var integer
     * 0: attente de reponse
     * 1: accepté
     * 2: refusé
     * 3: bloqué
     *
     * @ORM\Column(name="state", type="smallint")
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