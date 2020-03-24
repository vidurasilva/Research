<?php

namespace AppBundle\Responses;

use JMS\Serializer\Annotation\Type;
use ApiBundle\Responses\AbstractResponse;
use Symfony\Component\HttpFoundation\Request;
use JMS\Serializer\Annotation\Groups;

class CheckinSummary extends AbstractResponse
{
	protected $status;

	/**
	 * @var int
	 * @Groups({"list","details"})
	 * @Type("integer")
	 */
	protected $userGoalId;

	/**
	 * @var int
	 * @Groups({"list","details"})
	 * @Type("integer")
	 */
	protected $goalId;

	/**
	 * @var string
	 * @Groups({"list","details"})
	 * @Type("string")
	 */
	protected $goalTitle;

	/**
	 * @var int
	 * @Groups({"list","details"})
	 * @Type("integer")
	 */
	protected $taskId;

	/**
	 * @var string
	 * @Groups({"list","details"})
	 * @Type("string")
	 */
	protected $taskTitle;

	/**
	 * @var boolean
	 * @Groups({"list","details"})
	 * @Type("boolean")
	 */
	protected $done;

	/**
	 * @var \DateTime
	 * @Groups({"list","details"})
	 * @Type("datetime")
	 */
	protected $checkinDate;

	/**
	 * @var array
	 * @Groups({"list","details"})
	 * @Type("array")
	 */
	protected $userGoal;


    /**
     * CategoryList constructor.
     * @param array $checkinData
     */
    public function __construct(array $checkinData)
    {
        parent::__construct(200);

		$this->userGoalId = $checkinData['userGoalId'];
		$this->goalId = $checkinData['userGoalId'];
		$this->goalTitle = $checkinData['userGoalId'];
		$this->taskId = $checkinData['userGoalId'];

    }
}