<?php

namespace AppBundle\Responses;

use AppBundle\Entity\Task;
use AppBundle\Entity\UserGoal;
use AppBundle\Model\Status;
use JMS\Serializer\Annotation\Type;
use ApiBundle\Responses\AbstractResponse;
use Symfony\Component\HttpFoundation\Request;
use JMS\Serializer\Annotation\Groups;

class CheckinDetail extends AbstractResponse
{
    /**
     * @var array
     * @Groups({"list","details"})
     * @Type("array")
     */
    protected $data;

    /**
     * GoalSummary constructor.
     * @param Task $task
     */
    public function __construct(Task $task, UserGoal $userGoal, $basePath, $milestones = NULL)
    {
        parent::__construct(200);
        $this->data = [
            'task'     => new TaskSummary($task, true, $userGoal->getStartDate(), NULL, Status::DONE),
            'userGoal' => new UserGoalSummary($userGoal, $basePath),
        ];

    }
}