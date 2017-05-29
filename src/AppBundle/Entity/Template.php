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
     * @var \AppBundle\Entity\Sites
     */
    private $sites;


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

