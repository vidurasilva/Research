<?php
/**
 * Created by PhpStorm.
 * User: mdubbelman
 * Date: 9/26/16
 * Time: 2:11 PM
 */

namespace AppBundle\Service;


use AppBundle\Entity\Checkin;
use AppBundle\Entity\Milestone;
use AppBundle\Entity\PaymentCharge;
use AppBundle\Entity\Task;
use AppBundle\Entity\UserGoal as UserGoalEntity;
use AppBundle\Entity\UserGoalImage;
use AppBundle\Helper\UserGoalHelper;
use AppBundle\Model\Status;
use AppBundle\Repository\CheckinRepository;
use Doctrine\ORM\EntityManager;

class UserGoal
{
	const MAX_TRANSACTION_ATTEMPTS = 3;

	/**
	 * @var EntityManager
	 */
	private $entityManager;
	/**
	 * @var StripePayment
	 */
	private $stripePaymentService;

	/**
	 * @var MailerService
	 */
	private $mailService;

	/**
	 * @var string
	 */
	protected $failedGoalsEmail;

	/**
	 * @var string
	 */
	protected $cooldownTime;


	public function __construct(
		EntityManager $entityManager,
		StripePayment $stripePaymentService,
		MailerService $mailerService,
		$failedGoalsEmail,
		$cooldownTime
	) {
		$this->entityManager        = $entityManager;
		$this->stripePaymentService = $stripePaymentService;
		$this->mailService          = $mailerService;
		$this->failedGoalsEmail     = $failedGoalsEmail;
		$this->cooldownTime         = $cooldownTime;
	}

	/**
	 * @param UserGoalEntity $userGoal
	 * @return PaymentCharge
	 * @throws \Exception
	 */
	public function charge(UserGoalEntity $userGoal)
	{
		if ($userGoal->getPaymentCharge()) {
			throw new \Exception('Usergoal already paid');
		}

		$user   = $userGoal->getUser();
		$amount = $this->getTotalAmountForUserGoal($userGoal);

		$amount = $this->stripePaymentService->getChargeAmount($amount, $userGoal->getCurrency());

		$paymentCharge = new PaymentCharge($user, $amount, $userGoal);
		$paymentCharge->setCustomer($user->getPaymentUser());
		$source = $userGoal->getPaymentToken() ? $userGoal->getPaymentToken() : '';
		$paymentCharge->setSource($source);
		$paymentCharge->setDescription('Payment of usergoal ' . $userGoal->getId() . ' for ' . $user->getEmail());
		if ($amount > 0) {
			$this->stripePaymentService->createPayment($paymentCharge);
		}

		$this->entityManager->persist($paymentCharge);
		$this->entityManager->flush();

		return $paymentCharge;
	}

	public function getTotalAmountForUserGoal(UserGoalEntity $userGoal)
	{
		$amount = null;
		if ($userGoal->getGlobalStake()) {
			$amount = $userGoal->getStakeAmount();
		} else {
			$failedTaskCount = $this->countFailedTasks($userGoal);
			$count           = $failedTaskCount - $userGoal->getMaximumFails();

			if ($count > 0) {
				$amount = $userGoal->getStakeAmount() * $count;
			} else {
				$amount = 0;
			}
		}

		return $amount;
	}

	public function markDoneTasks(array $checkins)
	{
		foreach ($checkins as $checkin) {
			/** @var UserGoalEntity $userGoal */
			$userGoal  = $checkin['userGoal'];
			$doneTasks = $this->getDoneTasksForUserGoal($userGoal);

			/** @var Milestone $milestone */
			foreach ($userGoal->getGoal()->getMilestones() as $milestone) {
				/** @var Task $task */
				foreach ($milestone->getTasks() as $task) {
					if (in_array($task->getId(), $doneTasks)) {
						//$task->set
					}
				}
			}
		}
	}

