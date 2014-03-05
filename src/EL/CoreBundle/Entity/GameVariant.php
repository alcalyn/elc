<?php

namespace EL\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * GameVariant
 * 
 * Used for score
 *
 * @ORM\Table(name="el_core_gamevariant")
 * @ORM\Entity(repositoryClass="EL\CoreBundle\Repository\GameVariantRepository")
 */
class GameVariant extends AbstractLangEntity
{
    /**
     * Default name for default game variants (where no variant are used)
     */
    const DEFAULT_NAME = 'default_variant';
    
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=31)
     */
    private $name;
    
    /**
     * @var Game
     * 
     * @ORM\ManyToOne(targetEntity="EL\CoreBundle\Entity\Game")
     * @ORM\JoinColumn(nullable=false)
     */
    private $game;
    
    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     * 
     * @ORM\OneToMany(targetEntity="EL\CoreBundle\Entity\GameLang", mappedBy="game")
     * @ORM\JoinColumn(nullable=false)
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
     * @param \EL\CoreBundle\Entity\Game $game
     * @return GameVariant
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
     * Add langs
     *
     * @param \EL\CoreBundle\Entity\GameLang $langs
     * @return GameVariant
     */
    public function addLang(\EL\CoreBundle\Entity\GameLang $langs)
    {
        $this->langs[] = $langs;
    
        return $this;
    }

    /**
     * Remove langs
     *
     * @param \EL\CoreBundle\Entity\GameLang $langs
     */
    public function removeLang(\EL\CoreBundle\Entity\GameLang $langs)
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