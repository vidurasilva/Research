<?php

namespace AppBundle\DataFixtures\ORM;

use ApiBundle\Responses\MilestoneSummary;
use AppBundle\Entity\Goal;
use AppBundle\Entity\GoalCategory;
use AppBundle\Entity\Milestone;
use AppBundle\Entity\Task;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadCategoryData extends AbstractFixture implements FixtureInterface, OrderedFixtureInterface, ContainerAwareInterface
{
	/**
	 * @var ContainerInterface
	 */
	protected $container;

	/**
	 * @param ObjectManager $manager
	 */
	public function load(ObjectManager $manager)
	{
		$metadata = $manager->getClassMetadata(GoalCategory::class);
		$metadata->setIdGenerator(new \Doctrine\ORM\Id\AssignedGenerator());
		$metadata->setIdGeneratorType(\Doctrine\ORM\Mapping\ClassMetadata::GENERATOR_TYPE_NONE);

		$metadata = $manager->getClassMetadata(Goal::class);
		$metadata->setIdGenerator(new \Doctrine\ORM\Id\AssignedGenerator());
		$metadata->setIdGeneratorType(\Doctrine\ORM\Mapping\ClassMetadata::GENERATOR_TYPE_NONE);


		$metadata = $manager->getClassMetadata(Milestone::class);
		$metadata->setIdGenerator(new \Doctrine\ORM\Id\AssignedGenerator());
		$metadata->setIdGeneratorType(\Doctrine\ORM\Mapping\ClassMetadata::GENERATOR_TYPE_NONE);

	    $category = new GoalCategory();
		$category->setId(1);
        $category->setTitle('Health');
        $category->setIcon('like-2.png');

        $goal = new Goal();
		$goal->setId(1);
        $goal->setTitle('Be productive');
        $goal->setCategory($category);
        $goal->setDuration('4 weeks');
        $manager->persist($category);
        $manager->persist($goal);

		$milestone = new Milestone();
		$milestone->setNumber(1);
		$milestone->setId(1);
		$milestone->setTitle('Get started');
		$milestone->setGoal($goal);
		$manager->persist($milestone);

		$taskData = [
			1 => 'Organize your stuff',
			2 => 'Make a todo list',
			3 => 'Clean your inbox',
			4 => 'Check-in',
		];

		$this->addTasks($taskData, $milestone, $manager);

		$milestone = new Milestone();
		$milestone->setId(2);
		$milestone->setNumber(2);
		$milestone->setTitle('Basics');
		$milestone->setGoal($goal);
		$manager->persist($milestone);

		$taskData = [
			1 => 'Use your todo list',
			2 => 'Check your mail 3 times a day',
			3 => 'Take a walk for 5 minutes',
			4 => 'Listen to some relaxing music',
		];

		$this->addTasks($taskData, $milestone, $manager);

		$milestone = new Milestone();
		$milestone->setId(3);
		$milestone->setNumber(3);
		$milestone->setTitle('Step up');
		$milestone->setGoal($goal);
		$manager->persist($milestone);

		$taskData = [
			1 => 'Say "No" to 3 collegues',
			2 => 'Read a book',
			3 => 'Use post-its for your todo list',
		];

		$this->addTasks($taskData, $milestone, $manager);

		$goal = new Goal();
		$goal->setId(2);
		$goal->setTitle('Stop smoking');
		$goal->setCategory($category);
		$goal->setDuration('3 months');
		$goal->setDescription('Be more healthy and quit smoking');
		$manager->persist($category);
		$manager->persist($goal);

        $category = new GoalCategory();
		$category->setId(2);
        $category->setTitle('Meditate');
        $category->setIcon('page-1.png');

        $goal = new Goal();
		$goal->setId(3);
        $goal->setTitle('Meditate');
        $goal->setCategory($category);
        $manager->persist($category);
        $manager->persist($goal);

        $category = new GoalCategory();
		$category->setId(3);
        $category->setTitle('Challenges');
        $category->setIcon('alarm-clock.png');

        $goal = new Goal();
		$goal->setId(4);
        $goal->setTitle('Start running');
        $goal->setCategory($category);
        $manager->persist($category);
        $manager->persist($goal);

        $category = new GoalCategory();
		$category->setId(4);
        $category->setTitle('Bad habits');
        $category->setIcon('like.png');
        $manager->persist($category);

        $category = new GoalCategory();
		$category->setId(5);
        $category->setTitle('Knowledge');
        $category->setIcon('worldwide-1.png');
        $manager->persist($category);

		$manager->flush();
	}

	protected function addTasks($taskData, $milestone, $manager)
	{
		foreach ($taskData as $position => $title) {
			$task = new Task();
			$task->setTitle($title);
			$task->setMilestone($milestone);
			$task->setPosition($position);
			$manager->persist($task);
		}
	}

	/**
	 * @param ContainerInterface|null $container
	 */
	public function setContainer(ContainerInterface $container = null)
	{
		$this->container = $container;
	}

	/**
	 * @return int
	 */
	public function getOrder()
	{
		return 3;
	}
}