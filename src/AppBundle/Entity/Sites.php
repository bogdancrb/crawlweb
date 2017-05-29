<?php

namespace AppBundle\Entity;

/**
 * Sites
 */
class Sites
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $mainUrl;

    /**
     * @var \AppBundle\Entity\Category
     */
    private $category;


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
     *
     * @return Sites
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
     * Set mainUrl
     *
     * @param string $mainUrl
     *
     * @return Sites
     */
    public function setMainUrl($mainUrl)
    {
        $this->mainUrl = $mainUrl;

        return $this;
    }

    /**
     * Get mainUrl
     *
     * @return string
     */
    public function getMainUrl()
    {
        return $this->mainUrl;
    }

    /**
     * Set category
     *
     * @param \AppBundle\Entity\Category $category
     *
     * @return Sites
     */
    public function setCategory(\AppBundle\Entity\Category $category = null)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * Get category
     *
     * @return \AppBundle\Entity\Category
     */
    public function getCategory()
    {
        return $this->category;
    }
}

