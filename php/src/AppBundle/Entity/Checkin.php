<?php

namespace AppBundle\Entity;

/**
 * Checkin
 */
class Checkin
{
    const CHECKIN_DONE   = 'done';
    const CHECKIN_FAILED = 'failed';

    public function __construct(UserGoal $userGoal, Task $task, $approved = true)
    {
        $this->userGoal = $userGoal;
        $this->task     = $task;
        $this->approved = $approved;
    }

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
     * @var \AppBundle\Entity\Task
     */
    private $task;


    /**
     * Set task
     *
     * @param \AppBundle\Entity\Task $task
     *
     * @return Checkin
     */
    public function setTask(\AppBundle\Entity\Task $task = NULL)
    {
        $this->task = $task;

        return $this;
    }

    /**
     * Get task
     *
     * @return \AppBundle\Entity\Task
     */
    public function getTask()
    {
        return $this->task;
    }

    /**
     * @var UserGoal
     */
    private $userGoal;


    /**
     * Set userGoal
     *
     * @param \AppBundle\Entity\UserGoal $userGoal
     *
     * @return Checkin
     */
    public function setUserGoal(\AppBundle\Entity\UserGoal $userGoal = NULL)
    {
        $this->userGoal = $userGoal;

        return $this;
    }

    /**
     * Get userGoal
     *
     * @return \AppBundle\Entity\UserGoal
     */
    public function getUserGoal()
    {
        return $this->userGoal;
    }

    /**
     * @var \DateTime
     */
    private $created;


    /**
     * Set created
     *
     * @param \DateTime $created
     *
     * @return Checkin
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
     * @var boolean
     */
    private $approved = 1;


    /**
     * Set approved
     *
     * @param boolean $approved
     *
     * @return Checkin
     */
    public function setApproved($approved)
    {
        $this->approved = $approved;

        return $this;
    }

    /**
     * Get approved
     *
     * @return boolean
     */
    public function getApproved()
    {
        return $this->approved;
    }

    /**
     * @var string
     */
    private $status = 'done';


    /**
     * Set status
     *
     * @param string $status
     *
     * @return Checkin
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }
}
