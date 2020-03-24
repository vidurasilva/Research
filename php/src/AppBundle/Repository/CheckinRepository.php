<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Checkin;
use AppBundle\Entity\Milestone;
use AppBundle\Entity\Task;
use AppBundle\Helper\UserGoalHelper;
use AppBundle\Model\Status;
use AppBundle\Service\MilestoneService;
use UserBundle\Entity\User;
use Doctrine\DBAL\Connection;
use AppBundle\Entity\UserGoal;

/**
 * Class CheckinRepository
 *
 * @package AppBundle\Repository
 */
class CheckinRepository extends \Doctrine\ORM\EntityRepository
{

    const COOLDOWN_TIME = '+2 days';

    /**
     * Get checkins for the given date
     *
     * @param User $user
     * @param \DateTime $dateTime
     *
     * @return array
     */
    public function getCheckinsForDate(User $user, \DateTime $dateTime)
    {
        //Fetch the goal ids for the current user.
        $userGoals = $this->findActiveUserGoalIds($user);
        $checkins = [];
        $now = new \DateTime('now');

        foreach ($userGoals as $userGoal) {

            $result = $this->getCheckinDataForUserGoal($userGoal, $dateTime);

            if (!empty($result)) {
                /** @var Task $task */
                $task = $this->getEntityManager()
                  ->getRepository('AppBundle:Task')
                  ->find((int)$result['taskId']);
                $userGoal = $this->getEntityManager()
                  ->getRepository('AppBundle:UserGoal')
                  ->find((int)$result['user_goal_id']);

                $userGoal = $this->updateGoalStatus($userGoal, $task);

                $task->setUnfinishedMilestoneTasks($this->countUnfinishedMilestoneTasks($task,
                  $userGoal));

                //Update global stake goals, because they can fail earlier before deadline
                if ($userGoal->getGlobalStake() && $task !== null) {

                    $updated = $this->updateGlobalGoals($userGoal, $task);

                    if ($updated) {
                        continue;
                    }
                }

                //Check if the next milestone start date is greater than if so show the last checkin task. Otherwise
                //show checkin from table
                if ($task->getMilestone()->getStartDate() > $now) {
                    $lastCheckin = $this->findLastCheckin($userGoal);

                    if (!empty($lastCheckin) && $userGoal->getStatus() === Status::OPEN) {
                        $task = $lastCheckin->getTask();
                        $result['checkinStatus'] = $lastCheckin->getStatus();
                        $result['checkinCreated'] = $lastCheckin->getCreated()
                          ->format('d-m-Y');
                    }
                }

                $checkins[] = [
                  'task' => $task,
                  'userGoal' => $userGoal,
                  'done' => !empty($result['checkinCreated']),
                  'state' => $result['checkinStatus'],
                  'checkinDate' => $result['checkinCreated'] ? new \DateTime($result['checkinCreated']) : null,
                  'description' => $result['description'],
                  'video' => $result['video'],
                  'finishedTasks' => $this->getDoneTasksForUserGoal($userGoal),
                ];
            }
        }

        return $checkins;
    }

    public function findLastCheckin(UserGoal $userGoal)
    {
        return $this->_em->createQuery('SELECT c FROM AppBundle\Entity\Checkin c WHERE c.userGoal = :userGoal ORDER BY c.created DESC')
          ->setParameter('userGoal', $userGoal->getId())
          ->setMaxResults(1)
          ->getOneOrNullResult();
    }

    /**
     * Global goals check, global stake goals can fail earlier before deadline.
     *
     * @param UserGoal $userGoal
     * @param Task $task
     *
     * @return bool
     */
    protected function updateGlobalGoals(UserGoal $userGoal, Task $task)
    {
        $countFailedTask = $this->countFailedTasks($userGoal);
        $milestoneService = new MilestoneService();
        $milestoneDaysLeft = $milestoneService->calculateMileStoneDays($task->getMilestone());
        $unfinishedMilestoneTasks = $task->getUnfinishedMilestoneTasks();

        $status = $milestoneDaysLeft - $unfinishedMilestoneTasks + $userGoal->getMaximumFails() - $countFailedTask;

        if ($status < 0) {
            //Set goal fail
            $this->markAllMissingTasksAsFailed($userGoal);
            $userGoal->setStatus(UserGoal::STATUS_FAILED);

            $this->_em->flush($userGoal);

            return true;
        }

        return false;
    }

