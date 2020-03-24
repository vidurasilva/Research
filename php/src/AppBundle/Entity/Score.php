<?php

namespace AppBundle\Entity;

use UserBundle\Entity\User;

/**
 * Score
 */
class Score
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var int
     */
    private $score;

    /**
     * @var User
     */
    private $user;

    /**
     * @var Goal
     */
    private $goal;

    /**
     * @var int
     */
    private $group_goal;

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
     * Set score
     *
     * @param integer $score
     *
     * @return Score
     */
    public function setScore($score)
    {
        $this->score = $score;

        return $this;
    }

    /**
     * Get score
     *
     * @return int
     */
    public function getScore()
    {
        return $this->score;
    }

    /**
     * Set user
     *
     * @param User $user
     *
     * @return Score
     */
    public function setUser($user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param Goal $goal
     *
     * @return Score
     */
    public function setGoal($goal)
    {
        $this->goal = $goal;

        return $this;
    }

    /**
     * @return Goal
     */
    public function getGoal()
    {
        return $this->goal;
    }

    /**
     * @param int $group_goal
     *
     * @return Score
     */
    public function setGroupGoal($group_goal)
    {
        $this->group_goal = $group_goal;

        return $this;
    }

    /**
     * @return int
     */
    public function getGroupGoal()
    {
        return $this->group_goal;
    }
}
