<?php
/**
 * Created by PhpStorm.
 * User: ngeleedst
 * Date: 14/10/16
 * Time: 16:28
 */

namespace AppBundle\Command;

use AppBundle\Entity\UserGoal;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GoalCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('app:goal:fail')
            ->setDescription('Handle the failed goals');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Goal check command started.');
	    $em = $this->getContainer()->get('doctrine.orm.entity_manager');

	    $failedUserGoals = $em->getRepository('AppBundle:UserGoal')->getAllFailedGoals();

        $logger = $this->getContainer()->get('logger');

        $logMessage = 'Found ' . count($failedUserGoals) . ' failed usergoals';
	    $output->writeln($logMessage);
	    $logger->info('************* HANDLING FAILED USERGOALS *************');
        $logger->info($logMessage);

	    $handledGoals = 0;
	    /** @var UserGoal $failedGoal */
        foreach ($failedUserGoals as $failedGoal){
            try {
	            $this->getContainer()->get('app.service.user_goal')->handleFailedGoal($failedGoal);
	            $logger->info('Successfully handled failed user goad with id ' . $failedGoal->getId());
	            $handledGoals++;
	        }  catch (\Exception $e) {
	            $logger->error(
                    sprintf(
                        'Failed handling goal %u: %s',
                        $failedGoal->getId(),
                        $e->getMessage()
                    )
                );
	        }
        }
	    $output->writeln('<info>Done, handled ' . $handledGoals . ' failed usergoals</info>');
        $logger->info('Done, handled ' . $handledGoals . ' failed usergoals');
        $logger->info('************* END HANDLING FAILED USERGOALS *************');
    }
}