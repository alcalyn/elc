<?php

namespace EL\Core\Entity;

/**
 * GameVariant
 * 
 * Used for score
 */
class GameVariant extends AbstractLangEntity
{
    /**
     * Default name for default game variants (where no variant are used)
     */
    const DEFAULT_NAME = 'default_variant';
    
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $name;
    
    /**
     * @var Game
     */
    private $game;
    
    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    private $scores;
    
    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    protected $langs;
    
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->langs = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
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
     * Set name
     *
     * @param string $name
     * @return GameVariant
     */
    public function setName($name)
    {
        $this->name = $name;
    
        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
    
    /**
     * Set game
     *
     * @param Game $game
     * @return GameVariant
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
     * Add langs
     *
     * @param GameLang $langs
     * @return GameVariant
     */
    public function addLang(GameLang $langs)
    {
        $this->langs[] = $langs;
    
        return $this;
    }

    /**
     * Remove langs
     *
     * @param GameLang $langs
     */
    public function removeLang(GameLang $langs)
    {
        $this->langs->removeElement($langs);
    }

    /**
     * Get langs
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getLangs()
    {
        return $this->langs;
    }

    /**
     * Add scores
     *
     * @param Score $scores
     * @return GameVariant
     */
    public function addScore(Score $scores)
    {
        $this->scores[] = $scores;
    
        return $this;
    }

    /**
     * Remove scores
     *
     * @param Score $scores
     */
    public function removeScore(Score $scores)
    {
        $this->scores->removeElement($scores);
    }

    /**
     * Get scores
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getScores()
    {
        return $this->scores;
    }
}
