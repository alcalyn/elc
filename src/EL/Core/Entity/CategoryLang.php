<?php

namespace EL\Core\Entity;

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
     * @param Category $category
     * @return CategoryLang
     */
    public function setCategory(Category $category)
    {
        $this->category = $category;
    
        return $this;
    }

    /**
     * Get category
     *
     * @return Category
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * Set lang
     *
     * @param Lang $lang
     * @return CategoryLang
     */
    public function setLang(Lang $lang)
    {
        $this->lang = $lang;
    
        return $this;
    }

    /**
     * Get lang
     *
     * @return Lang
     */
    public function getLang()
    {
        return $this->lang;
    }
}
