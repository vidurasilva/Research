<?php

namespace AppBundle\Responses;

use JMS\Serializer\Annotation\Type;
use ApiBundle\Responses\AbstractResponse;
use Symfony\Component\HttpFoundation\Request;
use JMS\Serializer\Annotation\Groups;

class CheckinList extends AbstractResponse
{
    /**
     * @var array
     * @Type("array")
     * @Groups({"list","details"})
     */
    protected $data;

    /**
     * CategoryList constructor.
     * @param array $checkins
     */
    public function __construct(array $checkins, \DateTime $dateTime, $basePath)
    {
        parent::__construct(200);

        $this->data = [
            'date'     => $dateTime->format('Y-m-d'),
            'checkins' => [],
        ];


        foreach ($checkins as $checkin) {
            $this->data['checkins'][] = [
                'task'     => new TaskSummary($checkin['task'], $checkin['done']),
                'userGoal' => new UserGoalSummary($checkin['userGoal'], $basePath . 'goals/'),
            ];
        }
    }
}