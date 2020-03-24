<?php

namespace AppBundle\Entity;

use ApiBundle\Entity\BasePathAware;
use AppBundle\Entity\Traits\CrudTrait;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use JMS\Serializer\Annotation\Type;
use JMS\Serializer\Annotation\Groups;
use UserBundle\Entity\User;

/**
 * Goal
 */
class Goal implements BasePathAware
{

    use CrudTrait;

    /**
     * @var int
     */
    private $duration;

    /**
     * @var string
     */
    private $durationUnit;

    /**
     * @var DateTime
     */
    private $startDate;

    /**
     * @var DateTime
     */
    private $deadline;

    /**
     * @var string
     */
    private $recurrence;

    /**
     * @var int
     */
    private $iteration;

    /**
     * @var string
     */
    private $description;

    /**
     * @var string
     * @Groups({"list","details"})
     * @Type("string")
     */
    private $icon;

    /**
     * @var integer
     */
    private $points;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $milestones;

    /**
     * @var User
     */
    private $user;

    /**
     * @var \AppBundle\Entity\GoalCategory
     */
    private $category;

    /**
     * @var \AppBundle\Entity\CommunityCategory
     */
    private $communityCategory;


    /**
     * @var string
     */
    protected $basePath;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->milestones = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Overwrite default clone function of doctrine
     */
    public function __clone()
    {
        // Get current collection
        $milestones = $this->getMilestones();

        $this->milestones = new ArrayCollection();
        foreach ($milestones as $milestone) {
            $cloneMilestone = clone $milestone;
            $this->milestones->add($cloneMilestone);
            $cloneMilestone->setGoal($this);
        }
    }

    /**
     * Get duration
     *
     * @return int
     */
    public function getDuration()
    {
        return $this->duration;
    }

    /**
     * Set duration
     *
     * @param int $duration
     *
     * @return Goal
     */
    public function setDuration($duration)
    {
        $this->duration = $duration;

        return $this;
    }

    /**
     * Return the durationUnit (week or month)
     *
     * @return string
     */
    public function getDurationUnit()
    {
        return $this->durationUnit;
    }

    /**
     * @param string $durationUnit
     */
    public function setDurationUnit($durationUnit)
    {
        $this->durationUnit = $durationUnit;
    }

    /**
     * @return DateTime
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * @param DateTime $startDate
     *
     * @return Goal
     */
    public function setStartDate($startDate)
    {
        $this->startDate = $startDate;

        return $this;
    }

    /**
     * @return DateTime
     */
    public function getDeadline()
    {
        return $this->deadline;
    }

    /**
     * @param DateTime $deadline
     *
     * @return $this
     */
    public function setDeadline($deadline)
    {
        $this->deadline = $deadline;

        return $this;
    }


    /**
     * @return string
     */
    public function getRecurrence()
    {
        return $this->recurrence;
    }

    /**
     * @param string $recurrence
     *
     * @return $this
     */
    public function setRecurrence($recurrence)
    {
        $this->recurrence = $recurrence;

        return $this;
    }

    /**
     * @return int
     */
    public function getIteration()
    {
        return $this->iteration;
    }

    /**
     * @param int $iteration
     *
     * @return $this
     */
    public function setIteration($iteration)
    {
        $this->iteration = $iteration;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Goal
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get icon
     *
     * @return string
     */
    public function getIcon()
    {
        return $this->icon;
    }

    /**
     * Set icon
     *
     * @param string $icon
     *
     * @return Goal
     */
    public function setIcon($icon)
    {
        $this->icon = $icon;

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

    /**
     * Set points
     *
     * @param integer $points
     *
     * @return Goal
     */
    public function setPoints($points)
    {
        $this->points = $points;

        return $this;
    }

    /**
     * Get milestones
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getMilestones()
    {
        return $this->milestones;
    }

    /**
     * Add milestone
     *
     * @param \AppBundle\Entity\Milestone $milestone
     *
     * @return Goal
     */
    public function addMilestone(\AppBundle\Entity\Milestone $milestone)
    {
        $milestone->setGoal($this);

        $this->milestones[] = $milestone;

        return $this;
    }

    /**
     * Remove milestone
     *
     * @param \AppBundle\Entity\Milestone $milestone
     */
    public function removeMilestone(\AppBundle\Entity\Milestone $milestone)
    {
        $this->milestones->removeElement($milestone);
    }

    /**
     * Clear all milestones
     */
    public function clearMilestones()
    {
        $this->milestones = new ArrayCollection();
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param User $user
     *
     * @return $this
     */
    public function setUser($user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get category
     *
     * @return \AppBundle\Entity\GoalCategory
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * Set category
     *
     * @param \AppBundle\Entity\GoalCategory $category
     *
     * @return Goal
     */
    public function setCategory(\AppBundle\Entity\GoalCategory $category = null)
    {
        $this->category = $category;

        return $this;
    }


    /**
     * Get communityCategory
     *
     * @return \AppBundle\Entity\CommunityCategory
     */
    public function getCommunityCategory()
    {
        return $this->communityCategory;
    }


    /**
     * Set communityCategory
     *
     * @param \AppBundle\Entity\CommunityCategory $communityCategory
     *
     * @return Goal
     */
    public function setCommunityCategory(
      \AppBundle\Entity\CommunityCategory $communityCategory = null
    ) {
        $this->communityCategory = $communityCategory;

        return $this;
    }

    /**
     * @param $basePath
     */
    public function setBasePath($basePath)
    {
        $this->basePath = $basePath;
        $this->icon = $basePath . $this->icon;
    }

    /**
     * @var string
     */
    private $additionalDescription;


    /**
     * Set additionalDescription
     *
     * @param string $additionalDescription
     *
     * @return Goal
     */
    public function setAdditionalDescription($additionalDescription)
    {
        $this->additionalDescription = $additionalDescription;

        return $this;
    }

    /**
     * Get additionalDescription
     *
     * @return string
     */
    public function getAdditionalDescription()
    {
        return $this->additionalDescription;
    }

    /**
     * @var boolean
     */
    private $predefined = 0;

    /**
     * @var \AppBundle\Entity\Goal
     */
    private $originGoal;


    /**
     * Set predefined
     *
     * @param boolean $predefined
     *
     * @return Goal
     */
    public function setPredefined($predefined)
    {
        $this->predefined = $predefined;

        return $this;
    }

    /**
     * Get predefined
     *
     * @return boolean
     */
    public function getPredefined()
    {
        return $this->predefined;
    }

    /**
     * Set originGoal
     *
     * @param \AppBundle\Entity\Goal $originGoal
     *
     * @return Goal
     */
    public function setOriginGoal(\AppBundle\Entity\Goal $originGoal = null)
    {
        $this->originGoal = $originGoal;

        return $this;
    }

    /**
     * Get originGoal
     *
     * @return \AppBundle\Entity\Goal
     */
    public function getOriginGoal()
    {
        return $this->originGoal;
    }
}
