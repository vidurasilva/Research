<?php

namespace AppBundle\Responses;

use ApiBundle\Responses\AbstractResponse;
use AppBundle\Entity\UserGoal;
use AppBundle\Entity\UserGoalImage;
use JMS\Serializer\Annotation\Type;
use JMS\Serializer\Annotation\Groups;

class UserGoalDetail extends AbstractResponse
{
    /**
     * @var array
     * @Type("array")
     * @Groups({"list","details"})
     */
    protected $data;

    /**
     * GoalSummary constructor.
     * @param UserGoal $goal
     */
    public function __construct(UserGoal $goal, $basePath, $failedTaks = 0)
    {
        parent::__construct(200);

        /** @var UserGoalImage|null $image */
        $image = NULL;
        if ($goal->getUserGoalImages()) {
            $image = $goal->getUserGoalImages()->first();
        }
        $charities = [];

        foreach ($goal->getUserGoalCharities() as $charity) {
	        $charities[] = new UserGoalCharitySummary($charity);
        }

        $this->data['userGoal'] = [
            'id'           		   => $goal->getId(),
            'supervisor'   		   => $goal->getSuperVisor(),
			'requiresCheckinImage' => $goal->requiresCheckinImage(),
            'amount'       		   => $goal->getStakeAmount() ? (int)$goal->getStakeAmount() : NULL,
            'currency'     		   => $goal->getCurrency(),
            'maximumFails' 		   => (int)$goal->getMaximumFails(),
            'image'        		   => $image ? $basePath . $image->getImage() : NULL,
            'charities'    		   => $charities,
            'failedTasks'  		   => $failedTaks,
            'goal'          	   => new GoalSummary($goal->getGoal(), null),
            'finished'     		   => $goal->getFinished(),
            'groupId'              => $goal->getGroup() ? $goal->getGroup()->getId() : NULL
        ];
    }
}