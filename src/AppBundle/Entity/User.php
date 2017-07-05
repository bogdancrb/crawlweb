<?php

namespace AppBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Avanzu\AdminThemeBundle\Model\UserInterface as ThemeUser;

/**
 * User
 */
class User extends BaseUser implements ThemeUser
{
    /**
     * @var integer
     */
    protected $id;

    /**
     * @var string
     */
    private $apiToken;

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->administrator = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set username
     *
     * @param string $username
     *
     * @return User
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Get username
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return User
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set password
     *
     * @param string $password
     *
     * @return User
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get password
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    public function getAvatar()
    {
        return 'https://scontent.ftsr1-1.fna.fbcdn.net/v/t1.0-9/10801975_352270314954443_79460598002570761_n.jpg?oh=f2b6c1d98266db889991ccbb7988b0f8&oe=59CC9281';
    }

    public function getName()
    {
        return 'Bogdan Corbeanu';
    }

    public function getMemberSince()
    {
        return '04.03.2017';
    }

    public function isOnline()
    {
        return true;
    }

    public function getIdentifier()
    {
        return $this->getId();
    }

    public function getTitle()
    {
        return 'System Admin';
    }

    /**
     * @param string $apiToken
     * @return User
     */
    public function setApiToken($apiToken)
    {
        $this->apiToken = $apiToken;
        
        return $this;
    }

    /**
     * @return string
     */
    public function getApiToken()
    {
        return $this->apiToken;
    }
}

