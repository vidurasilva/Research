<?php

namespace AppBundle\Entity;

use AppBundle\Entity\Traits\CrudTrait;
use Doctrine\Common\Collections\ArrayCollection;


/**
 * Milestone
 */
class Milestone
{
    const RECURRENCE_WEEK  = 'week';
    const RECURRENCE_MONTH = 'month';

    const RECURRENCE_FRIENDLY_WEEK  = 'weekly';
    const RECURRENCE_FRIENDLY_MONTH = 'monthly';

    const RECURRENCE_WEEK_PLURAL  = 'weeks';
    const RECURRENCE_MONTH_PLURAL = 'months';

    const DEFAULT_MILESTONE_DURATION = '-1 weeks';

    public static $recurrences = [
        self::RECURRENCE_FRIENDLY_WEEK  => self::RECURRENCE_WEEK,
        self::RECURRENCE_FRIENDLY_MONTH => self::RECURRENCE_MONTH
    ];

    public static function getRecurrences()
    {
        return [
            self::RECURRENCE_WEEK_PLURAL  => self::RECURRENCE_FRIENDLY_WEEK,
            self::RECURRENCE_MONTH_PLURAL => self::RECURRENCE_FRIENDLY_MONTH
        ];
    }

    public static function mapRecurrence($key)
    {
        if (array_key_exists($key, self::$recurrences)) {
            return self::$recurrences[$key];
        }
    }

    use CrudTrait;

    /**
     * @var \AppBundle\Entity\Goal
     */
    private $goal;

    /**
     * @var string
     */
    private $recurrence = 0;

    /**
     * @var int
     */
    private $iterations;

    /**
     * @var int
     */
    private $duration;

	/**
	 * Overwrite default clone function of doctrine
	 */
	public function __clone()
	{
		// Get current collection
		$tasks = $this->getTasks();

		$this->tasks = new ArrayCollection();
		foreach ($tasks as $task) {
			$cloneTask = clone $task;
			$this->tasks->add($cloneTask);
			$cloneTask->setMilestone($this);
		}
	}

    /**
     * LifeCycleEvent.
     */
    public function preUpdate()
    {
        $this->iterations = (int)$this->getTasks()->count();
    }

    /**
     * Set goal
     *
     * @param \AppBundle\Entity\Goal $goal
     *
     * @return Milestone
     */
    public function setGoal(\AppBundle\Entity\Goal $goal = NULL)
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
    private $number;


    /**
     * Set number
     *
     * @param integer $number
     *
     * @return Milestone
     */
    public function setNumber($number)
    {
        $this->number = $number;

        return $this;
    }

    /**
     * Get number
     *
     * @return integer
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $tasks;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->tasks = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add task
     *
     * @param \AppBundle\Entity\Task $task
     *
     * @return Milestone
     */
    public function addTask(\AppBundle\Entity\Task $task)
    {
        $task->setMilestone($this);
        $this->tasks[] = $task;

        return $this;
    }

	/**
	 * Add multiple tasks
	 *
	 * @param array $tasks
	 * @return $this
	 */
    public function addMultipleTasks(array $tasks)
	{
		foreach ($tasks as $task) {
			$this->addTask($task);
		}

		return $this;
	}

    /**
     * Remove task
     *
     * @param \AppBundle\Entity\Task $task
     */
    public function removeTask(\AppBundle\Entity\Task $task)
    {
        $this->tasks->removeElement($task);
    }

    /**
     * Get tasks
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTasks()
    {
        return $this->tasks;
    }

    /**
     * @var \DateTime
     */
    private $deadline;


    /**
     * Set deadline
     *
     * @param \DateTime $deadline
     *
     * @return Milestone
     */
    public function setDeadline($deadline)
    {
        $this->deadline = $deadline;

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
     * @return string
     */
    public function getRecurrence()
    {
        return $this->recurrence;
    }

    /**
     * @param string $recurrence
     */
    public function setRecurrence($recurrence)
    {
        $this->recurrence = $recurrence;
    }

    /**
     * @return int
     */
    public function getIterations()
    {
        return $this->iterations;
    }

    /**
     * @param int $iterations
     */
    public function setIterations($iterations)
    {
        $this->iterations = $iterations;
    }

    /**
     * @return int
     */
    public function getDuration()
    {
        return $this->duration;
    }

    /**
     * @param int $duration
     */
    public function setDuration($duration)
    {
        $this->duration = $duration;
    }
    /**
     * @var \DateTime
     */
    private $startDate;


    /**
     * Set startDate
     *
     * @param \DateTime $startDate
     *
     * @return Milestone
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
}
