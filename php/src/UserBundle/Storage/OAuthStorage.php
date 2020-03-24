<?php

namespace UserBundle\Storage;

use FOS\OAuthServerBundle\Storage\OAuthStorage as BaseOauthStorage;
use OAuth2\Model\IOAuth2Client;
use UserBundle\Entity\Client;

class OAuthStorage extends BaseOauthStorage
{
    /**
     * {@inheritdoc}
     */
    public function createAccessToken($tokenString, IOAuth2Client $client, $data, $expires, $scope = null)
    {
        if (!$client instanceof Client) {
            throw new \InvalidArgumentException('Client has to be a \UserBundle\Entity\Client');
        }

        $token = $this->accessTokenManager->createToken();
        $token->setToken($tokenString);
        $token->setClient($client);
        $token->setExpiresAt($expires);
        $token->setScope($scope ?: $client->getScope());

        if (null !== $data) {
            $token->setUser($data);
        }

        $this->accessTokenManager->updateToken($token);

        return $token;
    }
}