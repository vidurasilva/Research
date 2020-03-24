<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\CommunityCategory;
use AppBundle\Entity\CommunityQuestion;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadCommunityData extends AbstractFixture implements FixtureInterface, OrderedFixtureInterface, ContainerAwareInterface
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
        //Fix id generator
        $metadata = $manager->getClassMetadata(CommunityCategory::class);
        $metadata->setIdGenerator(new \Doctrine\ORM\Id\AssignedGenerator());
        $metadata->setIdGeneratorType(\Doctrine\ORM\Mapping\ClassMetadata::GENERATOR_TYPE_NONE);

        $metadata = $manager->getClassMetadata(CommunityQuestion::class);
        $metadata->setIdGenerator(new \Doctrine\ORM\Id\AssignedGenerator());
        $metadata->setIdGeneratorType(\Doctrine\ORM\Mapping\ClassMetadata::GENERATOR_TYPE_NONE);

	    $user = $manager->getRepository('UserBundle:User')->findBy(['email' => 'ngeleedst@e-sites.nl']);

        //Add root category
        $category = new CommunityCategory();
        $category->setId(1);
        $category->setTitle('Health');
        $category->setIcon('like-2.png');

        //Add sub-category
        $childCat = new CommunityCategory();
        $childCat->setId(2);
        $childCat->setTitle('Be productive');
        $childCat->setParent($category);

        $manager->persist($category);
        $manager->persist($childCat);

        //Add question
        $question = new CommunityQuestion();
        $question->setId(1);
        $question->setTitle('Can i add a custom task to an existing goal?');
        $question->setCommunityCategory($childCat);
        $question->setUser(current($user));
        $manager->persist($question);

        //Add question
        $question = new CommunityQuestion();
        $question->setId(2);
        $question->setTitle('How can i set a new goal?');
        $question->setCommunityCategory($childCat);
        $question->setUser(current($user));
        $manager->persist($question);

        $question = new CommunityQuestion();
        $question->setId(3);
        $question->setTitle('Can i set stakes for each milestone?');
        $question->setCommunityCategory($childCat);
        $question->setUser(current($user));
        $manager->persist($question);

        //Add root category
        $category = new CommunityCategory();
        $category->setId(3);
        $category->setTitle('Meditate');
        $category->setIcon('page-1.png');
        $manager->persist($category);

        //Add root category
        $category = new CommunityCategory();
        $category->setId(4);
        $category->setTitle('Challenges');
        $category->setIcon('alarm-clock.png');
        $manager->persist($category);

        //Add root category
        $category = new CommunityCategory();
        $category->setId(5);
        $category->setTitle('Bad habits');
        $category->setIcon('like.png');
        $manager->persist($category);

        //Add root category
        $category = new CommunityCategory();
        $category->setId(6);
        $category->setTitle('Knowledge');
        $category->setIcon('worldwide-1.png');
        $manager->persist($category);

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
		return 4;
	}
}