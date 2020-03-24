<?php
/**
 * Created by PhpStorm.
 * User: mdubbelman
 * Date: 9/12/16
 * Time: 2:17 PM
 */

namespace AppBundle\Controller;

use ApiBundle\Exception\FailRegistry;
use ApiBundle\Responses\FailResponse;
use ApiBundle\Responses\GeneralFailResponse;
use ApiBundle\Responses\SuccessResponse;
use AppBundle\Entity\Goal;
use AppBundle\Entity\GoalGroup;
use AppBundle\Entity\GroupInvite;
use AppBundle\Entity\GroupUser;
use AppBundle\Entity\Task;
use AppBundle\Entity\UserGoal;
use AppBundle\Entity\UserGoalCharity;
use AppBundle\Event\GoalEvent;
use AppBundle\Event\GoalEvents;
use AppBundle\Exception\Payment\CardNotFound;
use AppBundle\Exception\Payment\CustomerNotFound;
use AppBundle\Repository\UserGoalRepository;
use AppBundle\Responses\GoalList;
use AppBundle\Responses\UserGoalDetail;
use AppBundle\Responses\UserGoalList;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use FOS\RestBundle\View\View;
use AppBundle\Responses\GoalDetail;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use ApiBundle\Controller\AbstractApiController;
use Symfony\Component\HttpKernel\Exception\HttpException;
use UserBundle\Entity\User;


class GoalController extends AbstractApiController
{

    /**
     * @ApiDoc(
     *     section="Goals",
     *     resource=true,
     *     description="This call returns the details of the given goal.",
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
    public function detailAction(Request $request, Goal $goal)
    {
        $uploadPath = $this->getParameter('data_upload_web');
        $uploadPath = $request->getSchemeAndHttpHost() . $uploadPath;

        return $this->createApiResponse(new GoalDetail($goal, $uploadPath,
          $goal->getMilestones()), 'details');
    }

    /**
     * @ApiDoc(
     *     section="Goals",
     *     resource=true,
     *     description="This call returns the details of the given goal with
     *   newly generated milestones. The new milestones are not persisted. ",
     *   output="ApiBundle\Responses\SuccessResponse", tags={
     *         "anonymous" = "#408000"
     *     }
     * )
     *
     * @param Request $request
     *
     * @return View
     */
    public function retryDetailAction(Request $request, Goal $goal)
    {
        $uploadPath = $this->getParameter('data_upload_web');
        $uploadPath = $request->getSchemeAndHttpHost() . $uploadPath;

        $milestones = $goal->getMilestones();
        if ($goal->getDeadline()) {
            $goal->setStartDate(new \DateTime());
            $goal->setDescription($this->getGoalDescription($goal));
            $goal->clearMilestones();
            $milestones = $this->get('app.service.goal')
              ->generateGoalMileStones($this->get('doctrine.orm.default_entity_manager'),
                $goal, false);
        }

        return $this->createApiResponse(new GoalDetail($goal, $uploadPath,
          $milestones, 'details'));
    }

    /**
     * @param Goal $goal
     *
     * @return string
     */
    private function getGoalDescription(Goal $goal)
    {
        $milestones = $goal->getMilestones();
        if ($milestones->count() > 0) {
            /** @var ArrayCollection $tasks */
            $tasks = $milestones->first()->getTasks();

            return $tasks->count() > 0 ? $tasks->first()->getTitle() : "";
        }

        return "";
    }

    /**
     * @ApiDoc(
     *     section="User",
     *     resource=true,
     *     description="This call returns all the goals the user has
     *   anticipated.", output="ApiBundle\Responses\SuccessResponse", tags={
     *         "anonymous" = "#408000"
     *     }
     * )
     *
     * @param Request $request
     *
     * @return View
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        /** @var User $user */
        $user = $this->getUser();
        $userGoals = $em->getRepository('AppBundle:UserGoal')
          ->findAllUserGoals($user);

        $basePath = $this->getWebUploadPath() . '/' . \AppBundle\Service\GoalService::GOAL_UPLOAD_DIR . '/';

