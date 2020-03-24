<?php

namespace AppBundle\Entity;

/**
 * SupervisorCheckinTokens
 */
class SupervisorCheckinTokens
{
    const MAIL_FAILED  = 'failed';
    const MAIL_SUCCESS = 'success';
    const MAIL_OPEN    = 'open'; //On init first status

    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $acceptToken;

    /**
     * @var string
     */
    private $declineToken;

    /**
     * @var \DateTime
     */
    private $createdAt;

    /**
     * @var int
     */
    private $status;

    /**
     * @var UserGoal
     */
    private $userGoal;

    /**
     * @var Task
     */
    private $task;

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
     * Set acceptToken
     *
     * @param string $acceptToken
     *
     * @return SupervisorCheckinTokens
     */
    public function setAcceptToken($acceptToken)
    {
        $this->acceptToken = $acceptToken;

        return $this;
    }

    /**
     * Get acceptToken
     *
     * @return string
     */
    public function getAcceptToken()
    {
        return $this->acceptToken;
    }

    /**
     * Set declineToken
     *
     * @param string $declineToken
     *
     * @return SupervisorCheckinTokens
     */
    public function setDeclineToken($declineToken)
    {
        $this->declineToken = $declineToken;

        return $this;
    }

    /**
     * Get declineToken
     *
     * @return string
     */
    public function getDeclineToken()
    {
        return $this->declineToken;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return SupervisorCheckinTokens
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param int $status
     *
     * @return SupervisorCheckinTokens
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @param UserGoal $userGoal
     *
     * @return SupervisorCheckinTokens
     */
    public function setUserGoal($userGoal)
    {
        $this->userGoal = $userGoal;

        return $this;
    }

    /**
     * @return UserGoal
     */
    public function getUserGoal()
    {
        return $this->userGoal;
    }

    /**
     * @param Task $task
     *
     * @return SupervisorCheckinTokens
     */
    public function setTask($task)
    {
        $this->task = $task;

        return $this;
    }

    /**
     * @return Task
     */
    public function getTask()
    {
        return $this->task;
    }

    /**
     * @var string
     */
    private $image;


    /**
     * Set image
     *
     * @param string $image
     *
     * @return SupervisorCheckinTokens
     */
    public function setImage($image)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * Get image
     *
     * @return string
     */
    public function getImage()
    {
        return $this->image;
    }
    /**
     * @var integer
     */
    private $mailAttemps = 0;


    /**
     * Set mailAttemps
     *
     * @param integer $mailAttemps
     *
     * @return SupervisorCheckinTokens
     */
    public function setMailAttemps($mailAttemps)
    {
        $this->mailAttemps = $mailAttemps;

        return $this;
    }

    /**
     * Get mailAttemps
     *
     * @return integer
     */
    public function getMailAttemps()
    {
        return $this->mailAttemps;
    }
}
