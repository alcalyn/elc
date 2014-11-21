<?php

namespace EL\Core\Entity;

/**
 * GameLang
 */
class GameLang
{
    /**
     * @var integer
     */
    private $id;
    
    /**
     * @var Game
     */
    private $game;
    
    /**
     * @var Lang
     */
    private $lang;

    /**
     * @var string
     */
    private $title;

    /**
     * @var string
     */
    private $slug;

    /**
     * @var string
     */
    private $shortDesc;

    /**
     * @var string
     */
    private $longDesc;
    
    /**
     * Url or ressource of game picture displayed in game home page.
     * Can be "http://..." or "bundles/tictactoe/img/home.jpg"
     * 
     * @var string
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
     * @param Game $game
     * @return GameLang
     */
    public function setGame(Game $game)
    {
        $this->game = $game;
    
        return $this;
    }

    /**
     * Get game
     *
     * @return Game
     */
    public function getGame()
    {
        return $this->game;
    }

    /**
     * Set lang
     *
     * @param Lang $lang
     * @return GameLang
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