	/**
	 * Calculate failed tasks
	 *
	 * @param UserGoalEntity $userGoal
	 *
	 * @return int
	 */
	public function countFailedTasks(UserGoalEntity $userGoal)
	{
		$repo = $this->entityManager->getRepository(Checkin::class);

		$checkedInTasksCount = $repo->findBy([
			'userGoal' => $userGoal,
			'status'   => Checkin::CHECKIN_FAILED
		]);

		return count($checkedInTasksCount);
	}

	/**
	 * Get the total checkin count
	 *
	 * @param UserGoalEntity $userGoal
	 * @return int
	 */
	private function getTotalCheckedInTaskCount(UserGoalEntity $userGoal)
	{
		$repo = $this->entityManager->getRepository('AppBundle:Checkin');

		$checkedInTasks = $repo->findBy([
			'userGoal' => $userGoal,
			'status'   => Status::DONE
		]);

		return count($checkedInTasks);
	}

	/**
	 * Get the total task count
	 *
	 * @param UserGoalEntity $userGoal
	 * @return int
	 */
	private function getTotalTaskCount(UserGoalEntity $userGoal)
	{
		$totalTasks = 0;
		/** @var Milestone $mileStone */
		foreach ($userGoal->getGoal()->getMilestones() as $mileStone) {
			$totalTasks += count($mileStone->getTasks());
		}

		return $totalTasks;
	}

	/**
	 * This function handles the failed goal, it can mail an embarrassing picture and/or charge the user
	 *
	 * @param UserGoalEntity $userGoal
	 * @throws \Exception
	 */
	public function handleFailedGoal(UserGoalEntity $userGoal)
	{
		if ($userGoal->getStatus() != UserGoalEntity::STATUS_FAILED) {
			throw new \Exception('Status of the usergoal is not ' . UserGoalEntity::STATUS_FAILED);
		}

		if ($userGoal->getTransactionAttempts() >= self::MAX_TRANSACTION_ATTEMPTS) {
			throw new \Exception('Max transaction attempts reached, skipping');
		}

		$allDone = true;
		// we need to pay out
		if ($userGoal->getStakeAmount() && !$userGoal->getPaymentCharge()) {
			try {
				$this->charge($userGoal);
			} catch (\Exception $e) {
				$allDone = false;
			}
		}

		// we need to mail the embarrassing picture and keep track if it is already sended.
		if (count($userGoal->getUserGoalImages()) > 0) {

			$embarrassingImagesCount = $this->entityManager->getRepository('AppBundle:UserGoalImage')->countNotSendedEmbarrassingImages($userGoal->getId());

			if ($embarrassingImagesCount > 0) {

				if ($this->mailEmbarrassingPicture($userGoal)) {
					//Update all queue images in table directly, user goal images is always one maybe move to user goal table later
					$this->entityManager->getRepository('AppBundle:UserGoalImage')->updateAllSendedEmbarrassingImages($userGoal->getId());
				}
			}
		}

		//Check if all transactions are finished.
		if ($allDone) {
			$userGoal->setActive(false);
		}

		$userGoal->setTransactionAttempts($userGoal->getTransactionAttempts() + 1);
		$this->entityManager->flush();
	}

	/**
	 * Mark user goals als COMPLETED or FAILED, also check if user goal doesn't have open approvals within cooldowntime
	 * @param UserGoalEntity $userGoal
	 * @return bool
	 */
	public function markAsFailedOrSucceeded(UserGoalEntity $userGoal)
	{
		if ($this->checkOpenSupervisorApprovalsAndWithinCooldown($userGoal)) {
			return false;
		}

		$this->entityManager->getRepository('AppBundle:Checkin')->markAllMissingTasksAsFailed($userGoal); //Create system checkins for missing user checkins

		if ($this->getTotalCheckedInTaskCount($userGoal) + $userGoal->getMaximumFails() >= $this->getTotalTaskCount($userGoal)) {
			$userGoal->setStatus(UserGoalEntity::STATUS_COMPLETED);
			$userGoal->setFinished(true);
		} else {
			$userGoal->setStatus(UserGoalEntity::STATUS_FAILED);
		}

		$this->entityManager->flush();

		return true;
	}

