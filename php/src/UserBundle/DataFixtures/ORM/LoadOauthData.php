<?php

namespace UserBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use FOS\OAuthServerBundle\Model\ClientManager;
use OAuth2\OAuth2;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use UserBundle\Entity\AccessToken;
use UserBundle\Entity\Client;
use UserBundle\Entity\RefreshToken;
use UserBundle\Entity\User;

class LoadOauthData extends AbstractFixture implements FixtureInterface, OrderedFixtureInterface, ContainerAwareInterface
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
        $client = $this->createClient(1, 'random_id', 'secret', null, [
            OAuth2::GRANT_TYPE_CLIENT_CREDENTIALS,
            OAuth2::GRANT_TYPE_REFRESH_TOKEN,
            OAuth2::GRANT_TYPE_USER_CREDENTIALS
        ]);

        $this->createAccessToken($client, 'anontoken');
    }

    /**
     * @param Client $client
     * @param string $token
     * @param User|null $user
     * @param string|null $scope
     */
    protected function createAccessToken(Client $client, $token, User $user = null, $scope = null)
    {
        $em = $this->container->get('doctrine.orm.default_entity_manager');

        $accessToken = new AccessToken();
        $accessToken->setToken($token);
        $accessToken->setExpiresAt(PHP_INT_MAX);
        $accessToken->setClient($client);
        $accessToken->setScope($scope);

        if ($user) {
            $accessToken->setUser($user);
        }

        $em->persist($accessToken);
        $em->flush();
    }

    /**
     * @param Client $client
     * @param string $token
     * @param User|null $user
     */
    protected function createRefreshToken(Client $client, $token, User $user = null)
    {
        $em = $this->container->get('doctrine.orm.default_entity_manager');

        $accessToken = new RefreshToken();
        $accessToken->setToken($token);
        $accessToken->setExpiresAt(PHP_INT_MAX);
        $accessToken->setClient($client);

        if ($user) {
            $accessToken->setUser($user);
        }

        $em->persist($accessToken);
        $em->flush();
    }

    /**
     * @param integer $id
     * @param string $randomId
     * @param string $secret
     * @param string|null $scope
     * @param array $grantTypes
     * @return \FOS\OAuthServerBundle\Model\ClientInterface
     */
    protected function createClient($id, $randomId, $secret, $scope = null, array $grantTypes = [])
    {
        $em = $this->container->get('doctrine.orm.default_entity_manager');

        /** @var ClientManager $clientManager */
        $clientManager = $this->container->get('fos_oauth_server.client_manager.default');
        $client = $clientManager->createClient();

        $client->setId($id);
        $metadata = $em->getClassMetadata(get_class($client));
        $metadata->setIdGenerator(new \Doctrine\ORM\Id\AssignedGenerator());
        $metadata->setIdGeneratorType(\Doctrine\ORM\Mapping\ClassMetadata::GENERATOR_TYPE_NONE);

        $client->setAllowedGrantTypes($grantTypes);

        $client->setRandomId($randomId);
        $client->setSecret($secret);
        $client->setScope($scope);

        $clientManager->updateClient($client);

        return $client;
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
        return 2;
    }
}