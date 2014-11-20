<?php

namespace EL\Core\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Favorite
 */
class Favorite
{
    /**
     * @var integer
     */
    private $id;
    
    private $joueur;
    
    private $game;

    /**
     * @var integer
     */
    private $note;
    
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
     * Set note
     *
     * @param integer $note
     * @return Favorite
     */
    public function setNote($note)
    {
        $this->note = $note;
    
        return $this;
    }

    /**
     * Get note
     *
     * @return integer
     */
    public function getNote()
    {
        return $this->note;
    }

    /**
     * Set joueur
     *
     * @param string $joueur
     * @return Favorite
     */
    public function setJoueur($joueur)
    {
        $this->joueur = $joueur;
    
        return $this;
    }

    /**
     * Get joueur
     *
     * @return string
     */
    public function getJoueur()
    {
        return $this->joueur;
    }

    /**
     * Set game
     *
     * @param string $game
     * @return Favorite
     */
    public function setGame($game)
    {
        $this->game = $game;
    
        return $this;
    }

    /**
     * Get game
     *
     * @return string
     */
    public function getGame()
    {
        return $this->game;
    }
}
