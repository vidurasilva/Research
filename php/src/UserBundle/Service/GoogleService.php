<?php
/**
 * Created by PhpStorm.
 * User: mdubbelman
 * Date: 9/8/16
 * Time: 1:36 PM
 */

namespace UserBundle\Service;


use ApiBundle\Exception\FailRegistry;
use UserBundle\Entity\User;

class GoogleService extends SocialMediaLogin
{
    protected $client;

    public function getAuthorizationRegistryFailIndex()
    {
        return FailRegistry::AUTHORIZATION_FAIL_GOOGLE;
    }


    public function __construct($clientId, $clientSecret)
    {
        $this->client = new \Google_Client(
            [
                'client_id' => $clientId,
                'client_secret' => $clientSecret
            ]
        );
        $this->client->addScope(\Google_Service_People::USERINFO_PROFILE);
    }

    public function getClient()
    {
        return $this->client;
    }

	public function getProfileImage(User $user, $accessToken, $width = 750, $height = 750)
	{
		$this->client->setAccessToken(['access_token' => $accessToken, 'expires_in' => 60 * 60]);
		$googleAuth = new \Google_Service_Oauth2($this->client);
		$userdata = $googleAuth->userinfo->get();

		return $userdata->getPicture();
	}

    public function getUserByAccessToken($accessToken)
    {
        try {
            $this->client->setAccessToken(['access_token' => $accessToken, 'expires_in' => 60 * 60]);
            $googleAuth = new \Google_Service_Oauth2($this->client);
            $userdata = $googleAuth->userinfo->get();
        } catch (\Exception $e) {
            if ($data = \json_decode($e->getMessage())) {
                if (isset($data->error->message)) {
                    $this->lastError = $data->error->message;
                    return false;
                }
            }
            $this->lastError = 'Google API unknown error';
            return false;
        }

        return $this->mapUser($userdata);
    }

    protected function getUserFields()
    {
        return ['id', 'email', 'name', 'gender'];
    }

    protected function mapUser($externalUser)
    {
        $user = new User();
        $user->setGoogleUserId($externalUser['id']);
        $user->setUsername($externalUser['email']);
        $user->setEmail($externalUser['email']);
        $user->setFirstname($externalUser['givenName']);
        $user->setLastname($externalUser['familyName']);
        $user->setGender($externalUser['gender']);
        $user->setEnabled(true);

        $this->checkPassword($user);

        return $user;
    }
}