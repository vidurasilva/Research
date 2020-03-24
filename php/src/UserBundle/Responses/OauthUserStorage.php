<?php

namespace UserBundle\Responses;

use JMS\Serializer\Annotation\Type;

class OauthUserStorage extends UserStorage
{
    /**
     * @var OauthCredentials
     * @Type("UserBundle\Responses\OauthCredentials")
     */
    protected $oauthCredentials;

    /**
     * @var OauthCredentials
     * @Type("boolean")
     */
    protected $isNewUser;

    /**
     * @param \UserBundle\Entity\User $entity
     * @param OauthCredentials $oauthCredentials
     * @param bool $isNewUser
     */
    public function __construct(\UserBundle\Entity\User $entity, OauthCredentials $oauthCredentials, $isNewUser = false)
    {
        parent::__construct($entity);
        $this->isNewUser = $isNewUser;
        $this->oauthCredentials = $oauthCredentials;
    }
}