        return $this->createApiResponse(

          new UserGoalList($userGoals, $basePath), 'list');
    }

    /**
     * @ApiDoc(
     *     section="Goals",
     *     resource=true,
     *     requirement="json",
     *     description="This call creates a new custom goal.",
     *     parameters={
     *          { "name"="name", "dataType"="string", "required"=true,
     *   "description"="Title of the custom goal"},
     *          { "name"="recurrence", "dataType"="string", "required"=true,
     *   "description"="Every week or month"},
     *          { "name"="iteration", "dataType"="string", "required"=true,
     *   "description"="Number of tasks per recurrence"},
     *          { "name"="checkinDescription", "dataType"="string",
     *   "required"=true, "description"="Checkin description"},
     *          { "name"="startDate", "dataType"="string", "required"=true,
     *   "description"="Start date for custom goal, default is now"},
   *          { "name"="deadline", "dataType"="string", "required"=true,
   *   "description"="Deadline for custom goal"}
     *      },
     *     output="AppBundle\Responses\SuccessResponse",
     *     tags={
     *         "anonymous" = "#408000"
     *     }
     * )
     *
     * @param Request $request
     *
     * @return View
     */
    public function createAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        if ($missingParameters = $this->assertParameters([
          'name',
          'recurrence',
          'iteration',
          'checkinDescription',
          'deadline',
        ])) {
            return $this->createApiResponse(new FailResponse(FailRegistry::GENERAL_MISSING_PARAMETERS,
              $missingParameters));
        }

        $now = new \DateTime('NOW', $this->getTimezoneFromRequest());

        $name = $request->request->get('name');
        $recurrence = $request->request->get('recurrence');
		$iteration = $request->request->getInt('iteration');
		$checkinDescription = $request->request->get('checkinDescription');
		$points = $request->request->getInt('points', Task::DEFAULT_POINTS);

		$deadline = \DateTime::createFromFormat(
        	DATE_ATOM,
			$request->request->get('deadline')
		);

		$startDate = \DateTime::createFromFormat(
			DATE_ATOM,
			$request->request->get('startDate', $now->format(DATE_ATOM))
		);

		$deadline->setTimezone(new \DateTimeZone('+00:00'));
		$startDate->setTimezone(new \DateTimeZone('+00:00'));

        //Always set to end of day
        //$deadline->setTime(23,55,59);

        $goal = new Goal();

        $goal->setTitle($name);
        $goal->setRecurrence($recurrence);
        $goal->setIteration($iteration);
        $goal->setDescription($checkinDescription);
        $goal->setStartDate($startDate);
        $goal->setDeadline($deadline);
        $goal->setUser($this->getUser());
        $goal->setPoints($points);


        $goalService = $this->getGoalService();

        $duration = $goalService->getGoalDuration($goal);
        $durationUnit = $recurrence === 'week' ? 'week' : 'month';

        $goal->setDuration($duration);
        $goal->setDurationUnit($durationUnit);
        $em->persist($goal);

        $goalService->generateGoalMileStones($em, $goal);

        //After goal creation empty goal description, because this is only used for the task title.
        $goal->setDescription('');

        $em->flush();

        $uploadPath = $this->getParameter('data_upload_web');
        $uploadPath = $request->getSchemeAndHttpHost() . $uploadPath . '/';

        return $this->createApiResponse(new GoalDetail($goal, $uploadPath,
          $goal->getMilestones()), 'details');
    }

    protected function getFormErrors($form)
    {
        $errors = [];

        if ($form instanceof Form) {
            foreach ($form->getErrors() as $error) {
                $errors[] = $error->getMessage();
            }

            foreach ($form->all() as $key => $child) {
                /** @var $child Form */
                if ($err = $this->getFormErrors($child)) {
                    $errors[$key] = $err;
                }
            }
        }

        return $errors;
    }

    /**
     * @ApiDoc(
     *     section="Goals",
     *     resource=true,
     *     description="With this call the user can use the given goal.",
     *     parameters={
     *          { "name"="amount", "dataType"="integer", "required"=false,
     *   "description"="Amount for the stakes. Image or amount is required."},
     *          { "name"="image", "dataType"="file", "required"=false,
     *   "description"="Image to use when a goal fails. Image or amount is
     *   required."},
     *          { "name"="upload_image_later", "dataType"="boolean",
     *   "required"=false, "description"="The user has selected an image in the
     *   app, but it will be uploaded later."},
     *          { "name"="maximumFails", "dataType"="string", "required"=true,
     *   "description"="Maximum amount of failed check-ins."},
     *          { "name"="charities", "dataType"="array", "required"=false,
     *   "description"="Array of charities, with the id and percentage",
     *   "format"="
    Array
    (
    [0] => Array
    (
    [id] => 1
    [percentage] => 80
    )
    [1] => Array
    (
    [id] => 2
    [percentage] => 20
    )
    )
    "},
     *          { "name"="globalStake", "dataType"="int", "required"=false,
     *   "description"="Whether the amount is for the entire goal or for each
     *   check in. 1 for true, 0 for false"},
     *          { "name"="currency", "dataType"="string", "required"=false,
     *   "format"="EUR|USD", "description"="Currency for the stakes"},
     *          { "name"="supervisor", "dataType"="string", "required"=false,
     *   "description"="Email address of the supervisor"},
     *          { "name"="groupId", "dataType"="integer", "required"=false,
     *   "description"="The group id from the invitations."},
     *          { "name"="groupGoal", "dataType"="array", "required"=false,
     *   "description"="Array of group including guests, do not use in groupId
     *   combination!", "format"="
    Array
    (
    [name] => 'Group name',
    [startDate => 'yyyy-mm-dd'],
    [guests] => Array
    (
    [0] => 'ngeleedst@e-sites.nl',
    [1] => 'ngeleedst%1@e-sites.nl'
    )
    )
    "},
     *      },
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
    public function useGoalAction(Request $request, Goal $originGoal)
    {
        $amount = $request->request->get('amount');
        $groupGoal = json_decode($request->request->get('groupGoal'), true);
        /** @var UploadedFile $image */
        $image = $request->files->get('image');
        $requiresCheckinImage = $request->request->get('requiresCheckinImage',
          true);

        /** @var User $user */
        $user = $this->getUser();
        $groupId = $request->request->getInt('groupId', false);

        if (!$user) {
            return $this->createApiResponse(new FailResponse(FailRegistry::AUTHORIZATION_USER_NOT_LOGGED_IN));
        }

        if (!$this->checkParamCombination($request)) {
            return $this->createApiResponse(new FailResponse(FailRegistry::GENERAL_MISSING_PARAMETERS,
              ['Please set a stake, select an embarrassing picture or supply a supervisor e-mail']));
        }

        $maximumFails = $request->request->get('maximumFails');
        if ($maximumFails === null) {
            return $this->createApiResponse(new FailResponse(FailRegistry::GENERAL_MISSING_PARAMETERS,
              ['maximumFails']));
        }

        $goalService = $this->getGoalService();

        $goal = $originGoal;
        //for group goals no need to create new goal. 15/05/19 Pathi
        if (!$groupId) {
          $goal = $goalService->clonePredefinedGoal($originGoal);
        }
        
        /** @var EntityManager $em */
        $em = $this->get('doctrine.orm.default_entity_manager');

        $userGoal = new UserGoal();
        $userGoal->setUser($user);
        $userGoal->setGoal($goal);
        $userGoal->setRequiresCheckinImage($requiresCheckinImage);

        //Check if user is participating an existing group goal, if this is the case copy charities and other information.
        if ($groupId) {
            /** @var GoalGroup $group */
            if (!$group = $em->getRepository('AppBundle:GoalGroup')
              ->find($groupId)) {
                return $this->createApiResponse(new FailResponse(FailRegistry::GENERAL_GROUP_USER_NOT_EXIST,
                  ['Group not found.']));
            }

            $userGoal->setGroup($group);

            //Fetch group admin user goal to couple charities and such.
            $parentUserGoal = $em->getRepository('AppBundle:UserGoal')
              ->findOneBy([
                'group' => $group,
                'user' => $group->getAdmin(),
              ]);

            if (!$parentUserGoal) {
                return $this->createApiResponse(new FailResponse(FailRegistry::GENERAL_USER_GOAL_NOT_EXIST,
                  ['Parent user goal not found. Not able to copy settings from parent user goal.']));
            }

            $userGoal->setStartDate($parentUserGoal->getStartDate());
            $userGoal->setSuperVisor(null);
            $userGoal->setActive(true);

            if ($parentUserGoal->getStakeAmount()) {

                $userGoal->setStakeAmount($parentUserGoal->getStakeAmount());
                $userGoal->setCurrency($parentUserGoal->getCurrency());
                $userGoal->setStakeAmount($parentUserGoal->getStakeAmount());
                $userGoal->setGlobalStake($parentUserGoal->getGlobalStake());


                if (!empty($parentUserGoal)) {
                    /** @var UserGoalCharity $parentUserGoalCharity */
                    foreach ($parentUserGoal->getUserGoalCharities() as $parentUserGoalCharity) {
                        $userGoalCharity = new UserGoalCharity();
                        $userGoalCharity->setUserGoal($userGoal);
                        $userGoalCharity->setCharity($parentUserGoalCharity->getCharity());
                        $userGoalCharity->setPercentage($parentUserGoalCharity->getPercentage());
                        $userGoal->addUserGoalCharity($userGoalCharity);
                    }
                }
            }

            $userGoal->setMaximumFails($parentUserGoal->getMaximumFails());

            /**
             * Update invitation entity to enrolled so it is not showing up in the dashboard anymore.
             *
             * @var GroupUser $invitation
             */
            $invitation = $em->getRepository('AppBundle:GroupUser')
              ->findOneBy(['user' => $user, 'group' => $group]);
            if ($invitation) {
                $invitation->setEnrolled(true);
            }
        }

        /**
         * Only add create a new group goal if the group not exist, if the groupId param is set the user is participating
         * a group that already exist. @todo: move to seperate function, cleanup controller!
         */
        if ($groupGoal && !$groupId) {
            if (!($groupGoal['startDate'])) {
                return $this->createApiResponse(new FailResponse(FailRegistry::GENERAL_MISSING_PARAMETERS,
                  ['startDateMissing']));
            }

            //Create new group goal and set admin
            $goalGroup = new GoalGroup();
            $goalGroup->setName($groupGoal['name']);
            $goalGroup->setGoal($goal);
            $userGoal->setGroup($goalGroup);

            $userGoal->setStartDate(new \DateTime($groupGoal['startDate'],
              $this->getTimezoneFromRequest()));

            $goalGroup->setAdmin($user);

            if (!is_array($groupGoal['guests'])) {
                return $this->createApiResponse(new FailResponse(FailRegistry::GENERAL_MISSING_PARAMETERS,
                  ['guestsMissing']));
            }

            //Add attendees to group_invites.
            foreach ($groupGoal['guests'] as $guest) {
                if (filter_var($guest, FILTER_VALIDATE_EMAIL)) {
                    $invite = new GroupInvite();
                    $invite->setEmail($guest);
                    $invite->setGroup($goalGroup);

                    $goalGroup->addInvitation($invite);
                }
            }

            $this->getEventDispatcher()
              ->dispatch(GoalEvents::GOAL_GROUP_ATTACH_EXISTING_USERS,
                new GoalEvent($userGoal)); //Check if user exist and if exist attach
        }

        $userGoal->setMaximumFails($maximumFails);

        $charityRepo = $em->getRepository('AppBundle:Charity');

        if ($amount && !$groupId) {
            if (!$currency = $request->request->get('currency')) {
                return $this->createApiResponse(new FailResponse(FailRegistry::GENERAL_MISSING_PARAMETERS,
                  ['currency; when stakes are set, a valid currency parameter is required']));
            }

            if (!in_array($currency, ['EUR', 'USD'])) {
                return $this->createApiResponse(new FailResponse(FailRegistry::GENERAL_MISSING_PARAMETERS,
                  ['currency; when stakes are set, a valid currency parameter is required']));
            }

            $userGoal->setCurrency($currency);

            if (!$charities = $request->request->get('charities')) {
                return $this->createApiResponse(new FailResponse(FailRegistry::GENERAL_MISSING_PARAMETERS,
                  ['charities; when stakes are set, the charities parameter is required']));
            }
            $userGoal->setStakeAmount((int)$amount);

            if (($isGlobalStake = $request->request->get('globalStake')) === null) {
                return $this->createApiResponse(new FailResponse(FailRegistry::GENERAL_MISSING_PARAMETERS,
                  ['globalStake; when stakes are set, the globalStake parameter is required']));
            }

            $userGoal->setGlobalStake((bool)$isGlobalStake);

            foreach ($charities as $tmpCharity) {
                if (!isset($tmpCharity['id'])) {
                    return $this->createApiResponse(new FailResponse(FailRegistry::GENERAL_MISSING_PARAMETERS,
                      ['id, missing id of charity']));
                }
                if (!isset($tmpCharity['percentage'])) {
                    return $this->createApiResponse(new FailResponse(FailRegistry::GENERAL_MISSING_PARAMETERS,
                      ['percentage, missing percentage of charity']));
                }
                $charity = $charityRepo->find((int)$tmpCharity['id']);

                $userGoalCharity = new UserGoalCharity();
                $userGoalCharity->setUserGoal($userGoal);
                $userGoalCharity->setPercentage($tmpCharity['percentage']);
                $userGoalCharity->setCharity($charity);
                $em->persist($userGoalCharity);

                $userGoal->addUserGoalCharity($userGoalCharity);
            }
        }

        if ($supervisor = $request->request->get('supervisor')) {
            $supervisor = filter_var($supervisor, FILTER_VALIDATE_EMAIL);
            if (!$supervisor) {
                return $this->createApiResponse(new FailResponse(FailRegistry::GENERAL_MISSING_PARAMETERS,
                  ['supervisor; when a supervisor is provided, it must be a valid email address']));
            }
            $userGoal->setSuperVisor($supervisor);
        }

        $goalService->saveUserGoal($userGoal, $image);
        $basePath = $this->getWebUploadPath() . '/' . \AppBundle\Service\GoalService::GOAL_UPLOAD_DIR . '/';

        $this->getEventDispatcher()
          ->dispatch(GoalEvents::GOAL_SUBSCRIBED,
            new GoalEvent($userGoal)); //Send post goal subscribed events

        return $this->createApiResponse(new UserGoalDetail($userGoal,
          $basePath), 'details');
    }

    /**
     * Validate the supplied parameters meet the requirements for a valid stake
     *
     * Valid Combinations:
     * Money
     * Supervisor
     * Image
     * or any combination of the above
     *
     * @param Request $request
     *
     * @return int
     */
    private function checkParamCombination(Request $request)
    {
        $parameters = $request->request;

        $moneyStake = $parameters->get('amount') !== null && $parameters->get('amount') > 0;
        $supervisor = $parameters->get('supervisor') !== null;
        $imageStake = $parameters->get('upload_image_later');

        return $moneyStake | $supervisor | $imageStake;
    }

    /**
     * @ApiDoc(
     *     section="Goals",
     *     resource=true,
     *     description="Update the given usergoal to add an image or payment",
     *     parameters={
     *          { "name"="image", "dataType"="file", "required"=false,
     *   "description"="Image to use when a goal fails. Image or amount is
     *   required."},
     *          { "name"="source", "dataType"="string", "required"=false,
     *   "description"="Stripe payment source for use when charging"},
     *      },
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
    public function updateUserGoalAction(Request $request, UserGoal $userGoal)
    {
        if ($this->getUser()->getId() != $userGoal->getUser()->getId()) {
            throw new HttpException(403,
              'You are not allowed to view this usergoal');
        }

        /** @var UploadedFile $image */
        $image = $request->files->get('image');
        $amount = $request->request->get('amount');

        if (!empty($amount) || $amount === '0') {
            $amount = (int)$amount;
            $userGoal->setStakeAmount($amount);
        }

        if ($paymentSource = $request->request->get('source')) {

            try {
                if ($this->get('app.service.stripe_payment')
                  ->retreiveCard($userGoal->getUser()->getPaymentUser(),
                    $paymentSource)) {
                    $userGoal->setPaymentToken($paymentSource);
                }
            } catch (CustomerNotFound $e) {
                return $this->createApiResponse(new GeneralFailResponse(FailRegistry::PAYMENT_GENERAL_STRIPE_ERROR,
                    'Stripe customer not found. Did you forget to create one? Stripe exception: "' . $e->getMessage() . '"'));
            } catch (CardNotFound $e) {
                return $this->createApiResponse(new GeneralFailResponse(FailRegistry::PAYMENT_GENERAL_STRIPE_ERROR,
                    'Card not found for user. Stripe exception: "' . $e->getMessage() . '"'));
            }
        }

        $this->get('app.service.goal')->saveUserGoal($userGoal, $image);
        $basePath = $this->getWebUploadPath() . '/' . \AppBundle\Service\GoalService::GOAL_UPLOAD_DIR . '/';

        return $this->createApiResponse(new UserGoalDetail($userGoal,
          $basePath), 'details');
    }

    /**
     * @ApiDoc(
     *     section="Goals",
     *     resource=true,
     *     description="Get succeeded goal detail",
     *     output="ApiBundle\Responses\SuccessResponse",
     *     tags={
     *         "anonymous" = "#408000"
     *     }
     * )
     *
     * @param UserGoal $userGoal
     *
     * @return View
     *
     */
    public function succeededGoalAction(UserGoal $userGoal)
    {
        $userGoalsService = $this->get('app.service.user_goal');

        $failedTasks = $userGoalsService->countFailedTasks($userGoal);

        $basePath = $this->getWebUploadPath() . '/' . \AppBundle\Service\GoalService::GOAL_UPLOAD_DIR . '/';

        return $this->createApiResponse(

          new UserGoalDetail($userGoal, $basePath, $failedTasks), 'details');
    }

    /**
     * @ApiDoc(
     *     section="Goals",
     *     resource=true,
     *     description="Get failed goals for dashboard",
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
    public function failedGoalsAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        /** @var UserGoalRepository $repo */
        $repo = $em->getRepository('AppBundle:UserGoal');

        $failedGoals = $repo->getFailedGoals($this->getUser());

        $uploadPath = $this->getParameter('data_upload_web');
        $uploadPath = $request->getSchemeAndHttpHost() . $uploadPath . '/';

        return $this->createApiResponse(new GoalList($failedGoals, $uploadPath),
          'list');
    }

    /**
     * @ApiDoc(
     *     section="Goals",
     *     resource=true,
     *     description="Get succeeded goals for dashboard",
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
    public function succeededGoalsAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        /** @var UserGoalRepository $repo */
        $repo = $em->getRepository('AppBundle:UserGoal');

        $succeededGoals = $repo->getSucceededGoals($this->getUser());

        $uploadPath = $this->getParameter('data_upload_web');
        $uploadPath = $request->getSchemeAndHttpHost() . $uploadPath . '/';

        return $this->createApiResponse(new GoalList($succeededGoals,
          $uploadPath), 'list');
    }

    /**
     * @ApiDoc(
     *     section="Goals",
     *     resource=true,
     *     description="Close goal to hide on dashboard",
     *     output="ApiBundle\Responses\SuccessResponse",
     *     tags={
     *         "anonymous" = "#408000"
     *     }
     * )
     *
     * @param UserGoal $userGoal
     *
     * @return View
     */
    public function closeGoalAction(UserGoal $userGoal)
    {
        if ($userGoal->getUser() !== $this->getUser()) {
            return $this->createApiResponse(new SuccessResponse('You could not close this Goal'));
        }

        $em = $this->getDoctrine()->getManager();
        $userGoal->setShowInDashboard(false);

        $em->persist($userGoal);
        $em->flush();

        return $this->createApiResponse(new SuccessResponse('UserGoal closed'));
    }

    /**
     * @return object|\Symfony\Component\EventDispatcher\ContainerAwareEventDispatcher|\Symfony\Component\HttpKernel\Debug\TraceableEventDispatcher
     */
    protected function getEventDispatcher()
    {
        return $this->get('event_dispatcher');
    }
}