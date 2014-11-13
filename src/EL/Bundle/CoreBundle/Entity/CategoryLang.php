<?php

namespace EL\Bundle\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CategorieLang
 *
 * @ORM\Table(name="el_core_category_lang")
 * @ORM\Entity(repositoryClass="EL\Bundle\CoreBundle\Repository\CategoryLangRepository")
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
     * @ORM\ManyToOne(targetEntity="EL\Bundle\CoreBundle\Entity\Category", inversedBy="langs")
     * @ORM\JoinColumn(nullable=false)
     */
    private $category;
    
    /**
     * @ORM\ManyToOne(targetEntity="EL\Bundle\CoreBundle\Entity\Lang")
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
     * @return CategoryLang
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
     * @param \EL\Bundle\CoreBundle\Entity\Category $category
     * @return CategoryLang
     */
    public function setCategory(\EL\Bundle\CoreBundle\Entity\Category $category)
    {
        $this->category = $category;
    
        return $this;
    }

    /**
     * Get category
     *
     * @return \EL\Bundle\CoreBundle\Entity\Category 
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * Set lang
     *
     * @param \EL\Bundle\CoreBundle\Entity\Lang $lang
     * @return CategoryLang
     */
    public function setLang(\EL\Bundle\CoreBundle\Entity\Lang $lang)
    {
        $this->lang = $lang;
    
        return $this;
    }

    /**
     * Get lang
     *
     * @return \EL\Bundle\CoreBundle\Entity\Lang 
     */
    public function getLang()
    {
        return $this->lang;
    }
}
