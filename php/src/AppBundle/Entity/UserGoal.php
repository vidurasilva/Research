<?php

namespace AppBundle\Entity;

use AppBundle\Entity\Traits\CrudTrait;

/**
 * UserGoal
 */
class UserGoal
{

    const USER_DAILY_CHECKIN_LIMIT = 1;

    const STATUS_FAILED = 'failed';

    const STATUS_COMPLETED = 'completed';

    const DEFAULT_STATUS = 'open';

    /**
     * @var integer
     */
    protected $id;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $goalMilestones;

    /**
     * @var \UserBundle\Entity\User
     */
    private $user;

    /**
     * @var \AppBundle\Entity\Goal
     */
    private $goal;

    /**
     * @var bool
     */
    private $show_in_dashboard = 1;

    /**
     * @var GoalGroup
     */
    private $group;

    /**
     * @var \DateTime
     */
    private $created;

    /**
     * @var \DateTime
     */
    private $updated;

    /**
     * @var \DateTime
     */
    private $startDate;

    /**
     * @var \DateTime
     */
    private $endDate;

    /**
     * @var string
     */
    private $currency;

    /**
     * @var string
     */
    private $stakeAmount;

    /**
     * @var boolean
     */
    private $globalStake = 0;

    /**
     * @var integer
     */
    private $maximumFails;

    /**
     * @var string
     */
    private $superVisor;

    /**
     * @var boolean
     */
    private $requiresCheckinImage;

    /**
     * @var boolean
     */
    private $active = 0;

    /**
     * @var boolean
     */
    private $finished = 0;

    /**
     * @var \AppBundle\Entity\PaymentCharge
     */
    private $paymentCharge;

    /**
     * @var string
     */
    private $paymentToken;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $userGoalCharities;

    /**
     * @var string
     */
    private $status = self::DEFAULT_STATUS;

    /**
     * @var \DateTime
     */
    private $deadline;

    /**
     * @var integer
     */
    private $transactionAttempts = 0;

    /**
     * @var boolean
     */
    protected $supervisorNotified = false;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->goalMilestones = new \Doctrine\Common\Collections\ArrayCollection();
        $this->userGoalCharities = new \Doctrine\Common\Collections\ArrayCollection();
        $this->userGoalImages = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * Add goalMilestone
     *
     * @param \AppBundle\Entity\Milestone $goalMilestone
     *
     * @return \AppBundle\Entity\UserGoal
     */
    public function addGoalMilestone(Milestone $goalMilestone)
    {
        $this->goalMilestones[] = $goalMilestone;

        return $this;
    }

    /**
     * Remove goalMilestone
     *
     * @param \AppBundle\Entity\Milestone $goalMilestone
     */
    public function removeGoalMilestone(Milestone $goalMilestone)
    {
        $this->goalMilestones->removeElement($goalMilestone);
    }

    /**
     * Get goalMilestones
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getGoalMilestones()
    {
        return $this->goalMilestones;
    }

    /**
     * Set user
     *
     * @param \UserBundle\Entity\User $user
     *
     * @return UserGoal
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
     * Set goal
     *
     * @param \AppBundle\Entity\Goal $goal
     *
     * @return UserGoal
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

    public function getGoalTitle()
    {
        if ($this->goal instanceof Goal) {
            return $this->goal->getTitle();
        }

        return '';
    }

    /**
     * Set group
     *
     * @param GoalGroup $group
     *
     * @return UserGoal
     */
    public function setGroup(GoalGroup $group = null)
    {
        $this->group = $group;

        return $this;
    }

