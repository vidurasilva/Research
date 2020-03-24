<?php

namespace AppBundle\Responses;

use ApiBundle\Responses\AbstractResponse;
use AppBundle\Entity\Goal;
use AppBundle\Entity\Milestone;
use AppBundle\Entity\Task;
use JMS\Serializer\Annotation\Type;
use JMS\Serializer\Annotation\Groups;
use Symfony\Component\HttpFoundation\Request;

class TaskDetail extends AbstractResponse
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
    public function __construct(Task $task, $basePath, $milestones = NULL)
    {
        parent::__construct(200);

        $image = NULL;

        if (!$milestones) {
            $milestones = $task->getMilestone()->getGoal()->getMilestones();
        }

        if (!empty($task->getImage())) {
            $image = $basePath . $task->getImage();
        }

        $this->data = [
            'task' => [
                'id'          => $task->getId(),
                'title'       => $task->getTitle(),
                'position'    => $task->getPosition(),
                'video'       => $task->getVideo(),
                'image'       => $image,
                'description' => strip_tags($task->getDescription()),
                'points'      => $task->getPoints(),
            ],
            'goal' => new GoalSummary(
                $task->getMilestone()->getGoal(),
                $basePath,
                $milestones
            ),
        ];

    }
}