    /**
     * Get checkin data such for the given userGoal and optional date
     *
     * @param $userGoal
     * @param \DateTime|null $dateTime
     *
     * @return mixed
     */
    private function getCheckinDataForUserGoal(
      $userGoal,
      \DateTime $dateTime = null
    ) {
        if ($dateTime === null) {
            $dateTime = new \DateTime();
        }

        $sql = 'SELECT task.id as taskId, task.title as taskTitle, task.description, task.video,
							user_goal.id as user_goal_id, user_goal.goal_id as goalId, user_goal.group_id as user_goal_group_id,
							goal.title as goalTitle,
							checkin.created as checkinCreated,
							checkin.status as checkinStatus
					FROM milestone
					INNER JOIN user_goal USING (goal_id)
					INNER JOIN task ON milestone.id = task.`milestone_id`
					LEFT JOIN checkin ON checkin.`task_id` = task.id AND user_goal_id=:user_goal_id
					INNER JOIN goal ON goal.id = user_goal.goal_id 
					WHERE user_goal.id = :user_goal_id
					AND (
						DATE(CONVERT_TZ(checkin.created, :server_timezone, :client_timezone)) >= STR_TO_DATE(:date, "%Y-%m-%d") 
						OR checkin.created IS NULL
					)
					AND DATE(CONVERT_TZ(user_goal.start_date, :server_timezone, :client_timezone)) <= STR_TO_DATE(:start_date, "%Y-%m-%d")
					ORDER BY milestone.number, `position`
					LIMIT 1
					';

        $serverDateTime = new \DateTime();

        $stmt = $this->getEntityManager()->getConnection()->prepare($sql);
        $stmt->bindValue('user_goal_id', $userGoal['id']);
        $stmt->bindValue('date', $dateTime->format('Y-m-d'));
        $stmt->bindValue('start_date', $serverDateTime->format('Y-m-d'));
        $stmt->bindValue('client_timezone', $dateTime->format('P'));
        $stmt->bindValue('server_timezone', $serverDateTime->format('P'));

        $stmt->execute();


        return $stmt->fetch();
    }

    /**
     * Add failed checkins for all failed milestones
     *
     * @param \DateTime $dateTime
     *
     * @return int
     */
    public function markFailedTasks(\DateTime $dateTime)
    {
        //Fetch all active userGoals
        $userGoals = $this->getEntityManager()
          ->getRepository(UserGoal::class)
          ->findActiveUserGoals(true);
        $handledMilestones = 0;

        foreach ($userGoals as $userGoal) {
            $result = $this->getCheckinDataForUserGoal(['id' => $userGoal->getId()],
              $dateTime);

            if (!empty($result)) {
                $task = $this->getEntityManager()
                  ->getRepository('AppBundle:Task')
                  ->find((int)$result['taskId']);
                $userGoal = $this->getEntityManager()
                  ->getRepository('AppBundle:UserGoal')
                  ->find((int)$result['user_goal_id']);
                $userGoal = $this->updateGoalStatus($userGoal);
                $task->setUnfinishedMilestoneTasks($this->countUnfinishedMilestoneTasks($task,
                  $userGoal));

                //If task are over milestone deadline mark as a failed checkin.
                $milestoneDeadline = $task->getMilestone()->getDeadline();
                if ($milestoneDeadline === null && $userGoal->getDeadline()) {
                    $milestoneDeadline = clone $userGoal->getDeadline();
                    $milestoneDeadline->modify('+' . $task->getMilestone()
                        ->getNumber() . $task->getMilestone()->getRecurrence());
                }

                if ($milestoneDeadline < $dateTime) {
                    $this->markTasksAsFailed($userGoal, $task, $dateTime);
                    $handledMilestones++;
                }
            }
        }

        return $handledMilestones;
    }

