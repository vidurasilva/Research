<?php

namespace AppBundle\DataFixtures\ORM;


use AppBundle\Entity\Charity;
use AppBundle\Entity\CharityCategory;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadCharityData extends AbstractFixture implements FixtureInterface, OrderedFixtureInterface, ContainerAwareInterface
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
		$metadata = $manager->getClassMetadata(CharityCategory::class);
		$metadata->setIdGenerator(new \Doctrine\ORM\Id\AssignedGenerator());
		$metadata->setIdGeneratorType(\Doctrine\ORM\Mapping\ClassMetadata::GENERATOR_TYPE_NONE);

		$metadata = $manager->getClassMetadata(Charity::class);
		$metadata->setIdGenerator(new \Doctrine\ORM\Id\AssignedGenerator());
		$metadata->setIdGeneratorType(\Doctrine\ORM\Mapping\ClassMetadata::GENERATOR_TYPE_NONE);

	    $category = new CharityCategory();
		$category->setId(1);
        $category->setTitle('Positive charity');
		$manager->persist($category);

        $charity = new Charity();
		$charity->setId(1);
        $charity->setTitle('WWF');
		$charity->setCharityCategory($category);
        $manager->persist($charity);

		$charity = new Charity();
		$charity->setId(2);
		$charity->setTitle('Greenpeace');
		$charity->setCharityCategory($category);
		$manager->persist($charity);

		$category = new CharityCategory();
		$category->setId(2);
		$category->setTitle('Negative charity');
		$manager->persist($category);

		$charity = new Charity();
		$charity->setId(3);
		$charity->setTitle('Gun law');
		$charity->setCharityCategory($category);
		$manager->persist($charity);

		$category = new CharityCategory();
		$category->setId(3);
		$category->setTitle('Future development');
		$manager->persist($category);

		$charity = new Charity();
		$charity->setId(4);
		$charity->setTitle('Future development');
		$charity->setCharityCategory($category);
		$manager->persist($charity);

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
		return 5;
	}
}