<?php
/**
 * @author jgeerts@e-sites.nl
 * @since 27-6-16 16:30
 */

namespace UserBundle\DataFixtures\ORM;

use AppBundle\Entity\Score;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use UserBundle\Entity\User;

class LoadUserData extends AbstractFixture implements FixtureInterface, OrderedFixtureInterface, ContainerAwareInterface
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
        $metadata = $manager->getClassMetadata(User::class);
        $metadata->setIdGenerator(new \Doctrine\ORM\Id\AssignedGenerator());
        $metadata->setIdGeneratorType(\Doctrine\ORM\Mapping\ClassMetadata::GENERATOR_TYPE_NONE);

		$um = $this->container->get('fos_user.user_manager');

		$user = new User();
        $user->setId(1);
		$user->setUsername('mdubbelman@e-sites.nl');
		$user->setEmail('mdubbelman@e-sites.nl');
		$user->setEnabled(true);
		$user->setPlainPassword('test1234');
		$user->setRoles(['ROLE_USER', 'ROLE_ADMIN']);
		$user->setFirstname('Martin');
        $user->setLastname('Dubbelman');
		$um->updateUser($user);
		$this->addReference('mdubbelman', $user);

		$user = new User();
        $user->setId(2);
		$user->setUsername('ngeleedst@e-sites.nl');
		$user->setEmail('ngeleedst@e-sites.nl');
		$user->setEnabled(true);
		$user->setPlainPassword('test1234');
		$user->setRoles(['ROLE_USER', 'ROLE_ADMIN']);
		$user->setFirstname('Nick');
        $user->setLastname('Geleedst');
		$um->updateUser($user);
		$this->addReference('ngeleedst', $user);

		$user = new User();
        $user->setId(3);
		$user->setUsername('dvdhaar@e-sites.nl');
		$user->setEmail('dvdhaar@e-sites.nl');
		$user->setEnabled(true);
		$user->setPlainPassword('test1234');
		$user->setRoles(['ROLE_USER']);
        $user->setFirstname('Dennis');
        $user->setLastname('vdhaar');
		$um->updateUser($user);
		$this->addReference('dvdhaar', $user);

		$user = new User();
		$user->setId(4);
		$user->setUsername('martin+phpunit@e-sites.nl');
		$user->setEmail('martin+phpunit@e-sites.nl');
		$user->setEnabled(true);
		$user->setPlainPassword('test1234');
		$user->setRoles(['ROLE_USER']);
		$user->setFirstname('Martin');
		$user->setLastname('Dubbelman');
		$um->updateUser($user);
		$this->addReference('martindubbelman', $user);

		$user = new User();
		$user->setId(5);
		$user->setUsername('slochten@e-sites.nl');
		$user->setEmail('slochten@e-sites.nl');
		$user->setEnabled(true);
		$user->setPlainPassword('test1234');
		$user->setRoles(['ROLE_USER']);
		$user->setFirstname('Stefan');
		$user->setLastname('Lochten');
		$um->updateUser($user);
		$this->addReference('slochten', $user);

		$user = new User();
		$user->setId(6);
		$user->setUsername('slochten+test@e-sites.nl');
		$user->setEmail('slochten+test@e-sites.nl');
		$user->setEnabled(true);
		$user->setPlainPassword('test1234');
		$user->setRoles(['ROLE_USER']);
		$user->setFirstname('Ste-fan');
		$user->setLastname('Lochten');
		$um->updateUser($user);
		$this->addReference('stefanlochten', $user);

		$user = new User();
		$user->setId(7);
		$user->setUsername('stefan+score@e-sites.nl');
		$user->setEmail('stefan+score@e-sites.nl');
		$user->setEnabled(true);
		$user->setPlainPassword('test1234');
		$user->setRoles(['ROLE_USER']);
		$user->setFirstname('Score');
		$user->setLastname('Stefan');
		$um->updateUser($user);
		$this->addReference('lochtenscore', $user);

		$user = new User();
		$user->setId(8);
		$user->setUsername('slochten+score@e-sites.nl');
		$user->setEmail('slochten+score@e-sites.nl');
		$user->setEnabled(true);
		$user->setPlainPassword('test1234');
		$user->setRoles(['ROLE_USER']);
		$user->setFirstname('Lochten');
		$user->setLastname('Score');
		$um->updateUser($user);
		$this->addReference('scorestefan', $user);

		$user = new User();
		$user->setId(9);
		$user->setUsername('stefan+test@e-sites.nl');
		$user->setEmail('stefan+test@e-sites.nl');
		$user->setEnabled(true);
		$user->setPlainPassword('test1234');
		$user->setRoles(['ROLE_USER']);
		$user->setFirstname('Lochten');
		$user->setLastname('Stefan');
		$um->updateUser($user);
		$this->addReference('lochtenstefan', $user);

		$user = new User();
		$user->setId(10);
		$user->setUsername('stefan+10test@e-sites.nl');
		$user->setEmail('stefan+10test@e-sites.nl');
		$user->setEnabled(true);
		$user->setPlainPassword('test1234');
		$user->setRoles(['ROLE_USER']);
		$user->setFirstname('Lochten 10');
		$user->setLastname('Stefan');
		$um->updateUser($user);
		$this->addReference('lochten10stefan', $user);

		$user = new User();
		$user->setId(11);
		$user->setUsername('stefan+11test@e-sites.nl');
		$user->setEmail('stefan+11test@e-sites.nl');
		$user->setEnabled(true);
		$user->setPlainPassword('test1234');
		$user->setRoles(['ROLE_USER']);
		$user->setFirstname('Lochten 11');
		$user->setLastname('Stefan');
		$um->updateUser($user);
		$this->addReference('lochten11stefan', $user);

		$user = new User();
		$user->setId(12);
		$user->setUsername('stefan+12test@e-sites.nl');
		$user->setEmail('stefan+12test@e-sites.nl');
		$user->setEnabled(true);
		$user->setPlainPassword('test1234');
		$user->setRoles(['ROLE_USER']);
		$user->setFirstname('Lochten 12');
		$user->setLastname('Stefan');
		$um->updateUser($user);
		$this->addReference('lochten12stefan', $user);

		$user = new User();
		$user->setId(13);
		$user->setUsername('stefan+13test@e-sites.nl');
		$user->setEmail('stefan+13test@e-sites.nl');
		$user->setEnabled(true);
		$user->setPlainPassword('test1334');
		$user->setRoles(['ROLE_USER']);
		$user->setFirstname('Lochten 13');
		$user->setLastname('Stefan');
		$um->updateUser($user);
		$this->addReference('lochten13stefan', $user);

		$user = new User();
		$user->setId(14);
		$user->setUsername('stefan+14test@e-sites.nl');
		$user->setEmail('stefan+14test@e-sites.nl');
		$user->setEnabled(true);
		$user->setPlainPassword('test1434');
		$user->setRoles(['ROLE_USER']);
		$user->setFirstname('Lochten 14');
		$user->setLastname('Stefan');
		$um->updateUser($user);
		$this->addReference('lochten14stefan', $user);

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
		return 1;
	}
}