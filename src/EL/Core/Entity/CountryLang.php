<?php

namespace EL\Core\Entity;

/**
 * CountryLang
 */
class CountryLang
{
    /**
     * @var integer
     */
    private $id;
    
    private $country;
    
    private $lang;

    /**
     * @var string
     */
    private $title;

    /**
     * @var string
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
     * @param Country $country
     * @return CountryLang
     */
    public function setCountry(Country $country)
    {
        $this->country = $country;
    
        return $this;
    }

    /**
     * Get country
     *
     * @return Country
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * Set lang
     *
     * @param Lang $lang
     * @return CountryLang
     */
    public function setLang(Lang $lang)
    {
        $this->lang = $lang;
    
        return $this;
    }

    /**
     * Get lang
     *
     * @return Lang
     */
    public function getLang()
    {
        return $this->lang;
    }
}
