<?php
/**
 * Created by PhpStorm.
 * User: mdubbelman
 * Date: 9/28/16
 * Time: 11:28 AM
 */

namespace AppBundle\Controller;

use ApiBundle\Responses\FailResponse;
use ApiBundle\Responses\SuccessResponse;
use AppBundle\Entity\PaymentCharge;
use AppBundle\Exception\Payment\CustomerNotFound;
use AppBundle\Service\GoalService;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use ApiBundle\Controller\AbstractApiController;
use ApiBundle\Exception\FailRegistry;
use ApiBundle\Responses\GeneralFailResponse;
use AppBundle\Responses\StripeCustomerDetail;
use FOS\RestBundle\View\View;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use UserBundle\Entity\User;

class TestController extends Controller
{
	public function indexAction()
	{
		$em = $this->get('doctrine.orm.entity_manager');
		/** @var GoalService $goalService */
		$goalService = $this->get('app.service.goal');
		$userGoal = $em->getRepository('AppBundle:UserGoal')->find(21);
		//$goalService->mailSuperVisor($userGoal);


		$data = [];
		$pendingCheckins = $this->get('doctrine.orm.entity_manager')->getRepository('AppBundle:SupervisorCheckinTokens')->findApprovals();

		/** @var SupervisorCheckinTokens $pendingCheckin */
		foreach ($pendingCheckins as $pendingCheckin) {

				$data[$pendingCheckin->getUserGoal()->getId()][] = $pendingCheckin;

		}
		foreach ($data as $item) {
			$this->get('app.service.mailer')->sendCheckinListToSupervisor('martin@e-sites.nl', 'Accept user checkins', ':email:supervisor_checkin_list_check.html.twig', ['item' => $item]);
		}


		return new Response('ok');

		$item = $userGoal;


		die;
		$userGoalService = $this->get('app.service.user_goal');
		$em = $this->get('doctrine.orm.default_entity_manager');
		$userGoal = $em->getRepository('AppBundle:UserGoal')->find(265);
		$paymentCharge = $userGoalService->charge($userGoal);
		dump($paymentCharge);
		echo "\n<br>@".__FILE__.':'.__LINE__;
		die;
	}
}