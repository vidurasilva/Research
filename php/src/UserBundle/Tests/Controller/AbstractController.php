<?php
/**
 * Created by PhpStorm.
 * User: ngeleedst
 * Date: 02/09/16
 * Time: 11:57
 */

namespace UserBundle\Tests\Controller;

use Symfony\Component\HttpFoundation\Response;
use UserBundle\Entity\Client;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

abstract class AbstractController extends WebTestCase
{
    const GRANT_TYPE_CLIENT_CREDENTIALS = 'client_credentials';
    const GRANT_TYPE_PASSWORD = 'password';

    /**
     * @var \Symfony\Bundle\FrameworkBundle\Client
     */
    protected $client;

    /** @var  EntityManager */
    protected $em;

    /**
     * @var string
     */
    protected $token;

    /**
     * @var array
     */
    protected $tokenMockData;

    /**
     * @var array
     */
    protected $authMockData;

	protected function doRequest($method, $uri, $token, $parameters = [], $files = [], $checkStatusCode = true)
	{
		$this->client->request($method, $uri, $parameters, $files, ['HTTP_Authorization' => $token]);
		$content = json_decode($this->client->getResponse()->getContent());

		if ($checkStatusCode) {
			$message = null;
			if (isset($content->data->message)) {
				$message = $content->data->message;
			} elseif (isset($content->message)) {
				$message = $content->message;
			} elseif (isset($content->error)) {
				$message = $content->error;
				if (isset($content->error_description)) {
					$message .= ': ' . $content->error_description;
				}
			}

			$this->assertEquals(
				Response::HTTP_OK,
				$this->client->getResponse()->getStatusCode(),
				$message
			);
		}

		return $content;
	}

    /**
     * @return array
     */
    public function setUp()
    {
        self::bootKernel();

        $this->em = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        $this->client = static::createClient();
        $this->tokenMockData = $this->loadTokenMockingData();
        $this->authMockData = $this->loadAuthMockingData();

        $this->generateTokenOauthFlow();
    }

    public function tearDown()
    {
        $this->removeMockDataFromDb();
    }

    /**
     * @return array
     */
    protected function loadTokenMockingData($anonymous = true)
    {
        /** @var Client $clientData */
        $clientData = $this->em->getRepository('UserBundle:Client')->find(1);

        $data = [
                'client_id' => $clientData->getId() . '_' . $clientData->getRandomId(),
                'client_secret' => $clientData->getSecret(),
                'grant_type' => $anonymous ? self::GRANT_TYPE_CLIENT_CREDENTIALS : self::GRANT_TYPE_PASSWORD
            ];

	    if (!$anonymous) {
	    	$data['username'] = 'ngeleedst@e-sites.nl';
	    	$data['password'] = 'test1234';
	    }

	    return $data;
    }

    /**
     * @todo: add mock lib
     * @return array
     */
    protected function loadAuthMockingData()
    {
        return
            [
                'firstname' => 'Nick',
                'lastname' => 'Geleedst',
                'nickname' => 'Nickname',
                'email' => sprintf('ngeleedst+%s@e-sites.nl', time()),
                'password' => uniqid('test')
            ];
    }

    /**
     * Generate oauth token.
     */
    protected function generateTokenOauthFlow()
    {
    	$content = $this->doRequest('POST', '/api/oauth/v2/token', null, $this->tokenMockData, [], false);
        $this->token = sprintf('Bearer %s', $content->access_token); //Temp save token

        unset($content);
    }

    protected function getLoggedInToken()
    {
	    $loggedInMockData = $this->loadTokenMockingData(false);

	    // we have an anonymous token, we also need a logged in token for some endpoints
	    $content = $this->doRequest('POST', '/api/oauth/v2/token', null, $loggedInMockData, [], false);
	    if (!empty($content->access_token)) {
	    	return 'Bearer ' . $content->access_token;
	    }
    }

    protected function removeMockDataFromDb()
    {
        self::bootKernel();
        $fosUserManager = static::$kernel->getContainer()
            ->get('fos_user.user_manager');

        $user = $fosUserManager->findUserByEmail($this->authMockData['email']);

        if ($user) {
            $fosUserManager->deleteUser($user);
        }
    }
}