<?php

namespace UserBundle\Responses;

use JMS\Serializer\Annotation\Type;

class OauthCredentials
{
    /**
     * @var int
     * @Type("integer")
     */
    protected $expiresIn;

    /**
     * @var string
     * @Type("string")
     */
    protected $accessToken;

    /**
     * @var string
     * @Type("string")
     */
    protected $tokenType;

    /**
     * @var string|null
     * @Type("string")
     */
    protected $refreshToken = null;

    /**
     * @var string|null
     * @Type("string")
     */
    protected $scope;

    /**
     * @param array $tokenSet
     */
    public function __construct(array $tokenSet)
    {
        $this->accessToken = $tokenSet['access_token'];
        $this->expiresIn = $tokenSet['expires_in'];
        $this->scope = $tokenSet['scope'];
        $this->tokenType = $tokenSet['token_type'];

        if (isset($tokenSet['refresh_token'])) {
            $this->refreshToken = $tokenSet['refresh_token'];
        }
    }
}