<?php

namespace AppBundle\Entity;

/**
 * GoalGroup
 */
class GoalGroup
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
     * @var \Doctrine\Common\Collections\Collection
     */
    private $userGoals;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->userGoals = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add userGoal
     *
     * @param \AppBundle\Entity\UserGoal $userGoal
     *
     * @return GoalGroup
     */
    public function addUserGoal(\AppBundle\Entity\UserGoal $userGoal)
    {
        $this->userGoals[] = $userGoal;

        return $this;
    }

    /**
     * Remove userGoal
     *
     * @param \AppBundle\Entity\UserGoal $userGoal
     */
    public function removeUserGoal(\AppBundle\Entity\UserGoal $userGoal)
    {
        $this->userGoals->removeElement($userGoal);
    }

    /**
     * Get userGoals
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getUserGoals()
    {
        return $this->userGoals;
    }
    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $groupUsers;


    /**
     * Add groupUser
     *
     * @param \AppBundle\Entity\GroupUser $groupUser
     *
     * @return GoalGroup
     */
    public function addGroupUser(\AppBundle\Entity\GroupUser $groupUser)
    {
        $this->groupUsers[] = $groupUser;

        return $this;
    }

    /**
     * Remove groupUser
     *
     * @param \AppBundle\Entity\GroupUser $groupUser
     */
    public function removeGroupUser(\AppBundle\Entity\GroupUser $groupUser)
    {
        $this->groupUsers->removeElement($groupUser);
    }

    /**
     * Get groupUsers
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getGroupUsers()
    {
        return $this->groupUsers;
    }
    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $invitations;


    /**
     * Add invitation
     *
     * @param \AppBundle\Entity\GroupInvite $invitation
     *
     * @return GoalGroup
     */
    public function addInvitation(\AppBundle\Entity\GroupInvite $invitation)
    {
        $this->invitations[] = $invitation;

        return $this;
    }

    /**
     * Remove invitation
     *
     * @param \AppBundle\Entity\GroupInvite $invitation
     */
    public function removeInvitation(\AppBundle\Entity\GroupInvite $invitation)
    {
        $this->invitations->removeElement($invitation);
    }

    /**
     * Get invitations
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getInvitations()
    {
        return $this->invitations;
    }
    /**
     * @var string
     */
    private $name;


    /**
     * Set name
     *
     * @param string $name
     *
     * @return GoalGroup
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
     * @var \UserBundle\Entity\User
     */
    private $admin;


    /**
     * Set admin
     *
     * @param \UserBundle\Entity\User $admin
     *
     * @return GoalGroup
     */
    public function setAdmin(\UserBundle\Entity\User $admin = null)
    {
        $this->admin = $admin;

        return $this;
    }

    /**
     * Get admin
     *
     * @return \UserBundle\Entity\User
     */
    public function getAdmin()
    {
        return $this->admin;
    }
    /**
     * @var \AppBundle\Entity\Goal
     */
    private $goal;


    /**
     * Set goal
     *
     * @param \AppBundle\Entity\Goal $goal
     *
     * @return GoalGroup
     */
    public function setGoal(\AppBundle\Entity\Goal $goal = null)
    {
        $this->goal = $goal;

        return $this;
    }

    /**
     * Get goal
     *
     * @return \AppBundle\Entity\Goal
     */
    public function getGoal()
    {
        return $this->goal;
    }
    /**
     * @var integer
     */
    private $points;


    /**
     * Set points
     *
     * @param integer $points
     *
     * @return GoalGroup
     */
    public function setPoints($points)
    {
        $this->points = $points;

        return $this;
    }

    /**
     * Get points
     *
     * @return integer
     */
    public function getPoints()
    {
        return $this->points;
    }
}
