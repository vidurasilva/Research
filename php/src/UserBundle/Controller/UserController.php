<?php

namespace UserBundle\Controller;

use ApiBundle\Controller\AbstractApiController;
use ApiBundle\Exception\FailRegistry;
use ApiBundle\Responses\FailResponse;
use ApiBundle\Responses\SuccessResponse;
use FOS\RestBundle\Controller\Annotations\Put;
use FOS\RestBundle\Controller\Annotations\Route;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use FOS\RestBundle\Controller\Annotations\Get;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use UserBundle\Entity\User;
use UserBundle\Repository\Filter\UserFilter;
use UserBundle\Repository\UserRepository;
use UserBundle\Responses\UserDetailResponse;
use UserBundle\Responses\UserList;
use UserBundle\Responses\UserStorage;
use UserBundle\Responses\UserSummary;
use UserBundle\Service\UserService;

class UserController extends AbstractApiController
{
    /**
     * List a user their current preferences.
     *
     * @ApiDoc(
     *     section="User",
     *     resource=true,
     *     description="Get the current user",
     *     output="UserBundle\Responses\UserStorage"
     * )
     *
     */
    public function meAction(Request $request)
    {
    	$user = $this->getUser();
	    if (!$user) {
		    return $this->createApiResponse(new FailResponse(FailRegistry::AUTHORIZATION_UNKNOWN_USER));
	    }
        return $this->createApiResponse(new UserDetailResponse($user, $this->generateBaseUrl($request)));
    }

    /**
     * List a user their current preferences.
     *
     * @ApiDoc(
     *     section="User",
     *     resource=true,
     *     description="Get user by id",
     *     output="UserBundle\Responses\UserStorage"
     * )
     *
     * @param integer $id
     * @return \FOS\RestBundle\View\View
     */
    public function getUserAction($id)
    {
        $user = $this->getUserService()->findUserById($id);
        if (!$user) {
            return $this->createApiResponse(new FailResponse(FailRegistry::AUTHORIZATION_UNKNOWN_USER));
        }

        return $this->createApiResponse(new UserStorage($user));
    }
    /**
     * @ApiDoc(
     *     section="User",
     *     resource=true,
     *     description="Send password recovery e-mail if user found",
     *     output="AppBundle\Responses\SuccessResponse",
     *     parameters={
     *          { "name"="email", "dataType"="string", "required"=true, "description"="Email of the user that wants to reset their password"}
     *     },
     *     tags={
     *         "anonymous" = "#408000"
     *     }
     * )
     *
     * @param Request $request
     * @return \FOS\RestBundle\View\View
     */
    public function requestPasswordAction(Request $request)
    {
        if ($missingParameters = $this->assertParameters(['email'])) {
            return $this->createApiResponse(new FailResponse(FailRegistry::GENERAL_MISSING_PARAMETERS, $missingParameters));
        }

        $user = $this->getUserService()->findUserByUsername($request->get('email'));
        if ($user) {
            $this->getUserService()->sendPasswordRecoveryEmail($user);
        }

        return $this->createApiResponse(new SuccessResponse('User e-mailed if found'));
    }

    /**
     * List a user their current preferences.
     *
     * @ApiDoc(
     *     section="User",
     *     resource=true,
     *     description="Get a list of users",
     *     output="UserBundle\Responses\UserList",
     *     parameters={
     *      { "name"="role", "dataType"="string", "required"=false, "description"="The role the user should have."},
     *     }
     * )
     *
     */
    public function indexAction(Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        /** @var UserRepository $repo */
        $repo = $this->get('doctrine.orm.default_entity_manager')->getRepository(User::class);

        $filter = new UserFilter();
        if ($request->query->has('role')) {
            $filter->role($request->query->get('role'));
        }

        return $this->createApiResponse(
            new UserList($repo->findByFilter($filter))
        );
    }

    /**
     * List a user their current preferences.
     *
     * @ApiDoc(
     *     section="User",
     *     resource=true,
     *     description="Get the current user",
     *     output="UserBundle\Responses\UserDetailResponse",
     * )
     *
     */
    public function detailAction($user)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $entity = $this->getUserService()->findUserById($user);
        if (!$entity) {
            throw $this->createNotFoundException();
        }

        return $this->createApiResponse(new UserDetailResponse($entity));
    }

    /**
     * @return UserService
     */
    protected function getUserService()
    {
        return $this->container->get('user.service');
    }

    /**
     * @return integer
     */
    protected function getRecoveryTokenTTL()
    {
        return $this->container->getParameter('fos_user.resetting.token_ttl');
    }
}
