<?php
namespace UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use FOS\OAuthServerBundle\Entity\Client as BaseClient;


class Client extends BaseClient
{
    const DEVICE_FAMILY_UNKNOWN = 'unknown';
    const DEVICE_FAMILY_ANDROID = 'ios';
    const DEVICE_FAMILY_IOS = 'android';

    protected $id;

    protected $deviceFamily = self::DEVICE_FAMILY_UNKNOWN;

    protected $scope = null;

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @param integer $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }


    /**
     * @return string
     */
    public function getDeviceFamily()
    {
        return $this->deviceFamily;
    }

    /**
     * @param string $deviceFamily
     */
    public function setDeviceFamily($deviceFamily)
    {
        $this->deviceFamily = $deviceFamily;
    }

    /**
     * @return null|string
     */
    public function getScope()
    {
        return $this->scope;
    }

    /**
     * @param null|string $scope
     */
    public function setScope($scope)
    {
        $this->scope = $scope;
    }
}
