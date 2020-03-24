<?php

namespace UserBundle\Service\Factory;

use Facebook\Facebook;
use UserBundle\Service\FacebookService;

class FacebookServiceFactory
{
    const GRAPH_VERSION = 'v2.6';

    /**
     * @var string
     */
    protected $appId;

    /**
     * @var string
     */
    protected $appSecret;

    /**
     * @param string $appId
     * @param string $appSecret
     */
    public function __construct($appId, $appSecret)
    {
        $this->appId = $appId;
        $this->appSecret = $appSecret;
    }

    /**
     * @return FacebookService
     */
    public function create()
    {
        $adapter = new Facebook([
            'app_id' => $this->appId,
            'app_secret' => $this->appSecret,
            'default_graph_version' => self::GRAPH_VERSION,
        ]);

        return new FacebookService($adapter);
    }
}