<?php
/**
 * Created by PhpStorm.
 * User: ngeleedst
 * Date: 14/10/16
 * Time: 16:28
 */

namespace AppBundle\Command;

use AppBundle\Service\UserGoal;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * This class check if goals need to be marked as failed. It also checks if
 * there aren't open supervisor approvals Class MarkGoalsCommand
 *
 * @package AppBundle\Command
 */
class MarkGoalsCommand extends ContainerAwareCommand
{

    protected function configure()
    {
        $this->setName('app:goal:markgoals')
          ->setDescription('Check what goals need to be marked as failed.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Searching for goals to mark');

        /** @var EntityManager $em */
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        /** @var UserGoal $userGoalService */
        $userGoalService = $this->getContainer()->get('app.service.user_goal');

        $foundGoals = $em->getRepository('AppBundle:UserGoal')
          ->findGoalsToMark();

        $output->writeln('Found ' . count($foundGoals) . ' usergoals');
        $handledGoals = 0;
        foreach ($foundGoals as $goal) {
            try {
                if ($userGoalService->markAsFailedOrSucceeded($goal)) {
                    $handledGoals++;
                } else {
                    $output->writeln('<comment>The user goal with id: ' . $goal->getId() . ' is still within the cooldown period and waiting for supervisor approval(s).</comment>');
                }
            } catch (\Exception $e) {
                $output->writeln('<comment>Handling the goal: ' . $e->getMessage() . '</comment>');
            }
        }

        //Global goals can finish before deadline, check and sum
        $handledGoals = $handledGoals + $userGoalService->markGlobalGoals();

        $output->writeln('<info>Done, marked ' . $handledGoals . ' usergoals</info>');
    }
}