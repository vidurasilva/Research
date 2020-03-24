<?php
/**
 * Created by PhpStorm.
 * User: mdubbelman
 * Date: 10/5/16
 * Time: 11:11 AM
 */

namespace AppBundle\Service;

use AppBundle\Entity\Milestone as MilestoneEntity;

class MilestoneService
{
	const STATUS_DONE   = 'done';
	const STATUS_OPEN   = 'open';
	const STATUS_FAILED = 'failed';
	const STATUS_CLOSE  = 'closed';

	public static function getTasks()
	{
		return [
			self::STATUS_DONE,
			self::STATUS_OPEN,
			self::STATUS_FAILED,
		];
	}

	public static function isValidStatus($status)
	{
		return in_array($status, self::getTasks());
	}

	/**
	 * @param MilestoneEntity $milestone
	 * @param \DateTime $userGoalStartDate
	 * @return int|mixed
	 * @deprecated This function is deprecated because of new goals duplicate milestones and task with deadline in DB!
	 */
	public function calculateMilestoneDaysLeft(\AppBundle\Entity\Milestone $milestone, $userGoalStartDate)
	{
		$now = new \DateTime('now');

		//Get deadline from milestone record or generate based on the duration etc...
		$milestoneEndDate = $milestone->getDeadline() ? $milestone->getDeadline() : $this->calculateMilestoneDeadline($milestone,
			$userGoalStartDate);

		$diff = $now->diff($milestoneEndDate);

		if ($diff->invert === 0) {
			return $diff->days;
		}

		return 0;
	}

	/**
	 * @param MilestoneEntity $milestone
	 * @return int|mixed
	 */
	public function calculateMilestoneDays(MilestoneEntity $milestone)
	{
		$now = new \DateTime();
		$now->setTime(0, 0, 0);
		$milestoneDeadline = $milestone->getDeadline();

		if (empty($milestoneDeadline)) {
			return 0;
		}

		$milestoneDeadline->setTime(0, 0, 0);
		$diff = $now->diff($milestone->getDeadline());

		if ($diff->invert === 0) {
			return $diff->days;
		}

		return 0;
	}

	/**
	 * Calculate Milestone deadline on startdate of User Goal
	 *
	 * @param MilestoneEntity $milestone
	 * @param \DateTime $userGoalStartDate
	 *
	 * @return \DateTime
	 */
	private function calculateMilestoneDeadline(MilestoneEntity $milestone, $userGoalStartDate)
	{
		if ($milestone->getRecurrence() === 'week' || $milestone->getRecurrence() === 'weeks') {
			$plusDate = $milestone->getDuration() * 7;

			return $userGoalStartDate->modify('+' . $plusDate . ' days');
		} else {
			$plusDate = $milestone->getDuration();

			return $userGoalStartDate->modify('+' . $plusDate . ' month');
		}
	}

	public function calculateUnfinishedMilestoneTasks(MilestoneEntity $milestone, $finishedTasks = [])
	{
		$totalMilestoneTasks = $milestone->getTasks()->count();

		foreach ($milestone->getTasks() as $task) {

		}
	}
}