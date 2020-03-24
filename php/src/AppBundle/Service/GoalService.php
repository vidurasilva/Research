<?php
/**
 * Created by PhpStorm.
 * User: mdubbelman
 * Date: 9/26/16
 * Time: 2:11 PM
 */

namespace AppBundle\Service;


use AppBundle\Entity\Goal as GoalEntity;
use AppBundle\Entity\Goal;
use AppBundle\Entity\Milestone;
use AppBundle\Entity\Task;
use AppBundle\Entity\UserGoalImage;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use AppBundle\Entity\UserGoal as UserGoalEntity;

/**
 * Class Goal
 *
 * @package AppBundle\Service
 */
class GoalService
{

    const DAYS_MONTH = 31;

    const GOAL_UPLOAD_DIR = 'goals';

    /**
     * @var MailerService
     */
    private $mailerService;

    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var
     */
    private $uploadPath;

    /**
     * GoalService constructor.
     *
     * @param \AppBundle\Service\MailerService $mailerService
     * @param \Doctrine\ORM\EntityManager $entityManager
     * @param $uploadPath
     */
    public function __construct(
      MailerService $mailerService,
      EntityManager $entityManager,
      $uploadPath
    ) {
        $this->mailerService = $mailerService;
        $this->entityManager = $entityManager;
        $this->uploadPath = $uploadPath;
    }

    /**
     * @param \AppBundle\Entity\UserGoal $userGoal
     * @param UploadedFile|null $image
     */
    public function saveUserGoal(
      UserGoalEntity $userGoal,
      UploadedFile $image = null
    ) {
        //If object has no id it's new, so follow these operations only for new objects
        if (!$userGoal->getId()) {
            $this->setUserGoalDates($userGoal);
            $this->entityManager->persist($userGoal);
        }

        $this->entityManager->flush();

        // only set the image if we have successfully saved the usergoal
        if ($image) {
            $this->setUserGoalImage($userGoal, $image);
        }

        $this->setUserGoalStatus($userGoal);

        //Send mail to supervisor after goal is set to active
        if ($userGoal->getSuperVisor() && $userGoal->getActive()) {
            $this->mailSuperVisor($userGoal);
        }

        $this->entityManager->flush();
    }

    /**
     * @param \AppBundle\Entity\UserGoal $userGoal
     * @param UploadedFile|null $image
     */
    protected function setUserGoalImage(UserGoalEntity $userGoal, $image)
    {
        $this->deleteCurrentImages($userGoal);
        $userGoalImage = new UserGoalImage();
        $userGoalImage->setOriginalName($image->getClientOriginalName());
        $fileName = md5(uniqid()) . '.' . $image->guessExtension();
        $image->move($this->uploadPath . '/' . self::GOAL_UPLOAD_DIR,
          $fileName);
        $userGoalImage->setImage($fileName);
        $userGoalImage->setUserGoal($userGoal);
        $this->entityManager->persist($userGoalImage);
        $this->entityManager->flush();
        // add it to the collection, otherwise it will only be available when we load the UserGoal from the db
        $userGoal->addUserGoalImage($userGoalImage);
    }

    /**
     * @param \AppBundle\Entity\UserGoal $userGoal
     */
    protected function setUserGoalStatus(UserGoalEntity $userGoal)
    {
        $hasStake = $userGoal->getStakeAmount() > 0 ? true : false;
        $hasImageUpload = $userGoal->getUserGoalImages()
          ->count() !== 0 ? true : false;

        // if we have a supervisor the goal is always active
        if ($userGoal->getSuperVisor()) {
            $userGoal->setActive(true);
        }

        // If has stake + image upload both conditions needs to be correct if so set active
        if ($hasStake && $hasImageUpload) {
            $userGoal->setActive($this->getPaymentStatus($userGoal));
        }

        //Only has image upload and payment is failed
        if ($hasImageUpload && !$hasStake) {
            $userGoal->setActive(true);
        }

        //Only has stake and not an upload
        if ($hasStake && !$hasImageUpload) {
            $userGoal->setActive($this->getPaymentStatus($userGoal));
        }
    }

    /**
     * @param \AppBundle\Entity\UserGoal $userGoal
     *
     * @return bool
     */
    protected function getPaymentStatus(UserGoalEntity $userGoal)
    {
        if ($userGoal->getPaymentToken()) {
            return true;
        }

        return false;
    }

