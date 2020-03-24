<?php

namespace UserBundle\Service;

use ApiBundle\Exception\FailRegistry;
use Facebook\Exceptions\FacebookOtherException;
use Facebook\Exceptions\FacebookResponseException;
use Facebook\Facebook;
use Facebook\GraphNodes\GraphUser;
use UserBundle\Entity\User;

class FacebookService extends SocialMediaLogin
{
    /**
     * @var Facebook
     */
    protected $adapter;

    /**
     * @param Facebook $adapter
     */
    public function __construct(Facebook $adapter)
    {
        $this->adapter = $adapter;
    }

	public function getAuthorizationRegistryFailIndex()
	{
		return FailRegistry::AUTHORIZATION_FAIL_FACEBOOK;
	}

    public function getFacebookAdapter()
    {
	    return $this->adapter;
    }

    public function getProfileImage(User $user, $accessToken, $width = 750, $height = 750)
    {
    	try {
    		$query = http_build_query([
    		    'redirect' => false,
		        'width' => $width,
			    'height' => $height,
		    ]);

		    $response = $this->adapter->get('/'.$user->getFacebookUserId().'/picture?' . $query, $accessToken);
		    if (!$response || !$response->getGraphNode()) {
			    return null;
		    }

		    return $response->getGraphNode()->getField('url');

	    } catch (FacebookResponseException $e) {
		    $this->lastError = $e->getMessage();
		    return false;
	    } catch (FacebookOtherException $e) {

		    $this->lastError = $e->getMessage();
		    return false;
	    }

    }

	/**
     * @param string $accessToken
     * @return User|null
     */
    public function getUserByAccessToken($accessToken)
    {
        $fields = implode(',', $this->getUserFields());
		$fields .= ',picture';
        try {
            $response = $this->adapter->get('/me?fields=' . $fields, $accessToken);

            if (!$response || !$response->getGraphUser()) {
                return null;
            }

            if (!$response->getGraphUser()->getEmail()) {
                throw new FacebookOtherException('Required permission denied: e-mail');
            }

            return $this->mapUser($response->getGraphUser());

        } catch (FacebookResponseException $e) {
            $this->lastError = $e->getMessage();
            return false;
        } catch (FacebookOtherException $e) {
            $this->lastError = $e->getMessage();
            return false;
        }
    }

    /**
     * @param GraphUser $externalUser
     * @return User
     */
	protected function mapUser($externalUser)
    {
        $user = new User;
        $user->setFacebookUserId($externalUser->getId());
        $user->setUsername($externalUser->getEmail());
        $user->setEmail($externalUser->getEmail());
        $user->setFirstname($externalUser->getFirstName());
        $user->setLastname($externalUser->getLastName());
        $user->setGender($externalUser->getGender());
        $user->setEnabled(true);

	    $this->checkPassword($user);

        return $user;
    }

    /**
     * @return array
     */
    protected function getUserFields()
    {
        return ['id', 'email', 'first_name', 'last_name', 'gender'];
    }
}