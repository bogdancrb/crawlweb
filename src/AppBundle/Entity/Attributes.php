<?php

namespace AppBundle\Entity;

/**
 * Attributes
 */
class Attributes
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $value;

    /**
     * @var \AppBundle\Entity\Content
     */
    private $content;

    /**
     * @var \AppBundle\Entity\TemplateElement
     */
    private $templateElement;


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
     * Set value
     *
     * @param string $value
     *
     * @return Attributes
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Get value
     *
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set content
     *
     * @param \AppBundle\Entity\Content $content
     *
     * @return Attributes
     */
    public function setContent(\AppBundle\Entity\Content $content = null)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content
     *
     * @return \AppBundle\Entity\Content
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set templateElement
     *
     * @param \AppBundle\Entity\TemplateElement $templateElement
     *
     * @return Attributes
     */
    public function setTemplateElement(\AppBundle\Entity\TemplateElement $templateElement = null)
    {
        $this->templateElement = $templateElement;

        return $this;
    }

    /**
     * Get templateElement
     *
     * @return \AppBundle\Entity\TemplateElement
     */
    public function getTemplateElement()
    {
        return $this->templateElement;
    }
}

