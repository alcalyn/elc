<?php

namespace EL\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CountryLang
 *
 * @ORM\Table(name="el_core_country_lang")
 * @ORM\Entity(repositoryClass="EL\CoreBundle\Repository\CountryLangRepository")
 */
class CountryLang
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
     * @ORM\ManyToOne(targetEntity="EL\CoreBundle\Entity\Country")
     * @ORM\JoinColumn(nullable=false)
     */
    private $country;
    
    /**
     * @ORM\ManyToOne(targetEntity="EL\CoreBundle\Entity\Lang")
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
     * @return CountryLang
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
     * @return CountryLang
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
     * @param \EL\CoreBundle\Entity\Country $country
     * @return CountryLang
     */
    public function setCountry(\EL\CoreBundle\Entity\Country $country)
    {
        $this->country = $country;
    
        return $this;
    }

    /**
     * Get country
     *
     * @return \EL\CoreBundle\Entity\Country 
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * Set lang
     *
     * @param \EL\CoreBundle\Entity\Lang $lang
     * @return CountryLang
     */
    public function setLang(\EL\CoreBundle\Entity\Lang $lang)
    {
        $this->lang = $lang;
    
        return $this;
    }

    /**
     * Get lang
     *
     * @return \EL\CoreBundle\Entity\Lang 
     */
    public function getLang()
    {
        return $this->lang;
    }
}
