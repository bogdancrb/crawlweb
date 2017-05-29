<?php

namespace AppBundle\Entity;

/**
 * UsersHasRoles
 */
class UsersHasRoles
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var \AppBundle\Entity\Roles
     */
    private $roles;

    /**
     * @var \AppBundle\Entity\Users
     */
    private $users;


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
     * Set roles
     *
     * @param \AppBundle\Entity\Roles $roles
     *
     * @return UsersHasRoles
     */
    public function setRoles(\AppBundle\Entity\Roles $roles = null)
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * Get roles
     *
     * @return \AppBundle\Entity\Roles
     */
    public function getRoles()
    {
        return $this->roles;
    }

    /**
     * Set users
     *
     * @param \AppBundle\Entity\Users $users
     *
     * @return UsersHasRoles
     */
    public function setUsers(\AppBundle\Entity\Users $users = null)
    {
        $this->users = $users;

        return $this;
    }

    /**
     * Get users
     *
     * @return \AppBundle\Entity\Users
     */
    public function getUsers()
    {
        return $this->users;
    }
}

