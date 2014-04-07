<?php

namespace EL\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * GameVariantLang
 *
 * @ORM\Table(name="el_core_game_variant_lang")
 * @ORM\Entity(repositoryClass="EL\CoreBundle\Repository\GameVariantLangRepository")
 */
class GameVariantLang
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
     * @var GameVariant
     * 
     * @ORM\ManyToOne(targetEntity="EL\CoreBundle\Entity\GameVariant", inversedBy="langs")
     * @ORM\JoinColumn(nullable=false)
     */
    private $gameVariant;
    
    /**
     * @var Lang
     * 
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
     * @param \EL\CoreBundle\Entity\GameVariant $gameVariant
     * @return GameVariantLang
     */
    public function setGameVariant(\EL\CoreBundle\Entity\GameVariant $gameVariant)
    {
        $this->gameVariant = $gameVariant;
    
        return $this;
    }

    /**
     * Get gameVariant
     *
     * @return \EL\CoreBundle\Entity\GameVariant 
     */
    public function getGameVariant()
    {
        return $this->gameVariant;
    }

    /**
     * Set lang
     *
     * @param \EL\CoreBundle\Entity\Lang $lang
     * @return GameVariantLang
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
