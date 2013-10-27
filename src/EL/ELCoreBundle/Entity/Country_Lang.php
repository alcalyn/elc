<?php

namespace EL\ELCoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Country_Lang
 *
 * @ORM\Table(name="dev_core_country_lang")
 * @ORM\Entity(repositoryClass="EL\ELCoreBundle\Repositories\Country_LangRepository")
 */
class Country_Lang
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
     * @ORM\ManyToOne(targetEntity="EL\ELCoreBundle\Entity\Country")
     * @ORM\JoinColumn(nullable=false)
     */
    private $country;
    
    /**
     * @ORM\ManyToOne(targetEntity="EL\ELCoreBundle\Entity\Lang")
     * @ORM\JoinColumn(nullable=false)
     */
    private $lang;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=63)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="habitant", type="string", length=63)
     */
    private $habitant;


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
     * Set title
     *
     * @param string $title
     * @return Country_Lang
     */
    public function setTitle($title)
    {
        $this->title = $title;
    
        return $this;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set habitant
     *
     * @param string $habitant
     * @return Country_Lang
     */
    public function setHabitant($habitant)
    {
        $this->habitant = $habitant;
    
        return $this;
    }

    /**
     * Get habitant
     *
     * @return string 
     */
    public function getHabitant()
    {
        return $this->habitant;
    }

    /**
     * Set country
     *
     * @param \EL\ELCoreBundle\Entity\Country $country
     * @return Country_Lang
     */
    public function setCountry(\EL\ELCoreBundle\Entity\Country $country)
    {
        $this->country = $country;
    
        return $this;
    }

    /**
     * Get country
     *
     * @return \EL\ELCoreBundle\Entity\Country 
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * Set lang
     *
     * @param \EL\ELCoreBundle\Entity\Lang $lang
     * @return Country_Lang
     */
    public function setLang(\EL\ELCoreBundle\Entity\Lang $lang)
    {
        $this->lang = $lang;
    
        return $this;
    }

    /**
     * Get lang
     *
     * @return \EL\ELCoreBundle\Entity\Lang 
     */
    public function getLang()
    {
        return $this->lang;
    }
}