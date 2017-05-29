<?php

namespace AppBundle\Entity;

/**
 * TemplateElement
 */
class TemplateElement
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
    private $xpath;

    /**
     * @var \AppBundle\Entity\Template
     */
    private $template;


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
     * Set xpath
     *
     * @param string $xpath
     *
     * @return TemplateElement
     */
    public function setXpath($xpath)
    {
        $this->xpath = $xpath;

        return $this;
    }

    /**
     * Get xpath
     *
     * @return string
     */
    public function getXpath()
    {
        return $this->xpath;
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

