<?php

namespace UserBundle\Service;

use AppBundle\Service\MailerService;
use FOS\OAuthServerBundle\Entity\AccessTokenManager;
use FOS\UserBundle\Model\UserManager;
use FOS\UserBundle\Util\TokenGenerator;
use OAuth2\OAuth2;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\MimeType\ExtensionGuesser;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use UserBundle\Entity\User;
use UserBundle\Event\UserEvent;
use UserBundle\EventListener\RegistrationListener;
use UserBundle\Repository\UserRepository;
use UserBundle\Responses\OauthCredentials;

class UserService
{
    /**
     * @var UserManager
     */
    protected $userManager;

    /**
     * @var AccessTokenManager
     */
    protected $accessTokenManager;

    /**
     * @var OAuth2
     */
    protected $oauth;

    /**
     * @var TokenStorage
     */
    protected $tokenStorage;

    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * @var MailerService
     */
    protected $mailerService;

    /**
     * @var TokenGenerator
     */
    protected $tokenGenerator;

    /**
     * @var string
     */
    protected $marketingSiteUrl;

    /**
     * @var UserRepository
     */
    protected $userRepository;


    /**
     * @var Router
     */
    private $router;
	private $uploadPath;

	/**
     * @param UserManager $userManager
     * @param AccessTokenManager $accessTokenManager
     * @param OAuth2 $oauth
     * @param TokenStorage $tokenStorage
     * @param EventDispatcherInterface $eventDispatcher
     * @param MailerService $mailerService
     * @param TokenGenerator $tokenGenerator
     * @param UserRepository $userRepository
     * @param string $marketingSiteUrl
     */
    public function __construct(
        AuthService $authService,
        TokenStorage $tokenStorage,
        EventDispatcherInterface $eventDispatcher,
        MailerService $mailerService,
        TokenGenerator $tokenGenerator,
        UserRepository $userRepository,
        $marketingSiteUrl,
        Router $router,
		$uploadPath
    )
    {
        $this->tokenStorage = $tokenStorage;
        $this->eventDispatcher = $eventDispatcher;
        $this->mailerService = $mailerService;
        $this->tokenGenerator = $tokenGenerator;
        $this->userRepository = $userRepository;
        $this->marketingSiteUrl = $marketingSiteUrl;
        $this->authService = $authService;
        $this->router = $router;
	    $this->uploadPath = $uploadPath;
    }

    /**
     * @param string $username
     * @return User
     */
    public function findUserByUsername($username)
    {
        $userManager = $this->getUserManager();
        if ($user = $userManager->findUserByUsername($username)) {
            return $user;
        }

        return $userManager->findUserBy(['facebookUserId' => $username]);
    }

    /**
     * @param string $email
     * @return User
     */
    public function findUserByEmail($email)
    {
        return $this->getUserManager()->findUserByEmail($email);
    }


    /**
     * @param integer $id
     * @return User
     */
    public function findUserById($id)
    {
        return $this->getUserManager()->findUserBy(['id' => $id]);
    }

    /**
     * @param string $token
     * @return User
     */
    public function findUserByConfirmationToken($token)
    {
        return $this->getUserManager()->findUserByConfirmationToken($token);
    }

    /**
     * @param string $username
     * @return bool
     */
    public function userExists($username)
    {
        return !!$this->findUserByUsername($username);
    }

    /**
     * @param User $user
     */
    public function updateUser(User $user)
    {
        $this->getUserManager()->updateUser($user, true);

        if (!$user->getNotificationUserId()) {
            $this->generateNotificationUser($user);
        }
    }

    /**
     * @param User $user
     */
    public function disableUser(User $user)
    {
        $user->setEnabled(false);
        $this->getUserManager()->updateUser($user);
    }

    /**
     * @param $email
     * @param $password
     * @param $firstname
     * @param $lastname
     * @param $nickname
     * @return User
     */
    public function createUser($email, $password, $firstname, $lastname, $nickname)
    {
        $user = new User();

        $user->setUsername($email);
        $user->setEmail($email);
        $user->setNickName($nickname);
        $user->setFirstname($firstname);
        $user->setLastname($lastname);
        $user->setEnabled(true);
        $user->setPlainPassword($password);
        $user->addRole(User::ROLE_USER);

        $this->updateUser($user);

        $data = ['currentUser' => $user, 'password' => $password];
        $this->eventDispatcher->dispatch(RegistrationListener::EVENT_POSTREGISTRATION, new UserEvent($user, $data));

        return $user;
    }