    /**
     * @param $userGoal UserGoal
     * @param $task Task
     * @param $dateTime \DateTime
     */
    protected function markTasksAsFailed($userGoal, $task, $dateTime)
    {
        $tasks = $this->getEntityManager()
          ->getRepository('AppBundle:Task')
          ->findBy(['milestone' => $task->getMilestone()]);

        $this->createSystemCheckins($userGoal, $tasks, $dateTime);
    }

    /**
     * @param UserGoal $userGoal
     * @param array $tasks
     */
    protected function createSystemCheckins(
      UserGoal $userGoal,
      $tasks = [],
      \DateTime $dateTime
    ) {
        //System checkin
        $yesterday = clone $dateTime;
        $yesterday->modify('-1 day');
        $yesterday->setTime(0, 0, 0);

        foreach ($tasks as $tempTask) {
            $alreadyCompleted = $this->getEntityManager()
              ->getRepository(Checkin::class)
              ->findOneBy([
                'userGoal' => $userGoal,
                'task' => $tempTask,
              ]);

            if ($alreadyCompleted === null) {
                $checkin = new Checkin($userGoal, $tempTask);
                $checkin->setCreated($yesterday);
                $checkin->setStatus(Status::FAILED);
                $this->getEntityManager()->persist($checkin);
            }
        }

        $this->getEntityManager()->flush();
    }

    /**
     * @param User $user
     *
     * @return array
     */
    protected function findActiveUserGoalIds(User $user)
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT goal_id, id 
				FROM user_goal 
				WHERE user_id = ? 
				AND finished=0 
				AND active=1
				AND (
					end_date >= NOW()
					OR
					end_date IS NULL
				)
				ORDER BY start_date
				";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(1, $user->getId());
        $stmt->execute();

