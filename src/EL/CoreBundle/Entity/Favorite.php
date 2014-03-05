<?php

namespace EL\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Favorite
 *
 * @ORM\Table(name="el_core_favorite")
 * @ORM\Entity(repositoryClass="EL\CoreBundle\Repository\FavoriteRepository")
 */
class Favorite
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
     * @ORM\ManyToOne(targetEntity="EL\CoreBundle\Entity\Player")
     * @ORM\Column(nullable=false)
     */
    private $joueur;
    
    /**
     * @ORM\ManyToOne(targetEntity="EL\CoreBundle\Entity\Game")
     * @ORM\Column(nullable=false)
     */
    private $game;

    /**
     * @var integer
     *
     * @ORM\Column(name="note", type="smallint")
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
