<?php

namespace AppBundle\Responses;

use AppBundle\Entity\Milestone;
use AppBundle\Entity\UserGoal;
use JMS\Serializer\Annotation\Type;
use ApiBundle\Responses\AbstractResponse;
use Symfony\Component\HttpFoundation\Request;
use JMS\Serializer\Annotation\Groups;

class TimelineList extends AbstractResponse
{
    /**
     * @var array
     * @Type("array")
     * @Groups({"list","details"})
     */
    protected $data;

    /**
     * TimelineList constructor.
     * @param UserGoal $goal
     * @param $basePath
     * @param array $finishedTasks
     */
    public function __construct(UserGoal $goal, $basePath, $finishedTasks = [], $nextCheckin = NULL)
    {
        parent::__construct(200);
		$this->data = [
			'userGoal' => new UserGoalTimeline($goal, $basePath, $finishedTasks, $nextCheckin),
	    ];
    }
}