<?php

namespace EL\Core\Entity;

/**
 * GameVariantLang
 */
class GameVariantLang
{
    /**
     * @var integer
     */
    private $id;
    
    /**
     * @var GameVariant
     */
    private $gameVariant;
    
    /**
     * @var Lang
     */
    private $lang;

    /**
     * @var string
     */
    private $title;


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
     * @return GameVariantLang
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
     * Set gameVariant
     *
     * @param GameVariant $gameVariant
     * @return GameVariantLang
     */
    public function setGameVariant(GameVariant $gameVariant)
    {
        $this->gameVariant = $gameVariant;
    
        return $this;
    }

    /**
     * Get gameVariant
     *
     * @return GameVariant
     */
    public function getGameVariant()
    {
        return $this->gameVariant;
    }

    /**
     * Set lang
     *
     * @param Lang $lang
     * @return GameVariantLang
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
