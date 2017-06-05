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
     * @var \Doctrine\Common\Collections\Collection
     */
    private $content;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $template;

    /**
     * @var \AppBundle\Entity\Category
     */
    private $category;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->content = new \Doctrine\Common\Collections\ArrayCollection();
        $this->template = new \Doctrine\Common\Collections\ArrayCollection();
    }

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
     * Add content
     *
     * @param \AppBundle\Entity\Content $content
     *
     * @return Sites
     */
    public function addContent(\AppBundle\Entity\Content $content)
    {
        $this->content[] = $content;

        return $this;
    }

    /**
     * Remove content
     *
     * @param \AppBundle\Entity\Content $content
     */
    public function removeContent(\AppBundle\Entity\Content $content)
    {
        $this->content->removeElement($content);
    }

    /**
     * Get content
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Add template
     *
     * @param \AppBundle\Entity\Template $template
     *
     * @return Sites
     */
    public function addTemplate(\AppBundle\Entity\Template $template)
    {
        $this->template[] = $template;

        return $this;
    }

    /**
     * Remove template
     *
     * @param \AppBundle\Entity\Template $template
     */
    public function removeTemplate(\AppBundle\Entity\Template $template)
    {
        $this->template->removeElement($template);
    }

    /**
     * Get template
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTemplate()
    {
        return $this->template;
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

