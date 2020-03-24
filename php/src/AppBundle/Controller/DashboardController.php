<?php
/**
 * Created by PhpStorm.
 * User: ngeleedst
 * Date: 9/12/16
 * Time: 2:17 PM
 */

namespace AppBundle\Controller;

use AppBundle\Entity\UserGoal;
use AppBundle\Responses\DashboardList;
use Doctrine\ORM\EntityManager;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpFoundation\Request;
use ApiBundle\Controller\AbstractApiController;

class DashboardController extends AbstractApiController
{

    /**
     * A combined list with failed goals, invites & checkins
     * @ApiDoc(
     *     section="User",
     *     resource=true,
     *     description="A combined list with failed goals, invites & checkins",
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
    public function dashboardAction(Request $request)
    {
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();

        //Get group invitations
        $date = new \DateTime();
        $clientDate = $this->getClientDateTime();
        $date->setDate($clientDate->format('Y'), $clientDate->format('m'),
          $clientDate->format('d'));
        $invites = $this->getGroupInvitations($em, $date);

        //Get Todays checkins
        $checkins = $this->getTodaysCheckins($em, $clientDate);

        //Get Failed goals
        $failedGoals = $em->getRepository('AppBundle:UserGoal')
          ->getFailedUserGoals($this->getUser());
        $succeededGoals = $em->getRepository('AppBundle:UserGoal')
          ->getSucceededUserGoals($this->getUser());

        return $this->createApiResponse(new DashboardList($invites, $checkins,
          $failedGoals, $succeededGoals, $date,
          $this->generateBaseUrl($request),
          $this->get('app.service.user_goal')), 'details');
    }

    /**
     * @param EntityManager $em
     *
     * @return array
     */
    protected function getGroupInvitations(EntityManager $em)
    {
        $invites = [];
        $groupGoals = $em->getRepository('AppBundle:GroupUser')->findBy([
          'user' => $this->getUser(),
          'enrolled' => 0,
          'rejected' => 0,
        ]);

        /**
         * @var \AppBundle\Entity\GroupUser $group
         */
        foreach ($groupGoals as $group) {
            $userGoal = $em->getRepository('AppBundle:UserGoal')->findOneBy([
              'group' => $group->getGroup(),
              'user' => $group->getGroup()->getAdmin(),
            ]);

            $thisMorning = new \DateTime;
            $thisMorning->setTime(00, 00, 00);

            //If user goal not exist
            if (!empty($userGoal)) {
                if ($userGoal->getStartDate() >= $thisMorning) {
                    $invites[] = [
                      'groupUser' => $group->getGroup(),
                      'userGoal' => $userGoal,
                    ];
                }
            }
        }

        return $invites;
    }

    /**
     * @param EntityManager $em
     *
     * @return array
     */
    protected function getTodaysCheckins(EntityManager $em, \DateTime $date)
    {
        /** @var \AppBundle\Repository\CheckinRepository $repo */
        $repo = $em->getRepository('AppBundle:Checkin');

        $checkins = $repo->getCheckinsForDate($this->getUser(), $date);

        return $checkins;
    }
}