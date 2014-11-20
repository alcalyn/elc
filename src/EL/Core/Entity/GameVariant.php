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
     * 


     */
    private $game;
    
    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     * 


     */
    private $scores;
    
    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     * 


     */
    protected $langs;


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
     * Constructor
     */
    public function __construct()
    {
        $this->langs = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    /**
     * Set game
     *
     * @param \EL\Core\Entity\Game $game
     * @return GameVariant
     */
    public function setGame(\EL\Core\Entity\Game $game)
    {
        $this->game = $game;
    
        return $this;
    }

    /**
     * Get game
     *
     * @return \EL\Core\Entity\Game
     */
    public function getGame()
    {
        return $this->game;
    }

    /**
     * Add langs
     *
     * @param \EL\Core\Entity\GameLang $langs
     * @return GameVariant
     */
    public function addLang(\EL\Core\Entity\GameLang $langs)
    {
        $this->langs[] = $langs;
    
        return $this;
    }

    /**
     * Remove langs
     *
     * @param \EL\Core\Entity\GameLang $langs
     */
    public function removeLang(\EL\Core\Entity\GameLang $langs)
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
     * @param \EL\Core\Entity\Score $scores
     * @return GameVariant
     */
    public function addScore(\EL\Core\Entity\Score $scores)
    {
        $this->scores[] = $scores;
    
        return $this;
    }

    /**
     * Remove scores
     *
     * @param \EL\Core\Entity\Score $scores
     */
    public function removeScore(\EL\Core\Entity\Score $scores)
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
