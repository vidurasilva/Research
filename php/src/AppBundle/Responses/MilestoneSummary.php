<?php

namespace AppBundle\Responses;

use ApiBundle\Responses\AbstractResponse;
use AppBundle\Entity\Milestone;
use AppBundle\Model\Status;
use AppBundle\Service\MilestoneService as MilestoneService;
use Doctrine\Tests\Common\DataFixtures\StateFixture;
use JMS\Serializer\Annotation\Type;
use JMS\Serializer\Annotation\Groups;

class MilestoneSummary extends AbstractResponse
{
    // added so it will not be added to the response
    protected $status;

    /**
     * @var int
     * @Groups({"list","details"})
     * @Type("integer")
     */
    protected $id;

    /**
     * @var string
     * @Groups({"list","details"})
     * @Type("string")
     */
    protected $title;

    /**
     * @var int
     * @Groups({"list","details"})
     * @Type("integer")
     */
    protected $number;

    /**
     * @var string
     * @Groups({"list","details"})
     * @Type("string")
     */
    protected $milestoneStatus = MilestoneService::STATUS_OPEN;

    /**
     * @var array
     * @Groups({"list","details"})
     * @Type("array")
     */
    protected $tasks;

    /**
     * GoalSummary constructor.
     *
     * @param Milestone $milestone
     * @param array $tasks
     * @param array $finishedTasks
     * @param \DateTime $userGoalStartDate
     */
    public function __construct(Milestone $milestone, array $tasks = NULL, $finishedTasks = [], \DateTime $userGoalStartDate = NULL, $nextCheckin = NULL)
    {
        //parent::__construct(200);
        $this->id     = $milestone->getId();
        $this->title  = $milestone->getTitle();
        $this->number = $milestone->getNumber();
        $this->tasks  = [];
        if (!$tasks) {
            $tasks = $milestone->getTasks();
        }

        if ($milestone->getDeadline() === NULL && $userGoalStartDate) {
            $milestoneDeadline = $this->calculateMilestoneDeadline($milestone, $userGoalStartDate);
            $milestone->setDeadline($milestoneDeadline);
        }

        $allTasksDone   = true;
        $countOpenTasks = 0;

        if (empty($userGoalStartDate)) {
            $this->milestoneStatus = MilestoneService::STATUS_CLOSE;
        }

        foreach ($tasks as $task) {
            $done  = array_key_exists($task->getId(), $finishedTasks);
            $state = isset($finishedTasks[$task->getId()]) ? $finishedTasks[$task->getId()] : Status::OPEN; //Default is close

            //
            if (empty($userGoalStartDate)) {
                $state = Status::CLOSE;
            }

            if ($state === Status::OPEN) {
                $countOpenTasks++;
            }

            ///Not in the current milestone so set to closed
            if (isset($nextCheckin['milestoneId']) && $task->getMilestone()->getId() !== (int)$nextCheckin['milestoneId']) {

                if ($state !== Status::DONE && $state !== Status::FAILED) {
                    $state = Status::CLOSE;
                }
                $this->milestoneStatus = Status::CLOSE;
            }

            if ($state === Status::OPEN && $countOpenTasks > 1) {
//                $this->milestoneStatus = Status::CLOSE;
                $state = Status::CLOSE;
            }

            $this->tasks[] = new TaskSummary($task, $done, NULL, NULL, $state);
            if (!$done) {
                $allTasksDone = false;
            }
        }

        if ($allTasksDone) {
            $this->milestoneStatus = MilestoneService::STATUS_DONE;
        }

        if (!$allTasksDone && $milestone->getDeadline() < new \DateTime()) {
            $this->milestoneStatus = MilestoneService::STATUS_FAILED;
        }
    }

    /**
     * Calculate Milestone deadline on startdate of User Goal
     *
     * @param Milestone $milestone
     * @param \DateTime $userGoalStartDate
     *
     * @return \DateTime
     */
    private function calculateMilestoneDeadline(Milestone $milestone, \DateTime $userGoalStartDate)
    {
        if ($milestone->getRecurrence() === 'weeks') {
            $plusDate = $milestone->getDuration() * 7;

            return $userGoalStartDate->modify('+' . $plusDate . ' days');
        } else {
            $plusDate = $milestone->getDuration();

            return $userGoalStartDate->modify('+' . $plusDate . ' month');
        }
    }
}