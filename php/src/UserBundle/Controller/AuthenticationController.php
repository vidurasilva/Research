<?php

namespace UserBundle\Controller;

use ApiBundle\Controller\AbstractApiController;
use ApiBundle\Exception\FailRegistry;
use ApiBundle\Responses\FailResponse;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations\Post;
use UserBundle\Entity\User;
use UserBundle\Responses\OauthUserStorage;
use UserBundle\Service\FacebookService;
use UserBundle\Service\SocialMediaLogin;
use UserBundle\Service\UserService;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

class AuthenticationController extends AbstractApiController
{
    /**
     * @ApiDoc(
     *      section="Auth",
     *      resource=true,
     *      description="Information stored for the user",
     *      output="UserBundle\Responses\OauthUserStorage",
     *      parameters={
     *          { "name"="firstname", "dataType"="string", "required"=true, "description"="Firstname of the user."},
     *          { "name"="lastname", "dataType"="string", "required"=true, "description"="Lastname of the user."},
     *          { "name"="password", "dataType"="string", "required"=true, "description"="Password to used for logging in, minimum 6 characters."},
     *          { "name"="email", "dataType"="string", "required"=true, "description"="E-mail address"},
     *          { "name"="nickname", "dataType"="string", "required"=true, "description"="Nickname of the user."},
     *      },
     *     tags={
     *         "anonymous" = "#408000"
     *     }
     * )
     *
     *
     * @param Request $request
     * @return View
     */
    public function registerAction(Request $request)
    {

        if ($missingParameters = $this->assertParameters(['email', 'firstname', 'lastname', 'password'])) {
            return $this->createApiResponse(new FailResponse(FailRegistry::GENERAL_MISSING_PARAMETERS, $missingParameters));
        }

        $email = $request->get('email');
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $this->createApiResponse(new FailResponse(FailRegistry::GENERAL_INVALID_EMAIL));
        }

        if (strlen($request->get('password')) < User::MIN_LENGTH_PASSWORD) {
            return $this->createApiResponse(new FailResponse(FailRegistry::GENERAL_INVALID_PASSWORD));
        }

        $isNewUser = !$this->getUserService()->userExists($email);

        if (!$isNewUser && (!$this->getUser() || !$this->getUser()->hasRole(User::ROLE_ADMIN))) {
            return $this->createApiResponse(new FailResponse(FailRegistry::AUTHORIZATION_EXISTS_USER));
        }

        if ($isNewUser) {
            $user = $this->getUserService()->createUser(
                $email,
                $request->get('password'),
                $request->get('firstname'),
                $request->get('lastname'),
                $request->get('nickname')
            );
        } else {
            $user = $this->getUserService()->findUserByEmail($request->get('email'));
            $user = $this->getUserService()->synchroniseUserFromRequest($user, $request);
        }

        $oauthTokenSet = $this->getUserService()->promoteAccessToken($this->getAccessTokenFromRequest(), $user);
        return $this->createApiResponse(new OauthUserStorage($user, $oauthTokenSet, $isNewUser));
    }

    /**
     * @ApiDoc(
     *     section="Auth",
     *     resource=true,
     *     description="Synchronize facebook user information",
     *     output="UserBundle\Responses\OauthUserStorage",
     *     parameters={
     *          { "name"="facebook_access_token", "dataType"="string", "required"=true, "description"="Access token received from Facebook. Token must be valid for the pleasepush.me Facebook application. To generate an accesstoken for development, go to /app_dev.php/test/facebooktiontoken"}
     *     },
     *     tags={
     *         "anonymous" = "#408000"
     *     }
     * )
     *
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function facebookAction(Request $request)
    {
	    return $this->authenticateBySocialMedia(
		    $request,
		    $this->getFacebookService(),
		    'facebook_access_token',
		    'getFacebookUserId'
	    );
    }

	/**
	 * @ApiDoc(
	 *     section="Auth",
	 *     resource=true,
	 *     description="Synchronize google user information",
	 *     output="UserBundle\Responses\OauthUserStorage",
	 *     parameters={
	 *          { "name"="google_access_token", "dataType"="string", "required"=true, "description"="Access token received from Google.
                     Token must be valid for the pleasepush.me Google application. To generate an accesstoken for development, go to /app_dev.php/test/googleactiontoken"}
	 *     },
	 *     tags={
	 *         "anonymous" = "#408000"
	 *     }
	 * )
	 *
	 *
	 * @param Request $request
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
    public function googleAction(Request $request)
    {
		return $this->authenticateBySocialMedia(
			$request,
			$this->get('user.service.google'),
			'google_access_token',
			'getGoogleUserId'
		);
    }

    protected function authenticateBySocialMedia(Request $request, SocialMediaLogin $service, $accessTokenParameter, $serviceIdProperty)
    {
	    if ($missingParameters = $this->assertParameters([$accessTokenParameter])) {
		    return $this->createApiResponse(new FailResponse(FailRegistry::GENERAL_MISSING_PARAMETERS, $missingParameters));
	    }

	    $accesstoken = $request->get($accessTokenParameter);
	    if (!$accesstoken) {
		    return $this->createApiResponse(new FailResponse(FailRegistry::GENERAL_MISSING_PARAMETERS, [$accessTokenParameter]));
	    }
	    $user = $service->getUserByAccessToken($accesstoken);

	    if ($user === false && $service->getLastError()) {
		    return $this->createApiResponse(new FailResponse($service->getAuthorizationRegistryFailIndex(), $service->getLastError()));
	    }

	    $originalUser = null;
	    if ($this->getUserService()->userExists($user)) {
		    $originalUser = $this->getUserService()->findUserByUsername($user->$serviceIdProperty())
			    ?: $this->getUserService()->findUserByEmail($user->getEmail());

		    $user = $service->synchroniseUser($originalUser, $user);
		    if ($profilePicture = $service->getProfileImage($user, $accesstoken)) {
			    $this->get('user.service')->saveProfilePicture($user, $profilePicture);
		    }

	    }


	    $this->getUserService()->updateUser($user);
	    $oauthTokenSet = $this->getUserService()->promoteAccessToken($this->getAccessTokenFromRequest(), $user);

	    return $this->createApiResponse(new OauthUserStorage($user, $oauthTokenSet, ($originalUser == null)));
    }

    /**
     * @return UserService
     */
    protected function getUserService()
    {
        return $this->container->get('user.service');
    }

    /**
     * @return FacebookService
     */
    protected function getFacebookService()
    {
        return $this->container->get('user.service.facebook');
    }
}
