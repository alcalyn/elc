<?php

namespace EL\Core\Entity;

/**
 * Category
 */
class Category extends AbstractLangEntity
{
    /**
     * @var integer
     */
    private $id;
    
    private $games;
    
    protected $langs;

    /**
     * @var string
     */
    private $name;


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
     * @return Category
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
        $this->games = new \Doctrine\Common\Collections\ArrayCollection();
        $this->langs = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    /**
     * Add games
     *
     * @param Game $games
     * @return Category
     */
    public function addGame(Game $games)
    {
        $this->games[] = $games;
    
        return $this;
    }

    /**
     * Remove games
     *
     * @param Game $games
     */
    public function removeGame(Game $games)
    {
        $this->games->removeElement($games);
    }

    /**
     * Get games
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getGames()
    {
        return $this->games;
    }

    /**
     * Add langs
     *
     * @param CategoryLang $langs
     * @return Category
     */
    public function addLang(CategoryLang $langs)
    {
        $this->langs[] = $langs;
    
        return $this;
    }

    /**
     * Remove langs
     *
     * @param CategoryLang $langs
     */
    public function removeLang(CategoryLang $langs)
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
}
