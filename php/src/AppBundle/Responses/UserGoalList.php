<?php

namespace AppBundle\Responses;

use AppBundle\Entity\UserGoal;
use JMS\Serializer\Annotation\Type;
use ApiBundle\Responses\AbstractResponse;
use JMS\Serializer\Annotation\Groups;

class UserGoalList extends AbstractResponse
{
    /**
     * @var array
     * @Type("array")
     * @Groups({"list","details"})
     */
    protected $data;

    /**
     * UserGoalList constructor.
     * @param array $userGoals
     * @param $uploadPath
     */
    public function __construct(array $userGoals, $uploadPath)
    {
        parent::__construct(200);

		$this->data['userGoals'] = [];
        /** @var UserGoal $userGoal */
        foreach ($userGoals as $userGoal) {
            $this->data['userGoals'][] = new UserGoalSummary($userGoal, $uploadPath . 'goals/');
        }
    }
}