        return $stmt->fetchAll();
    }


    /**
     * @param $userGoalId
     * @param $taskId
     * @param \Doctrine\DBAL\Connection $conn
     * @param \DateTime $dateTime
     *
     * @return bool
     */
    public function checkIfCheckInTodayAndUnique(
      $userGoalId,
      $taskId,
      Connection $conn,
      \DateTime $dateTime
    ) {
        if ($this->checkIfCheckinToday($userGoalId, $conn, $dateTime)) {
            return true;
        }

        //Check if task is already in checkin table to prevent cheating
        $sql = "SELECT count(id) FROM checkin WHERE user_goal_id = :userGoalId AND task_id = :taskId";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue('userGoalId', $userGoalId);
        $stmt->bindValue('taskId', $taskId);
        $stmt->execute();

        return (int)$stmt->fetchColumn() > 0;

    }

    /**
     * @param $userGoalId
     * @param \Doctrine\DBAL\Connection $conn
     * @param \DateTime $dateTime
     *
     * @return bool
     */
    protected function checkIfCheckinToday(
      $userGoalId,
      Connection $conn,
      \DateTime $dateTime
    ) {
    	$serverDateTime = new \DateTime();

        $sql = "SELECT count(id) FROM checkin WHERE user_goal_id = :userGoalId AND DATE(CONVERT_TZ(created, :serverTimeZone, :clientTimeZone)) = DATE(:clientDate) ";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue('userGoalId', $userGoalId);
        $stmt->bindValue('serverTimeZone', $serverDateTime->format('P'));
        $stmt->bindValue('clientTimeZone', $dateTime->format('P'));
        $stmt->bindValue('clientDate', $dateTime->format('Y-m-d'));
        $stmt->execute();

        return (int)$stmt->fetchColumn() > 0;
    }

    public function getDoneTasksForUserGoal(UserGoal $userGoal)
    {
        $sql = 'SELECT task_id, status FROM checkin WHERE user_goal_id=:user_goal_id';
        $stmt = $this->_em->getConnection()->prepare($sql);
        $stmt->bindValue('user_goal_id', $userGoal->getId());
        $stmt->execute();
        $tasks = [];
        foreach ($stmt->fetchAll() as $result) {
            $tasks[$result['task_id']] = $result['status'];
        }

        return $tasks;
    }

    /**
     * @param UserGoal $userGoal
     *
     * @return int
     */
    public function countDoneTasksForUserGoal(UserGoal $userGoal)
    {
        $sql = 'SELECT count(id) as checkin_count FROM checkin WHERE user_goal_id=:user_goal_id AND status =:status AND approved =:approved';
        $stmt = $this->_em->getConnection()->prepare($sql);
        $stmt->bindValue('user_goal_id', $userGoal->getId());
        $stmt->bindValue('status', Checkin::CHECKIN_DONE);
        $stmt->bindValue('approved', 1);
        $stmt->execute();

        return (int)$stmt->fetchColumn(0);
    }

    /**
     * @param UserGoal $userGoal
     *
     * @return int
     */
    public function countAllCheckinsForUserGoal(UserGoal $userGoal)
    {
        $sql = 'SELECT count(id) as checkin_count FROM checkin WHERE user_goal_id=:user_goal_id';
        $stmt = $this->_em->getConnection()->prepare($sql);
        $stmt->bindValue('user_goal_id', $userGoal->getId());
        $stmt->execute();

        return (int)$stmt->fetchColumn(0);
    }

    /**
     * @param UserGoal $userGoal
     *
     * @return int
     */
    public function countFailedTasksForUserGoal(UserGoal $userGoal)
    {
        $sql = 'SELECT count(id) as checkin_count FROM checkin WHERE user_goal_id=:user_goal_id AND status =:status';
        $stmt = $this->_em->getConnection()->prepare($sql);
        $stmt->bindValue('user_goal_id', $userGoal->getId());
        $stmt->bindValue('status', Checkin::CHECKIN_FAILED);
        $stmt->execute();

        return (int)$stmt->fetchColumn(0);
    }

    /**
     * @param Task $task
     * @param UserGoal $userGoal
     *
     * @return int
     */
    public function countUnfinishedMilestoneTasks(
      Task $task,
      UserGoal $userGoal
    ) {
        $openCheckinsCurrentMilestone = $this->_em->getRepository('AppBundle:Checkin')
          ->findOpenCheckinsCurrentMilestone($userGoal, $task);

        return $openCheckinsCurrentMilestone;
    }

    /**
     * @param UserGoal $userGoal
     * @param Task $task
     *
     * @return int
     */
    public function findOpenCheckinsCurrentMilestone(
      UserGoal $userGoal,
      Task $task
    ) {
        $sql = 'SELECT id FROM task WHERE milestone_id=:milestoneId';
        $stmt = $this->_em->getConnection()->prepare($sql);
        $stmt->bindValue('milestoneId', $task->getMilestone()->getId());
        $stmt->execute();
        $taskIds = [];
        foreach ($stmt->fetchAll() as $result) {
            $taskIds[] = $result['id'];
        }

        $totalTasks = count($taskIds);

        $result = $this->_em->createQueryBuilder()
          ->select('count(c.id)')
          ->from('AppBundle:Checkin', 'c')
          ->where('c.userGoal =:userGoal')
          ->andWhere('c.task IN (:taskIds)')
          ->setParameter('userGoal', $userGoal)
          ->setParameter('taskIds', $taskIds)
          ->getQuery()
          ->getSingleScalarResult();

        $totalCheckins = (int)$result;

        return $totalTasks - $totalCheckins;
    }

    public function findNextUserGoalCheckin(
      UserGoal $userGoal,
      \DateTime $dateTime
    ) {
        $sql = 'SELECT task.id as taskId, task.title as taskTitle, task.description, task.video,
							user_goal.id as user_goal_id, user_goal.goal_id as goalId, user_goal.group_id as user_goal_group_id,
							goal.title as goalTitle,
							checkin.created as checkinCreated,
							checkin.status as checkinStatus,
							milestone.id as milestoneId
					FROM milestone
					INNER JOIN user_goal USING (goal_id)
					INNER JOIN task ON milestone.id = task.`milestone_id`
					LEFT JOIN checkin ON checkin.`task_id` = task.id AND user_goal_id=:user_goal_id
					INNER JOIN goal ON goal.id = user_goal.goal_id 
					WHERE user_goal.id = :user_goal_id
					AND (
						DATE(checkin.created) >=  STR_TO_DATE(:date, "%Y-%m-%d") 
						OR checkin.created IS NULL
					)
					ORDER BY milestone.number, `position`
					LIMIT 1
					';

        $stmt = $this->_em->getConnection()->prepare($sql);
        $stmt->bindValue('user_goal_id', $userGoal->getId());
        $stmt->bindValue('date', $dateTime->format('Y-m-d'));
        $stmt->execute();

        $result = $stmt->fetchAll();

        return !empty($result) ? current($result) : null;
    }

    /**
     * @param UserGoal $userGoal
     *
     * @return UserGoal
     */
    public function updateGoalStatus(UserGoal $userGoal, $task = null)
    {
        $now = new \DateTime('now');
        /** @var CheckinRepository $checkinRepository */
        $checkinRepository = $this->_em->getRepository('AppBundle:Checkin');

        $totalGoalTasks = 0;
        $currentGoalCheckins = (int)$checkinRepository->countDoneTasksForUserGoal($userGoal);
        $failedGoalCheckins = (int)$checkinRepository->countFailedTasksForUserGoal($userGoal);

        $totalCheckins = $currentGoalCheckins + $failedGoalCheckins;

        if (!empty($milestones = $userGoal->getGoal()->getMilestones())) {
            /** @var Milestone $milestone */
            foreach ($milestones as $milestone) {
                $totalGoalTasks = $totalGoalTasks + (int)$milestone->getTasks()
                    ->count();
            }
        }

        //All task are done
        if ($totalCheckins === $totalGoalTasks) {
            if ($failedGoalCheckins <= $userGoal->getMaximumFails()) {
                $userGoal->setStatus(UserGoal::STATUS_COMPLETED);
                $userGoal->setFinished(true);
            } else {
                $userGoal->setStatus(UserGoal::STATUS_FAILED);
            }
        }

        //Mark goal with global stake as failed if maximumFails is exceeded
        if ($userGoal->getGlobalStake() && $failedGoalCheckins > $userGoal->getMaximumFails()) {
            //$this->markAllMissingTasksAsFailed($userGoal);
            $userGoal->setStatus(UserGoal::STATUS_FAILED);
        }

        //User goal is expired set to in-active, this is also done in the mark-failed goals command...
        if ($now > $userGoal->getEndDate()) {
            //$userGoal->setStatus(UserGoal::STATUS_FAILED);
        }

        $this->_em->flush($userGoal);

        return $userGoal;
    }

    /**
     * Calculate failed tasks
     *
     * @param UserGoal $userGoal
     *
     * @return int
     */
    public function countFailedTasks(UserGoal $userGoal)
    {
        $repo = $this->_em->getRepository(Checkin::class);

        $checkedInTasksCount = $repo->findBy([
          'userGoal' => $userGoal,
          'status' => Checkin::CHECKIN_FAILED,
        ]);

        return count($checkedInTasksCount);
    }

    /**
     * @param UserGoal $userGoal
     */
    public function markAllMissingTasksAsFailed(UserGoal $userGoal)
    {
        $allTasks = $this->_em->getRepository('AppBundle:Task')
          ->findAllTasksForGoal($userGoal->getGoal());

        $this->createSystemCheckins($userGoal, $allTasks, new \DateTime('NOW'));
    }

}
