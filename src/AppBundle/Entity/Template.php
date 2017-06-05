<?php

namespace AppBundle\Entity;

/**
 * Template
 */
class Template
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
     * @var \Doctrine\Common\Collections\Collection
     */
    private $templateElement;

    /**
     * @var \AppBundle\Entity\Sites
     */
    private $sites;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->templateElement = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @return Template
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
     * Add templateElement
     *
     * @param \AppBundle\Entity\TemplateElement $templateElement
     *
     * @return Template
     */
    public function addTemplateElement(\AppBundle\Entity\TemplateElement $templateElement)
    {
        $this->templateElement[] = $templateElement;

        return $this;
    }

    /**
     * Remove templateElement
     *
     * @param \AppBundle\Entity\TemplateElement $templateElement
     */
    public function removeTemplateElement(\AppBundle\Entity\TemplateElement $templateElement)
    {
        $this->templateElement->removeElement($templateElement);
    }

    /**
     * Get templateElement
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTemplateElement()
    {
        return $this->templateElement;
    }

    /**
     * Set sites
     *
     * @param \AppBundle\Entity\Sites $sites
     *
     * @return Template
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

