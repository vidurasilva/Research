<?php

namespace AppBundle\Entity;

/**
 * GroupUser
 */
class GroupUser
{
    /**
     * @var int
     */
    private $id;


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }
    /**
     * @var \UserBundle\Entity\User
     */
    private $user;


    /**
     * Set user
     *
     * @param \UserBundle\Entity\User $user
     *
     * @return GroupUser
     */
    public function setUser(\UserBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \UserBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }
    /**
     * @var boolean
     */
    private $enrolled = 0;

    /**
     * @var \AppBundle\Entity\GoalGroup
     */
    private $group;


    /**
     * Set enrolled
     *
     * @param boolean $enrolled
     *
     * @return GroupUser
     */
    public function setEnrolled($enrolled)
    {
        $this->enrolled = $enrolled;

        return $this;
    }

    /**
     * Get enrolled
     *
     * @return boolean
     */
    public function getEnrolled()
    {
        return $this->enrolled;
    }

    /**
     * Set group
     *
     * @param \AppBundle\Entity\GoalGroup $group
     *
     * @return GroupUser
     */
    public function setGroup(\AppBundle\Entity\GoalGroup $group = null)
    {
        $this->group = $group;

        return $this;
    }

    /**
     * Get group
     *
     * @return \AppBundle\Entity\GoalGroup
     */
    public function getGroup()
    {
        return $this->group;
    }
    /**
     * @var \DateTime
     */
    private $created;

    /**
     * @var \DateTime
     */
    private $updated;


    /**
     * Set created
     *
     * @param \DateTime $created
     *
     * @return GroupUser
     */
    public function setCreated($created)
    {
        $this->created = $created;

        return $this;
    }

    /**
     * Get created
     *
     * @return \DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Set updated
     *
     * @param \DateTime $updated
     *
     * @return GroupUser
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;

        return $this;
    }

    /**
     * Get updated
     *
     * @return \DateTime
     */
    public function getUpdated()
    {
        return $this->updated;
    }
    /**
     * @var boolean
     */
    private $rejected = 0;


    /**
     * Set rejected
     *
     * @param boolean $rejected
     *
     * @return GroupUser
     */
    public function setRejected($rejected)
    {
        $this->rejected = $rejected;

        return $this;
    }

    /**
     * Get rejected
     *
     * @return boolean
     */
    public function getRejected()
    {
        return $this->rejected;
    }
}