    /**
     * Get group
     *
     * @return GoalGroup
     */
    public function getGroup()
    {
        return $this->group;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     *
     * @return UserGoal
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
     * @return UserGoal
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
     * Set startDate
     *
     * @param \DateTime $startDate
     *
     * @return UserGoal
     */
    public function setStartDate($startDate)
    {
        $this->startDate = $startDate;

        return $this;
    }

    /**
     * Get startDate
     *
     * @return \DateTime
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * Set endDate
     *
     * @param \DateTime $endDate
     *
     * @return UserGoal
     */
    public function setEndDate($endDate)
    {
        $this->endDate = $endDate;

        if ($endDate instanceof \DateTime) {
            $this->endDate->setTime(23, 59, 59);
        }

        return $this;
    }

    /**
     * Get endDate
     *
     * @return \DateTime
     */
    public function getEndDate()
    {
        return $this->endDate;
    }

    /**
     * Set currency
     *
     * @param string $currency
     *
     * @return UserGoal
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;

        return $this;
    }

    /**
     * Get currency
     *
     * @return string
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * Set stakeAmount
     *
     * @param string $stakeAmount
     *
     * @return UserGoal
     */
    public function setStakeAmount($stakeAmount)
    {
        $this->stakeAmount = $stakeAmount;

        return $this;
    }

    /**
     * Get stakeAmount
     *
     * @return string
     */
    public function getStakeAmount()
    {
        return $this->stakeAmount;
    }

    /**
     * Set globalStake
     *
     * @param boolean $globalStake
     *
     * @return UserGoal
     */
    public function setGlobalStake($globalStake)
    {
        $this->globalStake = $globalStake;

        return $this;
    }

    /**
     * Get globalStake
     *
     * @return boolean
     */
    public function getGlobalStake()
    {
        return $this->globalStake;
    }

    /**
     * Set maximumFails
     *
     * @param integer $maximumFails
     *
     * @return UserGoal
     */
    public function setMaximumFails($maximumFails)
    {
        $this->maximumFails = $maximumFails;

        return $this;
    }

    /**
     * Get maximumFails
     *
     * @return integer
     */
    public function getMaximumFails()
    {
        return $this->maximumFails;
    }

    /**
     * Set superVisor
     *
     * @param string $superVisor
     *
     * @return UserGoal
     */
    public function setSuperVisor($superVisor)
    {
        $this->superVisor = $superVisor;

        return $this;
    }

    /**
     * Get superVisor
     *
     * @return string
     */
    public function getSuperVisor()
    {
        return $this->superVisor;
    }

    /**
     * @return boolean
     */
    public function requiresCheckinImage()
    {
        return $this->requiresCheckinImage;
    }

    /**
     * @param boolean $requiresCheckinImage
     */
    public function setRequiresCheckinImage($requiresCheckinImage)
    {
        $this->requiresCheckinImage = $requiresCheckinImage;
    }

    /**
     * Add userGoalCharity
     *
     * @param \AppBundle\Entity\UserGoalCharity $userGoalCharity
     *
     * @return UserGoal
     */
    public function addUserGoalCharity(
      \AppBundle\Entity\UserGoalCharity $userGoalCharity
    ) {
        $this->userGoalCharities[] = $userGoalCharity;

        return $this;
    }

    /**
     * Remove userGoalCharity
     *
     * @param \AppBundle\Entity\UserGoalCharity $userGoalCharity
     */
    public function removeUserGoalCharity(
      \AppBundle\Entity\UserGoalCharity $userGoalCharity
    ) {
        $this->userGoalCharities->removeElement($userGoalCharity);
    }

    /**
     * Get userGoalCharities
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getUserGoalCharities()
    {
        return $this->userGoalCharities;
    }

    /**
     * @param \Doctrine\Common\Collections\ArrayCollection $userGoalCharities
     */
    public function setUserGoalCharities($userGoalCharities)
    {
        $this->userGoalCharities = $userGoalCharities;
    }

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $userGoalImages;


    /**
     * Add userGoalImage
     *
     * @param \AppBundle\Entity\UserGoalImage $userGoalImage
     *
     * @return UserGoal
     */
    public function addUserGoalImage(
      \AppBundle\Entity\UserGoalImage $userGoalImage
    ) {
        $this->userGoalImages[] = $userGoalImage;

        return $this;
    }

    /**
     * Remove userGoalImage
     *
     * @param \AppBundle\Entity\UserGoalImage $userGoalImage
     */
    public function removeUserGoalImage(
      \AppBundle\Entity\UserGoalImage $userGoalImage
    ) {
        $this->userGoalImages->removeElement($userGoalImage);
    }

    /**
     * Get userGoalImages
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getUserGoalImages()
    {
        return $this->userGoalImages;
    }

    /**
     * Set active
     *
     * @param boolean $active
     *
     * @return UserGoal
     */
    public function setActive($active)
    {
        $this->active = $active;

        return $this;
    }

    /**
     * Get active
     *
     * @return boolean
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * Set paymentToken
     *
     * @param string $paymentToken
     *
     * @return UserGoal
     */
    public function setPaymentToken($paymentToken)
    {
        $this->paymentToken = $paymentToken;

        return $this;
    }

    /**
     * Get paymentToken
     *
     * @return string
     */
    public function getPaymentToken()
    {
        return $this->paymentToken;
    }

    /**
     * Set finished
     *
     * @param boolean $finished
     *
     * @return UserGoal
     */
    public function setFinished($finished)
    {
        $this->finished = $finished;

        return $this;
    }

    /**
     * Get finished
     *
     * @return boolean
     */
    public function getFinished()
    {
        return $this->finished;
    }

    /**
     * @param boolean $show_in_dashboard
     *
     * @return UserGoal
     */
    public function setShowInDashboard($show_in_dashboard)
    {
        $this->show_in_dashboard = $show_in_dashboard;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isShowInDashboard()
    {
        return $this->show_in_dashboard;
    }

    /**
     * Get showInDashboard
     *
     * @return boolean
     */
    public function getShowInDashboard()
    {
        return $this->show_in_dashboard;
    }

    /**
     * Set paymentCharge
     *
     * @param \AppBundle\Entity\PaymentCharge $paymentCharge
     *
     * @return UserGoal
     */
    public function setPaymentCharge(
      \AppBundle\Entity\PaymentCharge $paymentCharge = null
    ) {
        $this->paymentCharge = $paymentCharge;

        return $this;
    }

    /**
     * Get paymentCharge
     *
     * @return \AppBundle\Entity\PaymentCharge
     */
    public function getPaymentCharge()
    {
        return $this->paymentCharge;
    }

    /**
     * Set status
     *
     * @param string $status
     *
     * @return UserGoal
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

    /**
     * Set transactionAttempts
     *
     * @param integer $transactionAttempts
     *
     * @return UserGoal
     */
    public function setTransactionAttempts($transactionAttempts)
    {
        $this->transactionAttempts = $transactionAttempts;

        return $this;
    }

    /**
     * Get transactionAttempts
     *
     * @return integer
     */
    public function getTransactionAttempts()
    {
        return $this->transactionAttempts;
    }

    /**
     * Set deadline
     *
     * @param \DateTime $deadline
     *
     * @return UserGoal
     */
    public function setDeadline($deadline)
    {
        $this->deadline = $deadline;

        if ($deadline instanceof \DateTime) {
            $this->deadline->setTime(23, 59, 59);
        }

        return $this;
    }

    /**
     * Get deadline
     *
     * @return \DateTime
     */
    public function getDeadline()
    {
        return $this->deadline;
    }

    /**
     * Get requiresCheckinImage
     *
     * @return boolean
     */
    public function getRequiresCheckinImage()
    {
        return $this->requiresCheckinImage;
    }

    /**
     * @return bool
     */
    public function isSupervisorNotified()
    {
        return $this->supervisorNotified;
    }

    /**
     * @param bool $supervisorNotified
     */
    public function setSupervisorNotified($supervisorNotified)
    {
        $this->supervisorNotified = $supervisorNotified;
    }
}
