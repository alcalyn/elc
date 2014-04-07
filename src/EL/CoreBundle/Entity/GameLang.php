<?php

namespace EL\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * GameLang
 *
 * @ORM\Table(name="el_core_game_lang")
 * @ORM\Entity(repositoryClass="EL\CoreBundle\Repository\GameLangRepository")
 */
class GameLang
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
     * @var Game
     * 
     * @ORM\ManyToOne(targetEntity="EL\CoreBundle\Entity\Game", inversedBy="langs")
     * @ORM\JoinColumn(nullable=false)
     */
    private $game;
    
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
     * @ORM\Column(name="title", type="string", length=31)
     */
    private $title;

    /**
     * @var string
     * 
     * @ORM\Column(name="slug", type="string", length=31)
     */
    private $slug;

    /**
     * @var string
     *
     * @ORM\Column(name="short_desc", type="string", length=255)
     */
    private $shortDesc;

    /**
     * @var string
     *
     * @ORM\Column(name="long_desc", type="string", length=1023)
     */
    private $longDesc;
    
    /**
     * Url or ressource of game picture displayed in game home page.
     * Can be "http://..." or "bundles/tictactoe/img/home.jpg"
     * 
     * @var string
     * 
     * @ORM\Column(name="picture_home", type="string", length=255, nullable=true)
     */
    private $pictureHome;
    
    
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
     * @return GameLang
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
     * Set slug
     *
     * @param string $slug
     * @return GameLang
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;
    
        return $this;
    }

    /**
     * Get slug
     *
     * @return string 
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Set shortDesc
     *
     * @param string $shortDesc
     * @return GameLang
     */
    public function setShortDesc($shortDesc)
    {
        $this->shortDesc = $shortDesc;
    
        return $this;
    }

    /**
     * Get shortDesc
     *
     * @return string 
     */
    public function getShortDesc()
    {
        return $this->shortDesc;
    }

    /**
     * Set longDesc
     *
     * @param string $longDesc
     * @return GameLang
     */
    public function setLongDesc($longDesc)
    {
        $this->longDesc = $longDesc;
    
        return $this;
    }

    /**
     * Get longDesc
     *
     * @return string 
     */
    public function getLongDesc()
    {
        return $this->longDesc;
    }

    /**
     * Set game
     *
     * @param \EL\CoreBundle\Entity\Game $game
     * @return GameLang
     */
    public function setGame(\EL\CoreBundle\Entity\Game $game)
    {
        $this->game = $game;
    
        return $this;
    }

    /**
     * Get game
     *
     * @return \EL\CoreBundle\Entity\Game 
     */
    public function getGame()
    {
        return $this->game;
    }

    /**
     * Set lang
     *
     * @param \EL\CoreBundle\Entity\Lang $lang
     * @return GameLang
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

    /**
     * Set pictureHome
     *
     * @param string $pictureHome
     * @return GameLang
     */
    public function setPictureHome($pictureHome)
    {
        $this->pictureHome = $pictureHome;
    
        return $this;
    }

    /**
     * Get pictureHome
     *
     * @return string 
     */
    public function getPictureHome()
    {
        return $this->pictureHome;
    }
}
