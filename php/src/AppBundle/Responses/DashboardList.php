<?php

namespace AppBundle\Responses;

use AppBundle\Service\UserGoal;
use JMS\Serializer\Annotation\Type;
use ApiBundle\Responses\AbstractResponse;
use Symfony\Component\HttpFoundation\Request;
use JMS\Serializer\Annotation\Groups;

class DashboardList extends AbstractResponse
{
    /**
     * @var array
     * @Type("array")
     * @Groups({"list","details"})
     */
    protected $data;

	/**
	 * InviteList constructor.
	 * @param array $invites
	 * @param array $checkins
	 * @param array $failedUserGoals
	 * @param array $succeededUserGoals
	 * @param \DateTime $date
	 * @param $basePath
	 * @param UserGoal $userGoalService
	 */
    public function __construct(array $invites, array $checkins, array $failedUserGoals, array $succeededUserGoals, \DateTime $date, $basePath, UserGoal $userGoalService = NULL)
    {
        parent::__construct(200);

        $this->data = [
            'groupInvites' => [],
            'checkins'     => [],
            'failedGoals'  => [],
			'succeededGoals' => []
        ];

        if (!empty($invites)) {
            foreach ($invites as $invite) {
                $this->data['groupInvites'][] = new GroupSummary($invite['groupUser'], $invite['userGoal'], false, $basePath);
            }
        }

        if (!empty($checkins)) {
            foreach ($checkins as $checkin) {
            	$failedTasks = 0;
            	if($userGoalService) {
            		$failedTasks = $userGoalService->countFailedTasks($checkin['userGoal']);
				}
				$userGoalStartDate = clone $checkin['userGoal']->getStartDate();
            	$this->data['checkins'][] =
                    [
                        'task'     => new TaskSummary($checkin['task'], $checkin['done'], $userGoalStartDate,
	                        $checkin['userGoal']->getGoal(), $checkin['state'], $basePath, $failedTasks,
	                        $checkin['checkinDate']),
                        'userGoal' => new UserGoalSummary($checkin['userGoal'], $basePath . 'goals/'),
                    ];
            }
        }

        if (!empty($failedUserGoals)) {
            foreach ($failedUserGoals as $failedUserGoal) {
                $this->data['failedGoals'][] = new UserGoalSummary($failedUserGoal, $basePath, $userGoalService);
            }
        }

        if (!empty($succeededUserGoals)) {
			foreach ($succeededUserGoals as $succeededUserGoal) {
				$this->data['succeededGoals'][] = new UserGoalSummary($succeededUserGoal, $basePath);
        	}
		}
    }
}