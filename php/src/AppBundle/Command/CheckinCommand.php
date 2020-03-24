<?php
/**
 * Created by PhpStorm.
 * User: ngeleedst
 * Date: 14/10/16
 * Time: 16:28
 */

namespace AppBundle\Command;

use AppBundle\Entity\SupervisorCheckinTokens;
use AppBundle\Entity\Task;
use AppBundle\Entity\UserGoal;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * This command sends batches of checkin approvals when all the task from a milestone are in the checkin table.
 * @todo: add an extra check that sends the approvals when not all checkins are added tot the checkin table.
 * Class CheckinCommand
 * @package AppBundle\Command
 */
class CheckinCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('app:mail:supervisor')
            ->setDescription('Send pending checkins to supervisor.');
    }

    /** @var EntityManager $em */
    protected $em;

    /**
     * @var int
     */
    protected $invitesSended = 0;

	/**
	 * @param InputInterface $input
	 * @param OutputInterface $output
	 * @return int|null|void
	 */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $data = [];
        $output->writeln('Start sending pending checkins to supervisors.');

        $this->em = $this->getContainer()->get('doctrine.orm.entity_manager');

        $pendingCheckins = $this->em->getRepository('AppBundle:SupervisorCheckinTokens')->findApprovals();

        /** @var SupervisorCheckinTokens $pendingCheckin */
        foreach ($pendingCheckins as $pendingCheckin) {

        	/** Only send approval mail to supervisor if all tasks of given milestone are finished */
            if ($this->checkIfMilestoneIsFinished($pendingCheckin->getUserGoal(), $pendingCheckin->getTask())) {
                $data[$pendingCheckin->getUserGoal()->getId()][] = $pendingCheckin;
            }
        }

		$handledPendingCheckins = 0;
        foreach ($data as $item) {
            $superVisorEmail = $item[0]->getUserGoal()->getSuperVisor();

            $result = $this->getContainer()->get('app.service.mailer')->sendCheckinListToSupervisor($superVisorEmail, 'Accept user checkins', ':email:supervisor_checkin_list_check.html.twig', ['item' => $item]);

            /**
             * Loop trough superVisorCheckin items.
             * @var SupervisorCheckinTokens $superVisorCheckin
             */
            foreach ($item as $superVisorCheckin) {
                $attemps = $superVisorCheckin->getMailAttemps() + 1;
                $superVisorCheckin->setMailAttemps($attemps);

                if ($result) {
                    $superVisorCheckin->setStatus(SupervisorCheckinTokens::MAIL_SUCCESS);
                }

            }

            $handledPendingCheckins++;
        }

        $this->em->flush();
        $output->writeln('Pending checkins command ended, sent ' . $handledPendingCheckins . ' mails');
    }

    /**
     * Check if milestone is finished, if so send mails to supervisor.
     * @param UserGoal $userGoal
     * @param Task $task
     * @return bool
     */
    protected function checkIfMilestoneIsFinished(UserGoal $userGoal, Task $task)
    {
        $totalMilestoneTasks         = $task->getMilestone()->getTasks()->count();
        $totalFinishedMilestoneTasks = $this->countTotalFinishedTasksMilestone($userGoal, $task->getMilestone()->getTasks());

        if ($totalFinishedMilestoneTasks == $totalMilestoneTasks) {
            return true;
        }

        return false;
    }

    /**
     * Note: count checkins for user goal and tasks.
     * @param UserGoal $userGoal
     * @param $tasks
     * @return int
     */
    protected function countTotalFinishedTasksMilestone(UserGoal $userGoal, $tasks)
    {
    	foreach($tasks as $task){
    		$taskIds[] = $task->getId();
	    }

        return $this->em->createQueryBuilder()
            ->select('COUNT(c.id)')
            ->from('AppBundle:Checkin', 'c')
            ->where('c.userGoal =:userGoalId')
            ->andWhere('c.task IN (:taskIds)')
            ->setParameter('userGoalId', $userGoal->getId())
            ->setParameter('taskIds', $taskIds)
            ->getQuery()->getSingleScalarResult();
    }
}