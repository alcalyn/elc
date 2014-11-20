<?php

namespace EL\Core\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CategorieLang
 */
class CategoryLang
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $title;
    
    private $category;
    
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
     * @param \EL\Core\Entity\Category $category
     * @return CategoryLang
     */
    public function setCategory(\EL\Core\Entity\Category $category)
    {
        $this->category = $category;
    
        return $this;
    }

    /**
     * Get category
     *
     * @return \EL\Core\Entity\Category
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * Set lang
     *
     * @param \EL\Core\Entity\Lang $lang
     * @return CategoryLang
     */
    public function setLang(\EL\Core\Entity\Lang $lang)
    {
        $this->lang = $lang;
    
        return $this;
    }

    /**
     * Get lang
     *
     * @return \EL\Core\Entity\Lang
     */
    public function getLang()
    {
        return $this->lang;
    }
}
