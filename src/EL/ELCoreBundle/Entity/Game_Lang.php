<?php

namespace EL\ELCoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Game_Lang
 *
 * @ORM\Table(name="el_core_game_lang")
 * @ORM\Entity(repositoryClass="EL\ELCoreBundle\Repositories\Game_LangRepository")
 */
class Game_Lang
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
     * @ORM\ManyToOne(targetEntity="EL\ELCoreBundle\Entity\Game")
     * @ORM\JoinColumn(nullable=false)
     */
    private $game;
    
    /**
     * @ORM\ManyToOne(targetEntity="EL\ELCoreBundle\Entity\Lang")
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
     * @return Game_Lang
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
     * @return Game_Lang
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
     * @return Game_Lang
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
     * @return Game_Lang
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
}
