<?php

namespace AppBundle\Helper;

use AppBundle\Entity\Task;
use AppBundle\Entity\UserGoal;

/**
 * Created by PhpStorm.
 * User: ngeleedst
 * Date: 19/10/16
 * Time: 09:14
 */
class UserGoalHelper
{
	const COOLDOWN_TIME =  '+2 days';
    /**
     * @var UserGoal
     */
    protected $userGoal;

    /**
     * @var Task
     */
    protected $currentTask;

    /**
     * GoalHelper constructor.
     * @param UserGoal $userGoal
     */
    public function __construct(UserGoal $userGoal, Task $task)
    {
        $this->userGoal    = $userGoal;
        $this->currentTask = $task;
    }

    /**
     * Calculate the milestone days left based on the
     */
    public function calculateMilestoneDaysLeft()
    {
        $data      = [];
        $now       = new \DateTime('now');
        $startDate = $this->userGoal->getStartDate();
        $endDate   = $this->userGoal->getEndDate();
        $totalDays = $endDate->diff($startDate)->days;

        //
        $daysLeft  = $startDate->diff($endDate)->days;
        $dayNumber = $startDate->diff($now)->days;
        $progress = $this->daysToPercentage($totalDays, $dayNumber);

        //Check if custom milestone or defined milestone, if custom deadline calculate based on the set deadline date
        if (NULL !== $this->currentTask->getMilestone()->getDeadline()) {
            $diff = $now->diff($this->currentTask->getMilestone()->getDeadline());
            if ($diff->invert !== 1) {
                return $diff->days;
            }

            return 0;
        }

        /** Transform milestone object to percentage */
        if (!empty($milestones)) {
            foreach ($milestones as $milestone) {


                $mileStoneDate = clone $startDate->modify(sprintf('+%d %s', $milestone->getDuration(), $milestone->getRecurrence()));

                //$data[$milestone]
            }
        }
    }

    /**
     * @param $totalDays
     * @param $dayNumber
     * @return float|int
     */
    protected function daysToPercentage($totalDays, $dayNumber)
    {
        return (int) number_format(round(($dayNumber / $totalDays) * 100), 0);
    }


	/**
	 * Generate end date with cooldown time
	 * @param \DateTime $endDate
	 * @return \DateTime
	 */
	public static function getCooldownEndDate(\DateTime $endDate)
	{
		$cooldownEndDate = clone $endDate;

		return $cooldownEndDate->modify(SELF::COOLDOWN_TIME);
	}
}