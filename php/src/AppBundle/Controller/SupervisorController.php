<?php

namespace AppBundle\Controller;

use ApiBundle\Controller\AbstractApiController;
use AppBundle\Entity\Checkin;
use AppBundle\Entity\SupervisorCheckinTokens;
use AppBundle\Entity\Task;
use AppBundle\Entity\UserGoal;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use UserBundle\Entity\User;

class SupervisorController extends AbstractApiController
{

    /**
     * Action for supervisor to decline checkin
     * @ApiDoc(
     *     section="Supervisor",
     *     resource=true,
     *     description="Decline checkin of user",
     *     output="ApiBundle\Responses\SuccessResponse",
     *     tags={
     *         "anonymous" = "#408000"
     *     }
     * )
     *
     * @param User $user
     * @param      $declineToken
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function declineTaskCheckinAction(User $user, $declineToken)
    {
        $em = $this->getDoctrine()->getManager();
        $supervisorCheckin = $em->getRepository('AppBundle:SupervisorCheckinTokens')->findOneBy(
            [
                'declineToken' => $declineToken,
                'status'       => SupervisorCheckinTokens::MAIL_SUCCESS
            ]
        );

        if ($supervisorCheckin instanceof SupervisorCheckinTokens && $supervisorCheckin->getUserGoal()->getUser()->getId() === $user->getId()) {
			/** @var Checkin $checkin */
			$checkin = $em->getRepository('AppBundle:Checkin')->findOneBy(
				['userGoal' => $supervisorCheckin->getUserGoal(), 'task' => $supervisorCheckin->getTask()]
			);

	        /** @var Task $task */
	        $task = $supervisorCheckin->getTask();
			//Subtract points
	        $this->getScoreService()->setGoalScore($user, $supervisorCheckin->getUserGoal()->getGoal(), $task, $supervisorCheckin->getUserGoal()->getGroup(), false);

	        if (!$checkin) {
				return $this->render('@App/Goal/accepted_error.html.twig');
			}

			$checkin->setApproved(false);
			$checkin->setStatus(Checkin::CHECKIN_FAILED);
            $em->remove($supervisorCheckin);

            $em->flush();
        }

        if (!$supervisorCheckin) {
        	$userGoal = new UserGoal();
        } else {
        	$userGoal = $supervisorCheckin->getUserGoal();
        }

	    return $this->render('@App/Goal/declined.html.twig', ['userGoal' => $userGoal]);
    }

    /**
     * Action for supervisor to accept checkin
     * @ApiDoc(
     *     section="Supervisor",
     *     resource=true,
     *     description="Accept checkin of user",
     *     output="ApiBundle\Responses\SuccessResponse",
     *     tags={
     *         "anonymous" = "#408000"
     *     }
     * )
     *
     * @param User $user
     * @param string $acceptToken
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function acceptTaskCheckinAction(User $user, $acceptToken)
    {
        $em = $this->getDoctrine()->getManager();

        $supervisorCheckin = $em->getRepository('AppBundle:SupervisorCheckinTokens')->findOneBy(
            [
                'acceptToken' => $acceptToken,
				'status' => SupervisorCheckinTokens::MAIL_SUCCESS
            ]
        );

        if ($supervisorCheckin instanceof SupervisorCheckinTokens && $supervisorCheckin->getUserGoal()->getUser() === $user) {
            /** @var Task $task */
            $task = $supervisorCheckin->getTask();

            /** @var Checkin $checkin */
            $checkin = $em->getRepository('AppBundle:Checkin')->findOneBy(['userGoal' => $supervisorCheckin->getUserGoal(), 'task' => $task]);

            if (!$checkin) {
	            return $this->render('@App/Goal/accepted_error.html.twig');
            }

            $checkin->setApproved(true);
			$checkin->setStatus(Checkin::CHECKIN_DONE);
            $em->remove($supervisorCheckin);

            $em->flush();

            return $this->render('@App/Goal/accepted.html.twig', ['userGoal' => $supervisorCheckin->getUserGoal()]);
        }

	    return $this->render('@App/Goal/accepted_error.html.twig');
    }

}
