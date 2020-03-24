<?php
/**
 * Created by PhpStorm.
 * User: Matthijs Overboom
 * Date: 5-5-17
 * Time: 15:23
 */

namespace AppBundle\Command;


use AppBundle\Entity\Checkin;
use AppBundle\Entity\Milestone;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MarkFailedMilestonesCommand extends ContainerAwareCommand
{
	protected function configure()
	{
		$this
			->setName('app:goal:markmilestones')
			->setDescription('Check what milestones need to be marked as failed.');
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$output->writeln('Searching for milestones to mark');

		$handledMilestones = $this->getContainer()->get('doctrine.orm.default_entity_manager')->getRepository(Checkin::class)->markFailedTasks(new \DateTime());
		$output->writeln('<info>Done, marked ' . $handledMilestones . ' milestones as failed</info>');
	}
}