<?php


namespace AppBundle\Service;


use AppBundle\Entity\Goal;
use AppBundle\Entity\GoalGroup;
use AppBundle\Entity\Score;
use AppBundle\Entity\Task;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use UserBundle\Entity\User;

class ScoreService
{

    const DEFAULT_SCORE = 0;

    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * ScoreService constructor.
     *
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }


    /**
     * @param User $user
     * @param Goal $goal
     * @param Task $task
     * @param GoalGroup|null $goalGroup
     * @param bool $addition
     */
    public function setGoalScore(
      User $user,
      Goal $goal,
      Task $task,
      GoalGroup $goalGroup = null,
      $addition = true
    ) {
        $repo = $this->entityManager->getRepository('AppBundle:Score');

        // At the goal use api the original goal is copied, so in case of scores we need to get the original one.
        if ($goal->getOriginGoal()) {
            $goal = $goal->getOriginGoal();
        }

        if ($task->getPoints() > 0 && $goalGroup == null) {

            $score = $repo->findOneBy([
              'user' => $user,
              'goal' => $goal,
              'group_goal' => $goalGroup,
            ]);

            if ($score instanceof Score) {
                $newScore = $this->calculateNewScore($score->getScore(),
                  $task->getPoints(), $addition);
                $score->setScore($newScore);
            } else {
                $score = new Score();
                $score->setScore($task->getPoints());
                $score->setGoal($goal);
                $score->setUser($user);
                $score->setGroupGoal(null);
            }


            $this->entityManager->persist($score);
            $this->entityManager->flush();
        }

        //Different point calculation for group goals
        if ($goalGroup !== null) {

            if (!$goalGroup instanceof GoalGroup) {
                throw new AccessDeniedHttpException('Not a group goal instance.');
            }

            $score = $repo->findOneBy([
              'user' => $user,
              'goal' => $goal,
              'group_goal' => $goalGroup,
            ]);

            if ($score instanceof Score) {
                $newScore = $this->calculateNewScore($score->getScore(),
                  $task->getPoints(), $addition);
                $score->setScore($newScore);
            } else {
                $score = new Score();
                $score->setScore(!empty($task->getPoints()) ? $task->getPoints() : self::DEFAULT_SCORE);
                $score->setGoal($goal);
                $score->setUser($user);
                $score->setGroupGoal($goalGroup);
            }

            $this->entityManager->persist($score);
            $this->entityManager->flush();
        }
    }

    /**
     * Calculate score based on addition otherwise subtract
     *
     * @param $score
     * @param $points
     * @param $addition
     *
     * @return int
     */
    protected function calculateNewScore($score, $points, $addition)
    {
        if ($addition) {
            return (int)$score + $points;
        } else {
            return (int)$score - $points;
        }
    }
}