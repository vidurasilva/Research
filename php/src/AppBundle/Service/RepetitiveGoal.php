<?php
/**
 * Created by PhpStorm.
 * User: Matthijs Overboom
 * Date: 6-1-17
 * Time: 13:29
 */

namespace AppBundle\Service;

use AppBundle\Entity\Goal as Goal;
use AppBundle\Entity\Milestone as Milestone;
use AppBundle\Entity\Task;
use Doctrine\ORM\EntityManager;

class RepetitiveGoal
{
	/**
	 * @var EntityManager
	 */
	private $em;

	public function __construct(EntityManager $em)
	{
		$this->em = $em;
	}

	/**
	 * Generate Milestones for Custom Goal
	 *
	 * @param GoalService $goal
	 *
	 * @param Task $taskPrototype
	 */
	public function generateRepetitiveGoalMileStones(Goal $goal, Task $taskPrototype)
	{
		$duration = $goal->getDuration();

		if($goal->getDurationUnit() === 'month' && $goal->getRecurrence() === 'week') {
			$duration = $goal->getDuration() * 4;
		}
		$mileStoneRecurrence = $goal->getRecurrence() . 's';
		if($goal->getRecurrence() === 'day') {
			$mileStoneRecurrence = 'months';
		}

		for ($milestoneCount = 1; $milestoneCount <= $duration; $milestoneCount++) {
			$mileStone = new Milestone();
			$mileStone->setTitle('Milestone ' . $milestoneCount);
			$mileStone->setGoal($goal);
			$mileStone->setNumber($milestoneCount);
			$mileStone->setDuration(1);
			$mileStone->setRecurrence($mileStoneRecurrence);
			$goal->addMilestone($mileStone);
		}
		$this->generateRepetitiveGoalTasks($goal, $taskPrototype);

		$this->em->persist($goal);
		$this->em->flush();
	}

	/**
	 * @param GoalService $goal
	 * @param Task $taskPrototype
	 */
	private function generateRepetitiveGoalTasks(Goal $goal, Task $taskPrototype)
	{
		foreach ($goal->getMilestones() as $milestone) {
			if($goal->getRecurrence() === 'day' && $goal->getDurationUnit() !== 'month') {
				$milestone->addMultipleTasks($this->generateTasks(7, $goal, $milestone, $taskPrototype));
			} else if($goal->getRecurrence() === 'day' && $goal->getDurationUnit() === 'month') {
				$milestone->addMultipleTasks($this->generateTasks(28, $goal, $milestone, $taskPrototype));
			} else {
				$milestone->addMultipleTasks($this->generateTasks($goal->getIteration(), $goal, $milestone, $taskPrototype));
			}
			$goal->addMilestone($milestone);
		}
	}

	/**
	 * @param $amount
	 * @param GoalService $goal
	 * @param Milestone $milestone
	 * @param Task $taskPrototype
	 * @return array
	 */
	private function generateTasks($amount, Goal $goal, Milestone $milestone, Task $taskPrototype)
	{
		$tasks = [];
		for($i = 0; $i < $amount; $i++) {
			$task = clone $taskPrototype;
			$task->setGoal($goal);
			$task->setMilestone($milestone);
			$task->setPosition($i + 1);
			$tasks[] = $task;
		}
		return $tasks;
	}
}