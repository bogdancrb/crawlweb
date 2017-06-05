<?php

namespace AppBundle\Entity;

/**
 * Content
 */
class Content
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $url;

    /**
     * @var \DateTime
     */
    private $lastAccessed;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $attributes;

    /**
     * @var \AppBundle\Entity\Sites
     */
    private $sites;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->attributes = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set url
     *
     * @param string $url
     *
     * @return Content
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Get url
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set lastAccessed
     *
     * @param \DateTime $lastAccessed
     *
     * @return Content
     */
    public function setLastAccessed($lastAccessed)
    {
        $this->lastAccessed = $lastAccessed;

        return $this;
    }

    /**
     * Get lastAccessed
     *
     * @return \DateTime
     */
    public function getLastAccessed()
    {
        return $this->lastAccessed;
    }

    /**
     * Add attribute
     *
     * @param \AppBundle\Entity\Attributes $attribute
     *
     * @return Content
     */
    public function addAttribute(\AppBundle\Entity\Attributes $attribute)
    {
        $this->attributes[] = $attribute;

        return $this;
    }

    /**
     * Remove attribute
     *
     * @param \AppBundle\Entity\Attributes $attribute
     */
    public function removeAttribute(\AppBundle\Entity\Attributes $attribute)
    {
        $this->attributes->removeElement($attribute);
    }

    /**
     * Get attributes
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * Set sites
     *
     * @param \AppBundle\Entity\Sites $sites
     *
     * @return Content
     */
    public function setSites(\AppBundle\Entity\Sites $sites = null)
    {
        $this->sites = $sites;

        return $this;
    }

    /**
     * Get sites
     *
     * @return \AppBundle\Entity\Sites
     */
    public function getSites()
    {
        return $this->sites;
    }
}