    /**
     * @param \AppBundle\Entity\UserGoal $userGoal
     */
    protected function setUserGoalDates(UserGoalEntity $userGoal)
    {
        /** @var Goal $goal */
        $goal = $userGoal->getGoal();

        if (!$userGoal->getStartDate()) {

            $goalStartDate = $goal->getStartDate();

            //Get start date from Goal entity for custom goals
            if (!empty($goalStartDate)) {
                $userGoal->setStartDate($goalStartDate);
            } else {
                $userGoal->setStartDate(new \DateTime());
            }
        }

        if (!$userGoal->getEndDate()) {
            $userGoal->setEndDate($this->calculateUserGoalEndDate($userGoal));
        }

        if (!$userGoal->getDeadline()) {
            if ($goal->getDeadline()) {
                $userGoal->setDeadline($userGoal->getDeadline());
            } else {
                $userGoal->setDeadline($this->calculateUserGoalEndDate($userGoal));
            }
        }

        if (!$userGoal->getStartDate()) {
            $userGoal->setStartDate(new \DateTime());
        }

        if (!$userGoal->getEndDate()) {
            $userGoal->setEndDate($this->calculateUserGoalEndDate($userGoal));
        }

        if (!$userGoal->getDeadline()) {

            if ($goal->getDeadline()) {

                $userGoal->setDeadline($userGoal->getDeadline());

                //Custom goal so set deadline based on the custom goal deadline
                if (!$goal->getPredefined() && empty($goal->getOriginGoal())) {
                    $userGoal->setDeadline($goal->getDeadline());
                }

            } else {
                $userGoal->setDeadline($this->calculateUserGoalEndDate($userGoal));
            }
        }
    }

    /**
     * @param \AppBundle\Entity\UserGoal $userGoal
     */
    public function mailSuperVisor(UserGoalEntity $userGoal)
    {
        //Check if supervisor already notified
        if (!$userGoal->isSupervisorNotified()) {
            $this->mailerService->sendToEmail(

              $userGoal->getSuperVisor(),

              $userGoal->getUser()
                ->getFirstname() . ' added your as a Supervisor',

              ':email:supervisor_added.html.twig',

              [

                'name' => $userGoal->getUser()->getFirstname(),

                'firstname' => $userGoal->getUser()->getFirstname(),

                'lastname' => $userGoal->getUser()->getLastname(),

                'user' => $userGoal->getUser()->getFirstname(),

                'goal' => $userGoal->getGoal()->getTitle(),

                'deadline' => $userGoal->getDeadline(),

                'hasMoneyStake' => $userGoal->getStakeAmount() > 0,

                'stakes' => $userGoal->getStakeAmount(),

                'stakeCurrency' => $userGoal->getCurrency(),

                'hasImageStake' => $userGoal->getUserGoalImages()->count() > 0,

              ]);

            $userGoal->setSupervisorNotified(true);
        }
    }

    /**
     * @param \AppBundle\Entity\UserGoal $userGoal
     */
    protected function deleteCurrentImages(UserGoalEntity $userGoal)
    {
        foreach ($userGoal->getUserGoalImages() as $goalImage) {
            $file = $this->uploadPath . '/' . self::GOAL_UPLOAD_DIR . '/' . $goalImage->getImage();
            if (file_exists($file)) {
                unlink($file);
            }
            $this->entityManager->remove($goalImage);
        }
    }

    /**
     * @param \AppBundle\Entity\UserGoal $userGoal
     *
     * @return null|string
     */
    public function getTotalAmountForUserGoal(UserGoalEntity $userGoal)
    {
        $amount = null;
        if ($userGoal->getGlobalStake()) {
            $amount = $userGoal->getStakeAmount();
        } else {
            $amount = $userGoal->getStakeAmount() * $userGoal->getMaximumFails();
        }

        return $amount;
    }


    /**
     * Calculate the duration of the goal
     *
     * @param GoalEntity $goal
     *
     * @return int
     */
    public function getGoalDuration(GoalEntity $goal)
    {
        $dateDiff = $goal->getDeadline()->diff($goal->getStartDate());

        if ($goal->getRecurrence() === 'week') {
            $duration = (int)ceil($dateDiff->days / 7);
        } else {
            $duration = (int)ceil($dateDiff->days / self::DAYS_MONTH);
        }

        return $duration;
    }

