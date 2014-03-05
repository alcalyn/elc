<?php

namespace EL\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CategorieLang
 *
 * @ORM\Table(name="el_core_category_lang")
 * @ORM\Entity(repositoryClass="EL\CoreBundle\Repository\CategoryLangRepository")
 */
class CategoryLang
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
     * @ORM\ManyToOne(targetEntity="EL\CoreBundle\Entity\Category", inversedBy="langs")
     * @ORM\JoinColumn(nullable=false)
     */
    private $category;
    
    /**
     * @ORM\ManyToOne(targetEntity="EL\CoreBundle\Entity\Lang")
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
     * @return CategorieLang
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
     * @param \EL\CoreBundle\Entity\Category $category
     * @return CategorieLang
     */
    public function setCategory(\EL\CoreBundle\Entity\Category $category)
    {
        $this->category = $category;
    
        return $this;
    }

    /**
     * Get category
     *
     * @return \EL\CoreBundle\Entity\Category 
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * Set lang
     *
     * @param \EL\CoreBundle\Entity\Lang $lang
     * @return CategorieLang
     */
    public function setLang(\EL\CoreBundle\Entity\Lang $lang)
    {
        $this->lang = $lang;
    
        return $this;
    }

    /**
     * Get lang
     *
     * @return \EL\CoreBundle\Entity\Lang 
     */
    public function getLang()
    {
        return $this->lang;
    }
}
