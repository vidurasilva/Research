<?php
/**
 * Created by PhpStorm.
 * User: ngeleedst
 * Date: 9/12/16
 * Time: 2:17 PM
 */

namespace AppBundle\Controller;


use ApiBundle\Exception\FailRegistry;
use ApiBundle\Responses\FailResponse;
use ApiBundle\Responses\SuccessResponse;
use AppBundle\Entity\GoalGroup;
use AppBundle\Entity\GroupUser;
use AppBundle\Entity\UserGoal;
use AppBundle\Responses\GroupDetail;
use AppBundle\Responses\InviteList;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use ApiBundle\Controller\AbstractApiController;
use Symfony\Component\HttpFoundation\JsonResponse;

class GoalGroupController extends AbstractApiController
{

    /**
     * Group goal detail
     * @ApiDoc(
     *     section="User",
     *     resource=true,
     *     description="Group goal detail",
     *     output="ApiBundle\Responses\SuccessResponse",
     *     tags={
     *         "anonymous" = "#408000"
     *     }
     * )
     *
     * @param Request $request
     *
     * @return View
     */
    public function indexAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        /** @var GoalGroup $group */
        $group = $em->getRepository('AppBundle:GoalGroup')->find($id);
        $userGoal = $em->getRepository('AppBundle:UserGoal')
          ->findOneBy(['group' => $group, 'user' => $group->getAdmin()]);

        $basePath = $this->getWebUploadPath() . '/' . \AppBundle\Service\GoalService::GOAL_UPLOAD_DIR . '/';

        return $this->createApiResponse(new GroupDetail($userGoal, null,
          $basePath), 'details');
    }

    /**
     * Fetches all the group invitations.
     * @ApiDoc(
     *     section="User",
     *     resource=true,
     *     description="This call fetches all the group invitations for the
     *   current user.", output="ApiBundle\Responses\SuccessResponse", tags={
     *         "anonymous" = "#408000"
     *     }
     * )
     *
     * @param Request $request
     *
     * @return View
     */
    public function invitesAction(Request $request)
    {
        $invites = [];
        $em = $this->getDoctrine()->getManager();
        $groupGoals = $em->getRepository('AppBundle:GroupUser')
          ->findBy([
            'user' => $this->getUser(),
            'enrolled' => 0,
            'rejected' => 0,
          ]);

        /**
         * @var GroupUser $group
         */
        foreach ($groupGoals as $group) {
            $userGoal = $em->getRepository('AppBundle:UserGoal')
              ->findOneBy([
                'group' => $group->getGroup(),
                'user' => $group->getGroup()->getAdmin(),
              ]);
            $invites[] = [
              'groupUser' => $group->getGroup(),
              'userGoal' => $userGoal,
            ];
        }

        $basePath = $this->getWebUploadPath() . '/' . \AppBundle\Service\GoalService::GOAL_UPLOAD_DIR . '/';

        return $this->createApiResponse(new InviteList($invites, $basePath),
          'list');
    }

    /**
     * Reject group invitation
     * @ApiDoc(
     *     section="User",
     *     resource=true,
     *     description="This call reject a group invitation",
     *     output="ApiBundle\Responses\SuccessResponse",
     *     tags={
     *         "anonymous" = "#408000"
     *     }
     * )
     *
     * @param Request $request
     * @param $groupUserId integer
     *
     * @return View
     */
    public function rejectAction(Request $request, $groupUserId)
    {
        $em = $this->getDoctrine()->getManager();

        /** @var GroupUser $groupUser */
        $groupUser = $em->getRepository('AppBundle:GroupUser')
          ->findGroupUserGoal($groupUserId, $this->getUser());

        if (!$groupUser) {
            return $this->createApiResponse(new FailResponse(FailRegistry::GENERAL_GROUP_USER_NOT_EXIST));
        }

        $groupUser->setRejected(true);
        $em->flush();

        return $this->createApiResponse(new SuccessResponse('Group goal invitation successfully rejected.'));
    }
}