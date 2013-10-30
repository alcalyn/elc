<?php

namespace EL\ELCoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Categorie_Lang
 *
 * @ORM\Table(name="el_core_category_lang")
 * @ORM\Entity(repositoryClass="EL\ELCoreBundle\Repositories\Categorie_LangRepository")
 */
class Categorie_Lang
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
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=31)
     */
    private $title;
    
    /**
     * @ORM\ManyToOne(targetEntity="EL\ELCoreBundle\Entity\Category, inversedBy="langs")
     * @ORM\JoinColumn(nullable=false)
     */
    private $category;
    
    /**
     * @ORM\ManyToOne(targetEntity="EL\ELCoreBundle\Entity\Lang")
     * @ORM\JoinColumn(nullable=false)
     */
    private $lang;


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
     * @return Categorie_Lang
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
     * Set category
     *
     * @param \EL\ELCoreBundle\Entity\Category $category
     * @return Categorie_Lang
     */
    public function setCategory(\EL\ELCoreBundle\Entity\Category $category)
    {
        $this->category = $category;
    
        return $this;
    }

    /**
     * Get category
     *
     * @return \EL\ELCoreBundle\Entity\Category 
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * Set lang
     *
     * @param \EL\ELCoreBundle\Entity\Lang $lang
     * @return Categorie_Lang
     */
    public function setLang(\EL\ELCoreBundle\Entity\Lang $lang)
    {
        $this->lang = $lang;
    
        return $this;
    }

    /**
     * Get lang
     *
     * @return \EL\ELCoreBundle\Entity\Lang 
     */
    public function getLang()
    {
        return $this->lang;
    }
}