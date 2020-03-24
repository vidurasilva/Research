<?php

namespace AppBundle\Entity;

/**
 * UserGoalCharity
 */
class UserGoalCharity
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var integer
     */
    private $percentage;

    /**
     * @var \AppBundle\Entity\UserGoal
     */
    private $userGoal;


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
     * Set percentage
     *
     * @param integer $percentage
     *
     * @return UserGoalCharity
     */
    public function setPercentage($percentage)
    {
        $this->percentage = $percentage;

        return $this;
    }

    /**
     * Get percentage
     *
     * @return integer
     */
    public function getPercentage()
    {
        return $this->percentage;
    }

    /**
     * Set userGoal
     *
     * @param \AppBundle\Entity\UserGoal $userGoal
     *
     * @return UserGoalCharity
     */
    public function setUserGoal(\AppBundle\Entity\UserGoal $userGoal = null)
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
     * @var \AppBundle\Entity\Charity
     */
    private $charity;


    /**
     * Set charity
     *
     * @param \AppBundle\Entity\Charity $charity
     *
     * @return UserGoalCharity
     */
    public function setCharity(\AppBundle\Entity\Charity $charity = null)
    {
        $this->charity = $charity;

        return $this;
    }

    /**
     * Get charity
     *
     * @return \AppBundle\Entity\Charity
     */
    public function getCharity()
    {
        return $this->charity;
    }
}
