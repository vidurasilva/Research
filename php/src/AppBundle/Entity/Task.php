<?php

namespace AppBundle\Entity;

use AppBundle\Entity\Traits\CrudTrait;

/**
 * Task
 */
class Task
{
	const DEFAULT_POINTS = 10;

    use CrudTrait;

    /**
     * @var int
     */
    protected $milestoneDaysLeft = 0;
    /**
     * @var int
     */
    protected $unfinishedMilestoneTasks = 0;
    /**
     * @var \AppBundle\Entity\Milestone
     */
    private $milestone;
    /**
     * @var integer
     */
    private $position;
    /**
     * @var integer
     */
    private $points;
    /**
     * @var string
     */
    private $video;
    /**
     * @var string
     */
    private $description;
    /**
     * @var string
     */
    private $image;
	/** @var boolean */
    private $removeImage;
    /**
     * @var \AppBundle\Entity\Goal
     */
    private $goal;

    /**
     * Get milestone
     *
     * @return \AppBundle\Entity\Milestone
     */
    public function getMilestone()
    {
        return $this->milestone;
    }

    /**
     * Set milestone
     *
     * @param \AppBundle\Entity\Milestone $milestone
     *
     * @return Task
     */
    public function setMilestone(\AppBundle\Entity\Milestone $milestone = NULL)
    {
        $this->milestone = $milestone;

        return $this;
    }

    /**
     * Get position
     *
     * @return integer
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * Set position
     *
     * @param integer $position
     *
     * @return Task
     */
    public function setPosition($position)
    {
        $this->position = $position;

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
     * @return Task
     */
    public function setPoints($points)
    {
        $this->points = $points;

        return $this;
    }

    /**
     * Get video
     *
     * @return string
     */
    public function getVideo()
    {
        return $this->video;
    }

    /**
     * Set video
     *
     * @param string $video
     *
     * @return Task
     */
    public function setVideo($video)
    {
        $this->video = $video;

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
     * @return Task
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return int
     */
    public function getMilestoneDaysLeft()
    {
        return $this->milestoneDaysLeft;
    }

    /**
     * @param int $milestoneDaysLeft
     */
    public function setMilestoneDaysLeft($milestoneDaysLeft)
    {
        $this->milestoneDaysLeft = $milestoneDaysLeft;
    }

    /**
     * @return int
     */
    public function getUnfinishedMilestoneTasks()
    {
        return $this->unfinishedMilestoneTasks;
    }

    /**
     * @param int $unfinishedMilestoneTasks
     */
    public function setUnfinishedMilestoneTasks($unfinishedMilestoneTasks)
    {
        $this->unfinishedMilestoneTasks = $unfinishedMilestoneTasks;
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
     * Set image
     *
     * @param string $image
     *
     * @return Task
     */
    public function setImage($image)
    {
        $this->image = $image;

        return $this;
    }

	/**
	 * @return bool
	 */
	public function getRemoveImage()
	{
		return $this->removeImage;
	}

	/**
	 * @param bool $removeImage
	 */
	public function setRemoveImage($removeImage)
	{
		$this->removeImage = $removeImage;
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
     * Set goal
     *
     * @param \AppBundle\Entity\Goal $goal
     *
     * @return Task
     */
    public function setGoal(\AppBundle\Entity\Goal $goal = null)
    {
        $this->goal = $goal;

        return $this;
    }

    protected $tempStatus = false;

    public function getTempStatus()
    {
    	return $this->tempStatus;
    }

    public function setTempStatus($status)
    {
    	$this->tempStatus = $status;
    }
}