    /**
     * @param User $user
     * @param Request $request
     * @return User
     */
    public function synchroniseUserFromRequest(User $user, Request $request)
    {
        if (!$user->getFirstname()) {
            $user->setFirstname($request->get('firstname'));
        }

        if (!$user->getLastname()) {
            $user->setFirstname($request->get('lastname'));
        }

        if (!$user->getNickName()) {
            $user->setFirstname($request->get('nickname'));
        }

        $this->updateUser($user);

        if ($this->getCurrentUser() && $this->getCurrentUser()->hasRole(User::ROLE_USER)) {
            $data = ['currentUser' => $this->getCurrentUser()];
            $this->eventDispatcher->dispatch(RegistrationListener::EVENT_POSTREGISTRATION, new UserEvent($user, $data));
        }

        return $user;
    }

    /**
     * @param $accessToken
     * @param User $user
     * @return OauthCredentials
     */
    public function promoteAccessToken($accessToken, User $user)
    {
        $accessToken = $this->authService->getAccessTokenManager()->findTokenByToken($accessToken);
        if (!$accessToken) {
            throw new AuthenticationException('No or invalid access token provided');
        }

        $oauth = $this->authService->getOauth();
        $tokenSet = $oauth->createAccessToken(
            $accessToken->getClient(),
            $user,
            $oauth->getVariable(OAuth2::CONFIG_SUPPORTED_SCOPES, null),
            $oauth->getVariable(OAuth2::CONFIG_ACCESS_LIFETIME),
            true,
            $oauth->getVariable(OAuth2::CONFIG_REFRESH_LIFETIME)
        );

        return new OauthCredentials($tokenSet);
    }

    /**
     * @param User $user
     * @return OauthCredentials
     */
    public function generateNotificationUser(User $user)
    {
        $token = $user->getId() . '_' . bin2hex(mcrypt_create_iv(15, MCRYPT_DEV_URANDOM));
        $user->setNotificationUserId($token);

        $this->updateUser($user);
    }

    /**
     * @param User $user
     */
    public function sendPasswordRecoveryEmail(User $user)
    {
        $user->setConfirmationToken($this->tokenGenerator->generateToken());
        $user->setPasswordRequestedAt(new \DateTime());
        $this->getUserManager()->updateUser($user);

        $recoveryUrl = $this->router->generate('user_password_reset', ['recoveryToken' => $user->getConfirmationToken()]);

        $this->mailerService->send(
            $user,
            'Password reset',
            'UserBundle:Email:passwordreset.html.twig',
            [
                'user' => $user,
                'recovery_url' => rtrim($this->marketingSiteUrl, '/') . $recoveryUrl
            ]
        );
    }

    /**
     * @return User|void
     */
    public function getCurrentUser()
    {
        if (null === $token = $this->tokenStorage->getToken()) {
            return;
        }

        if (!is_object($user = $token->getUser())) {
            return;
        }

        return $user;
    }

    /**
     * @param string $role
     * @return \UserBundle\Entity\User[]
     */
    public function getUsersWithRole($role)
    {
        return $this->userRepository->findByRole($role);
    }

    /**
     * @return void
     */
    public function updateLastActivity()
    {
        $user = $this->getCurrentUser();
        if (!$user) {
            return;
        }

        $user->setLastActivity(new \DateTime());
        $this->updateUser($user);
    }

    /**
     * @return \FOS\UserBundle\Doctrine\UserManager|UserManager
     */
    protected function getUserManager()
    {
        if (!$this->userManager) {
            $this->userManager = $this->authService->getUserManager();
        }

        return $this->userManager;
    }

    public function saveProfilePicture(User $user, $profilePicture)
    {
    	//$profilePicture = 'https://www.e-sites.nl/assets/images/logos/tde-logo.png';
    	$profilePictureDir = $this->uploadPath . '/profile-pictures/';
		if (!is_dir($profilePictureDir)) {
			mkdir($profilePictureDir, 0775);
		}
	    $ch = curl_init();
	    curl_setopt($ch, CURLOPT_URL, $profilePicture);
	    curl_setopt($ch, CURLOPT_HEADER, 0);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	    $data = curl_exec ($ch);
	    $error = curl_error($ch);
	    $info = curl_getinfo($ch);

	    curl_close ($ch);

	    $extension = null;
		if (!empty($info['content_type'])) {
			$extension = ExtensionGuesser::getInstance()->guess($info['content_type']);
		}

		if (!$extension) {
			$extension = 'jpeg';
		}
	    $fileName = md5(uniqid()) . '.' . $extension;
	    $picturePath = $profilePictureDir . $fileName;

	    if ($info['http_code'] != 200) {
	    	return false;
	    }

	    $file = fopen($picturePath, "w+");
	    fputs($file, $data);
	    fclose($file);

	    if (file_exists($picturePath)) {
	    	// check if we have a old one and delete it
	    	if ($user->getProfilePicture() && file_exists($profilePictureDir . $user->getProfilePicture())) {
				unlink($profilePictureDir . $user->getProfilePicture());
		    }
		    // set the new one
		    $user->setProfilePicture($fileName);
			$this->getUserManager()->updateUser($user);
	    }
    }


}