    /**
     * Generate Milestones for Custom Goal
     *
     * @param EntityManager $em
     * @param GoalEntity $goal
     * @param bool $persist
     *
     * @return \AppBundle\Entity\Milestone[]
     */
    public function generateGoalMileStones(
      EntityManager $em,
      GoalEntity $goal,
      $persist = true
    ) {
        $mileStoneDeadline = clone $goal->getStartDate();
        $duration = $this->getGoalDuration($goal);

        for ($milestoneCount = 1; $milestoneCount <= $duration; $milestoneCount++) {
            $mileStoneDeadline->modify('+1 ' . $goal->getRecurrence());

            $mileStone = new Milestone();
            $mileStone->setTitle('Milestone ' . $milestoneCount);
            $mileStone->setGoal($goal);
            $mileStone->setNumber($milestoneCount);
            $clonedDeadline = clone $mileStoneDeadline;
            $mileStone->setDeadline($clonedDeadline);
            $mileStone->setStartDate($clonedDeadline);

            if ($persist) {
                $em->persist($mileStone);
            }
            $this->generateMileStoneTasks($em, $goal, $mileStone, $persist);
            $goal->addMilestone($mileStone);
        }

        if ($persist) {
            $em->persist($goal);
            $em->flush();
        }

        return $goal->getMilestones()->toArray();
    }

    /**
     * Generate Tasks for Milestones from a Custom Goal
     *
     * @param EntityManager $em
     * @param GoalEntity $goal
     * @param Milestone $mileStone
     * @param bool $persist
     *
     * @return void
     */
    public function generateMileStoneTasks(
      EntityManager $em,
      GoalEntity $goal,
      Milestone $mileStone,
      $persist = true
    ) {
        for ($i = 1; $i <= $goal->getIteration(); $i++) {
            $task = new Task();

            $task->setTitle($goal->getDescription());
            //$task->setDescription();
            $task->setMilestone($mileStone);
            $task->setPosition($i);
            $task->setPoints($goal->getPoints());

            if ($persist) {
                $em->persist($task);
            }

            $mileStone->addTask($task);
        }

        if ($persist) {
            $em->persist($mileStone);
        }
    }

    /**
     * @param \AppBundle\Entity\UserGoal $userGoal
     *
     * @return \DateTime
     */
    protected function calculateUserGoalEndDate(UserGoalEntity $userGoal)
    {
        $endDate = clone $userGoal->getStartDate();
        $i = 0;

        /** @var Milestone $milestone */
        foreach ($userGoal->getGoal()->getMilestones() as $milestone) {

            //Add week(s)/month(s)
            if (null !== $milestone->getDuration() && null !== $milestone->getRecurrence() && $milestone->getDeadline() !== null) {
                $startDate = clone $endDate;
                $endDate->modify(sprintf('+%d %s', $milestone->getDuration(),
                  $milestone->getRecurrence()));
            } elseif (null !== $milestone->getDuration() && null !== $milestone->getRecurrence()) {
                $startDate = clone $endDate;
                $endDate->modify(sprintf('+%d %s', $milestone->getDuration(),
                  $milestone->getRecurrence()));
            } else {
                $endDate = $milestone->getDeadline();
                $startDate = clone $endDate;
                $startDate->modify(Milestone::DEFAULT_MILESTONE_DURATION); //Modify to default by 1 week.
            }

            $this->updateMilestoneDates($milestone, $startDate, $endDate,
              $i); //Update milestone dates

            $i++;
        }

        return $endDate;

    }

    /**
     * @param Milestone $milestone
     * @param \DateTime $endDate
     */
    protected function updateMilestoneDates(
      Milestone $milestone,
      \DateTime $startDate,
      \DateTime $endDate,
      $iteration = 0
    ) {
        $deadline = clone $endDate;

        $milestone->setStartDate($startDate);
        $milestone->setDeadline($deadline);

        unset($deadline);
    }

    /**
     * @param Goal $originGoal
     *
     * @return Goal
     */
    public function clonePredefinedGoal(Goal $originGoal)
    {
        if ($originGoal->getPredefined() || $originGoal->getDeadline() == null) {
            //Copy predefined goals so it's easier to set milestone, task deadlines.
            $goal = clone $originGoal;

            $goal->setPredefined(false); // Predefined goals are created in the cms.
            $goal->setOriginGoal($originGoal); //Set reference to old goal

            $this->entityManager->detach($goal);
            $this->entityManager->persist($goal);
            $this->entityManager->flush();

            return $goal;
        }

        return $originGoal;
    }
}