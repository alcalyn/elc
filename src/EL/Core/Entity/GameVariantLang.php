<?php

namespace EL\Core\Entity;

use Doctrine\ORM\Mapping as ORM;

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
     * 


     */
    private $gameVariant;
    
    /**
     * @var Lang
     * 


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
     * @param \EL\Core\Entity\GameVariant $gameVariant
     * @return GameVariantLang
     */
    public function setGameVariant(\EL\Core\Entity\GameVariant $gameVariant)
    {
        $this->gameVariant = $gameVariant;
    
        return $this;
    }

    /**
     * Get gameVariant
     *
     * @return \EL\Core\Entity\GameVariant
     */
    public function getGameVariant()
    {
        return $this->gameVariant;
    }

    /**
     * Set lang
     *
     * @param \EL\Core\Entity\Lang $lang
     * @return GameVariantLang
     */
    public function setLang(\EL\Core\Entity\Lang $lang)
    {
        $this->lang = $lang;
    
        return $this;
    }

    /**
     * Get lang
     *
     * @return \EL\Core\Entity\Lang
     */
    public function getLang()
    {
        return $this->lang;
    }
}
