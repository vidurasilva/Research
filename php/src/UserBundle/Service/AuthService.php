<?php

namespace UserBundle\Service;

use FOS\OAuthServerBundle\Entity\AccessTokenManager;
use FOS\UserBundle\Doctrine\UserManager;
use OAuth2\OAuth2;

class AuthService
{
    protected $userManager;
    protected $accessTokenManager;
    protected $oauth;

    /**
     * @param UserManager $userManager
     * @param AccessTokenManager $accessTokenManager
     * @param OAuth2 $oauth
     */
    public function __construct(UserManager $userManager, AccessTokenManager $accessTokenManager, OAuth2 $oauth)
    {
        $this->userManager = $userManager;
        $this->accessTokenManager = $accessTokenManager;
        $this->oauth = $oauth;
    }

    /**
     * @return UserManager
     */
    public function getUserManager()
    {
        return $this->userManager;
    }

    /**
     * @return AccessTokenManager
     */
    public function getAccessTokenManager()
    {
        return $this->accessTokenManager;
    }

    /**
     * @return OAuth2
     */
    public function getOauth()
    {
        return $this->oauth;
    }


}
