<?php

namespace AppBundle\Responses;

use ApiBundle\Responses\AbstractResponse;
use AppBundle\Entity\Task;
use AppBundle\Model\Status;
use JMS\Serializer\Annotation as Serializer;
use JMS\Serializer\Annotation\Type;
use JMS\Serializer\Annotation\Groups;
use AppBundle\Service\MilestoneService as MilestoneService;

class TaskSummary extends AbstractResponse
{
	protected $status;

	/**
	 * @var int
	 * @Groups({"list","details"})
	 * @Type("integer")
	 */
	protected $id;

	/**
	 * @var string
	 * @Groups({"list","details"})
	 * @Type("string")
	 */
	protected $title;

	/**
	 * @var string
	 * @Groups({"list","details"})
	 * @Type("integer")
	 */
	protected $position;

	/**
	 * @var integer
	 * @Groups({"list","details"})
	 * @Type("integer")
	 */
	protected $points;

	/**
	 * @var string
	 * @Groups({"list","details"})
	 * @Type("string")
	 */
	protected $description;

	/**
	 * @var string
	 * @Groups({"list","details"})
	 * @Type("string")
	 */
	protected $video;

	/**
	 * @var string
	 * @Groups({"list","details"})
	 * @Type("string")
	 */
	protected $image;

	/**
	 * @var boolean
	 * @Groups({"list","details"})
	 * @Type("boolean")
	 */
	protected $done = false;

	/**
	 * @var string
	 * @Groups({"list","details"})
	 * @Type("string")
	 */
	protected $taskStatus;

	/**
	 * @var integer
	 * @Groups({"list","details"})
	 * @Type("integer")
	 */
	protected $failedTasks;

	/**
	 * @var integer
	 * @Groups({"list","details", "dashboard"})
	 * @Type("integer")
	 */
	protected $unfinishedMilestoneTasks = 0;

	/**
	 * @var integer
	 * @Groups({"list","details", "dashboard"})
	 * @Type("integer")
	 */
	protected $milestoneDaysLeft = 0;


	/**
	 * @var string
	 * @Type("string")
	 * @Groups({"list","details"})
	 */
	protected $checkinDate;

	/**
	 * TaskSummary constructor.
	 * @param Task $task
	 * @param bool $done
	 * @param null $userGoalStartDate
	 * @param null $goal
	 * @param string $state
	 * @param string $basePath
	 * @param $failedTasks
	 */
	public function __construct(
		Task $task,
		$done = false,
		$userGoalStartDate = null,
		$goal = null,
		$state = Status::OPEN,
		$basePath = null,
		$failedTasks = 0,
		$checkinDate = null
	) {
		parent::__construct(200);
		$this->id       = $task->getId();
		$this->title    = $task->getTitle();
		$this->position = $task->getPosition();
		$this->video    = $task->getVideo();

		if (!empty($task->getImage())) {
			$this->image = $basePath . $task->getImage();
		}
		$this->description = $task->getDescription();
		$this->points      = $task->getPoints();
		$this->done        = $done;

		if (null !== $checkinDate && $checkinDate instanceof \DateTime){
			$this->checkinDate = $checkinDate->format('Y-m-d');
		}

		$this->taskStatus  = !empty($state) ? $state : Status::OPEN;
		$this->failedTasks = $failedTasks;

		//Is their a use case where custom or default goals do not have any points?
		if (null !== $goal) {
			$this->points = !empty($goal->getDeadline()) ? $goal->getPoints() : $task->getPoints();
		}

		$this->unfinishedMilestoneTasks = $task->getUnfinishedMilestoneTasks();

		$milestoneService = new MilestoneService();

		$this->milestoneDaysLeft = $milestoneService->calculateMileStoneDays($task->getMilestone());
	}
}