<?php

namespace AppBundle\DataFixtures\ORM;

use ApiBundle\Responses\MilestoneSummary;
use AppBundle\Entity\Goal;
use AppBundle\Entity\GoalCategory;
use AppBundle\Entity\Milestone;
use AppBundle\Entity\Score;
use AppBundle\Entity\Task;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManager;
use UserBundle\Entity\User;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadScoreData extends AbstractFixture implements FixtureInterface, OrderedFixtureInterface, ContainerAwareInterface
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
		$repo = $manager->getRepository('AppBundle:Goal');
		$goal = $repo->findOneBy(['id' => 2]);

		$repo = $manager->getRepository('UserBundle:User');
		$users = $repo->findAll();

		/** @var User $user */
		foreach ($users as $user) {
			$score = new Score();

			$score->setUser($user);
			$score->setScore(mt_rand(35, 78));
			if ($user->getId() === 4) {
				$score->setScore(10);
			}

			$score->setGroupGoal(null);
			$score->setGoal($goal);
			$manager->persist($score);
		}

		$manager->flush();
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
		return 6;
	}
}