<?php
/**
 * Created by PhpStorm.
 * User: mdubbelman
 * Date: 9/8/16
 * Time: 2:44 PM
 */

namespace UserBundle\Service;


use Doctrine\ORM\EntityManager;
use UserBundle\Entity\User;

abstract class SocialMediaLogin
{
    /**
     * @var string
     */
    protected $lastError = '';
	/**
	 * @var EntityManager
	 */
	private $entityManager;

	abstract protected function getUserFields();

    abstract protected function mapUser($externalUser);

    abstract public function getUserByAccessToken($accessToken);

    abstract public function getAuthorizationRegistryFailIndex();

	abstract public function getProfileImage(User $user, $accessToken, $width = 750, $height = 750);

    /**
     * @param User $currentUser
     * @param User $fetchedUser
     * @return User
     */
    public function synchroniseUser(User $currentUser, User $fetchedUser)
    {
        if ($fetchedUser->getEmail()) {
            $currentUser->setEmail($fetchedUser->getEmail());
        }

        $currentUser->setFirstname($fetchedUser->getFirstname());
        $currentUser->setLastname($fetchedUser->getLastname());
        $currentUser->setNickName($fetchedUser->getNickName());

        return $currentUser;
    }

    protected function checkPassword(User $user)
    {
        if ($user->getPassword() === null) {
            $password = bin2hex(mcrypt_create_iv(15, MCRYPT_DEV_URANDOM));
            $user->setPlainPassword($password);
        }
    }

    /**
     * @return string
     */
    public function getLastError()
    {
        return $this->lastError;
    }
}