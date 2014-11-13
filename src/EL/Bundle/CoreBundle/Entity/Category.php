<?php

namespace EL\Bundle\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Category
 *
 * @ORM\Table(name="el_core_category")
 * @ORM\Entity(repositoryClass="EL\Bundle\CoreBundle\Repository\CategoryRepository")
 */
class Category extends AbstractLangEntity
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
     * @ORM\OneToMany(targetEntity="EL\Bundle\CoreBundle\Entity\Game", mappedBy="category")
     */
    private $games;
    
    /**
     * @ORM\OneToMany(targetEntity="EL\Bundle\CoreBundle\Entity\CategoryLang", mappedBy="category")
     */
    protected $langs;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=31)
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
     * @param \EL\Bundle\CoreBundle\Entity\Game $games
     * @return Category
     */
    public function addGame(\EL\Bundle\CoreBundle\Entity\Game $games)
    {
        $this->games[] = $games;
    
        return $this;
    }

    /**
     * Remove games
     *
     * @param \EL\Bundle\CoreBundle\Entity\Game $games
     */
    public function removeGame(\EL\Bundle\CoreBundle\Entity\Game $games)
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
     * @param \EL\Bundle\CoreBundle\Entity\CategoryLang $langs
     * @return Category
     */
    public function addLang(\EL\Bundle\CoreBundle\Entity\CategoryLang $langs)
    {
        $this->langs[] = $langs;
    
        return $this;
    }

    /**
     * Remove langs
     *
     * @param \EL\Bundle\CoreBundle\Entity\CategoryLang $langs
     */
    public function removeLang(\EL\Bundle\CoreBundle\Entity\CategoryLang $langs)
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
