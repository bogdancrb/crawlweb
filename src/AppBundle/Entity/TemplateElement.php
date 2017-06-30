<?php

namespace AppBundle\Entity;

/**
 * TemplateElement
 */
class TemplateElement
{
    const IGNORE_ATTRIBUTE_VALUE_YES = 1;
    const IGNORE_ATTRIBUTE_VALUE_NO = 0;

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
    private $cssPath;

    /**
     * @var integer
     */
    private $ignoreAttributeValue = '0';

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $attributes;

    /**
     * @var \AppBundle\Entity\Template
     */
    private $template;

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
     * Set name
     *
     * @param string $name
     *
     * @return TemplateElement
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
     * Set cssPath
     *
     * @param string $cssPath
     *
     * @return TemplateElement
     */
    public function setCssPath($cssPath)
    {
        $this->cssPath = $cssPath;

        return $this;
    }

    /**
     * Get cssPath
     *
     * @return string
     */
    public function getCssPath()
    {
        return $this->cssPath;
    }

    /**
     * Set ignoreAttributeValue
     *
     * @param integer $ignoreAttributeValue
     *
     * @return TemplateElement
     */
    public function setIgnoreAttributeValue($ignoreAttributeValue)
    {
        $this->ignoreAttributeValue = $ignoreAttributeValue;

        return $this;
    }

    /**
     * Get ignoreAttributeValue
     *
     * @return integer
     */
    public function getIgnoreAttributeValue()
    {
        return $this->ignoreAttributeValue;
    }

    /**
     * Add attribute
     *
     * @param \AppBundle\Entity\Attributes $attribute
     *
     * @return TemplateElement
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
     * Set template
     *
     * @param \AppBundle\Entity\Template $template
     *
     * @return TemplateElement
     */
    public function setTemplate(\AppBundle\Entity\Template $template = null)
    {
        $this->template = $template;

        return $this;
    }

    /**
     * Get template
     *
     * @return \AppBundle\Entity\Template
     */
    public function getTemplate()
    {
        return $this->template;
    }
}