	/**
	 * @param UserGoalEntity $userGoal
	 * @return int
	 */
	public function mailEmbarrassingPicture(UserGoalEntity $userGoal)
	{
		return $this->mailService->sendToEmail(
			$this->failedGoalsEmail,
			'Failed goals embarrassing picture',
			':email:embarrassing_picture.html.twig',
			[
				'user'     => $userGoal->getUser(),
				'goal'     => $userGoal->getGoal(),
				'userGoal' => $userGoal
			]
		);
	}


	/**
	 * Check if user goal has open supervisor approvals and if those are within the cooldowntime
	 * @param \AppBundle\Entity\UserGoal $userGoal
	 * @return bool
	 */
	public function checkOpenSupervisorApprovalsAndWithinCooldown(\AppBundle\Entity\UserGoal $userGoal)
	{
		//Check if user goal has open supervisor tokens with cooldown period of x days
		$checkinTokens   = $this->entityManager->getRepository('AppBundle:SupervisorCheckinTokens')->countOpenSupervisorApprovals($userGoal->getId());
		$cooldownEndDate = $this->getCooldownEndDate($userGoal->getEndDate());
		$now             = new \DateTime('NOW');

		if ($checkinTokens > 0 && $now < $cooldownEndDate) {
			return true;
		}

		return false;
	}

	/**
	 * Generate end date with cooldown time
	 * @param \DateTime $endDate
	 * @return \DateTime
	 */
	public function getCooldownEndDate(\DateTime $endDate)
	{
		$cooldownEndDate = clone $endDate;

		return $cooldownEndDate->modify($this->cooldownTime);
	}

	/**
	 * Find global goals and if succeeded mark as finished before ending user goal
	 */
	public function markGlobalGoals()
	{
		$activeGlobalUserGoals = $this->entityManager->getRepository('AppBundle:UserGoal')->findGlobalActiveUserGoals();
		$i                     = 0;
		foreach ($activeGlobalUserGoals as $activeGlobalUserGoal) {

			if ($this->checkIfGlobalGoalIsFinished($activeGlobalUserGoal)) {
				$i++;
			}
		}

		return $i;
	}

	/**
	 * Global goals can succeed earlier so update user goal that are waiting for supervisor approvals or without supervisor
	 * @param UserGoalEntity $userGoal
	 * @return bool
	 */
	protected function checkIfGlobalGoalIsFinished(UserGoalEntity $userGoal)
	{
		/** @var CheckinRepository $checkinRepo */
		$checkinRepo = $this->entityManager->getRepository('AppBundle:Checkin');

		$checkins   = $checkinRepo->countAllCheckinsForUserGoal($userGoal);
		$totalTasks = $this->entityManager->getRepository('AppBundle:Task')->countAllTasksForGoal($userGoal->getGoal());

		$now         = new \DateTime('NOW');
		$failedTasks = $checkinRepo->countFailedTasks($userGoal);

		//Are all tasks finished
		if ($checkins === $totalTasks && $failedTasks <= $userGoal->getMaximumFails()) {

			//Check if last supervisor approval is outside cooldown period
			if ($userGoal->getSuperVisor()) {

				$lastCheckinDate = $this->entityManager->getRepository('AppBundle:SupervisorCheckinTokens')->getLastSupervisorApprovalDate($userGoal->getId());

				//Get last checkin approval
				if ($lastCheckinDate) {
					$cooldownSupervisorDate = UserGoalHelper::getCooldownEndDate($lastCheckinDate);

					if ($now > $cooldownSupervisorDate) {
						$userGoal->setStatus(UserGoalEntity::STATUS_COMPLETED);
						$userGoal->setFinished(true);

						$this->entityManager->flush($userGoal);

						return true;
					}
				}
			} else {
				//User goal doesn't have a supervisor, but finished global goal
				$userGoal->setStatus(UserGoalEntity::STATUS_COMPLETED);
				$userGoal->setFinished(true);

				$this->entityManager->flush($userGoal);
			}
		}

		return false;
	}
}