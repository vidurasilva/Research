<?php
/**
 * Created by PhpStorm.
 * User: ngeleedst
 * Date: 9/12/16
 * Time: 2:17 PM
 */
namespace AppBundle\Controller;

use ApiBundle\Responses\GeneralFailResponse;
use ApiBundle\Responses\SuccessResponse;
use AppBundle\Entity\SupervisorCheckinTokens;
use AppBundle\Entity\Task;
use AppBundle\Entity\Checkin;
use AppBundle\Repository\CheckinRepository;
use AppBundle\Responses\CheckinDetail;
use AppBundle\Responses\TaskDetail;
use AppBundle\Responses\TimelineList;
use Doctrine\ORM\EntityManager;
use FOS\RestBundle\View\View;
use AppBundle\Entity\UserGoal;
use AppBundle\Responses\CheckinList;
use ApiBundle\Responses\FailResponse;
use ApiBundle\Exception\FailRegistry;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use ApiBundle\Controller\AbstractApiController;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\File\File;

class CheckinController extends AbstractApiController
{
    /**
     * List all he daily check-ins that are available.
     * @ApiDoc(
     *     section="User",
     *     resource=true,
     *     description="This call returns the user available check-ins for today.",
     *     output="ApiBundle\Responses\SuccessResponse",
     *     tags={
     *         "anonymous" = "#408000"
     *     }
     * )
     *
     * @param Request $request
     * @return View
     */
    public function availableUserCheckinsAction(Request $request)
    {
        $date       = new \DateTime();
        $clientDate = $this->getClientDateTime();
        $date->setDate($clientDate->format('Y'), $clientDate->format('m'), $clientDate->format('d'));

        $checkins = $this->getDoctrine()->getRepository('AppBundle:Checkin')->getCheckinsForDate($this->getUser(), $date);

        return $this->createApiResponse(
            new CheckinList($checkins, $date, $this->generateBaseUrl($request)),
            'list'
        );
    }

    /**
     * Get the timeline of the given usergoal.
     * @ApiDoc(
     *     section="User",
     *     resource=true,
     *     description="This call returns timeline for the given UserGoal",
     *     output="ApiBundle\Responses\SuccessResponse",
     *     tags={
     *         "anonymous" = "#408000"
     *     }
     * )
     *
     * @param Request $request
     * @return View
     */
    public function checkinTimelineAction(Request $request, UserGoal $userGoal)
    {
        $em = $this->get('doctrine.orm.default_entity_manager');
        $finishedTasks = $em->getRepository('AppBundle:Checkin')->getDoneTasksForUserGoal($userGoal);
        $nextCheckin = $em->getRepository('AppBundle:Checkin')->findNextUserGoalCheckin($userGoal, $this->getClientDateTime());
        //$test = $em->getRepository('AppBundle:Checkin')->findUnfinishedMilestoneTasks($userGoal, $this->getClientDateTime());

        return $this->createApiResponse(
            new TimelineList(
                $userGoal,
                $this->generateBaseUrl($request),
                $finishedTasks,
                $nextCheckin
            ),
            'list'
        );
    }

    /**
     * Add a check-in
     * @ApiDoc(
     *     section="User",
     *     resource=true,
     *     description="Add check-in",
     *     output="ApiBundle\Responses\SuccessResponse",
     *     tags={
     *         "anonymous" = "#408000"
     *     },
     *     parameters={
     *          { "name"="image", "dataType"="file", "required"=false, "description"="Add an image as prove for the
     *     supervisor."}
     *     }
     * )
     *
     * @param Request $request
     * @return View
     */
    public function checkInAction(Request $request, $userGoalId, $taskId)
    {
        $approved = true;
        $em       = $this->get('doctrine.orm.default_entity_manager');

        /** @var CheckinRepository $checkinRepostory */
        $checkinRepostory = $em->getRepository('AppBundle:Checkin');

        //Check if user is allowed to checkin this task now.
        if ($checkinRepostory->checkIfCheckInTodayAndUnique($userGoalId, $taskId, $em->getConnection(), $this->getClientDateTime())) {
            return $this->createApiResponse(new FailResponse(FailRegistry::GENERAL_DAILY_LIMIT));
        }

        /** @var UserGoal $userGoal */
        $userGoal = $em->getRepository('AppBundle:UserGoal')->find($userGoalId);

        if (!$userGoal) {
            throw new BadRequestHttpException('UserGoal doesn\'t exist');
        }

        $task = $em->getRepository('AppBundle:Task')->find($taskId);

        if ($userGoal->getSuperVisor() !== NULL) {
            $this->createSupervisorMail($request, $em, $userGoal, $task);
            $approved = false;
        }

        $scoreService = $this->getScoreService();
        $scoreService->setGoalScore($this->getUser(), $userGoal->getGoal(), $task, $userGoal->getGroup());

        $checkin = new Checkin($userGoal, $task, $approved);

        $em->persist($checkin);
        $em->flush();

        // Update userGoal status failed or finished.
        $checkinRepostory->updateGoalStatus($userGoal);

        if ($userGoal->getFinished()) {
            $em->flush($userGoal);
        }

        $task->setUnfinishedMilestoneTasks($checkinRepostory->countUnfinishedMilestoneTasks($task, $userGoal));

        return $this->createApiResponse(new CheckinDetail($task, $userGoal, $this->generateBaseUrl($request) . 'goals/', 'details'));
    }

    public function detailAction(UserGoal $userGoal, Task $task)
    {
        if ($this->getUser()->getId() != $userGoal->getUser()->getId()) {
            throw new AccessDeniedException('User is not the owner of the usergoal');
        }

        if ($task->getMilestone()->getGoal()->getId() != $userGoal->getGoal()->getId()) {
            return $this->createApiResponse(new GeneralFailResponse(FailRegistry::GENERAL_UNKNOWN_ERROR, 'Task does not belong to Goal'));
        }

        $basePath = $this->getWebUploadPath() . '/' . \AppBundle\Service\GoalService::GOAL_UPLOAD_DIR . '/';

        return $this->createApiResponse(new TaskDetail($task, $basePath), 'details');
    }

    /**
     * Add checkin to supervisor checkin table, this checkin needs to be approved.
     * @param $request Request
     * @param $em EntityManager
     * @param $userGoal UserGoal
     * @param $task Task
     */
    protected function createSupervisorMail($request, $em, $userGoal, $task)
    {
        $supervisorCheckinToken = new SupervisorCheckinTokens();
        $acceptToken            = md5(uniqid(mt_rand(), true));
        $declineToken           = md5(uniqid(mt_rand(), true));
        $supervisorCheckinToken->setAcceptToken($acceptToken);
        $supervisorCheckinToken->setDeclineToken($declineToken);
        $supervisorCheckinToken->setTask($task);
        $supervisorCheckinToken->setStatus(SupervisorCheckinTokens::MAIL_OPEN);
        $supervisorCheckinToken->setUserGoal($userGoal);

        $em->persist($supervisorCheckinToken);

		/** @var UploadedFile $image */
		$image = $request->files->get('image', NULL);

		$attachment = NULL;

		if ($image !== NULL) {
			/** @var File $attachment */
			$fileName = md5(uniqid()) . '.' . $image->guessExtension();
			$attachment = $image->move($this->getParameter('data_uploads') . '/checkin/', $fileName);
			$supervisorCheckinToken->setImage($attachment->getFilename());
			$em->persist($supervisorCheckinToken);
		}
    }
}