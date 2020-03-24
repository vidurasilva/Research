<?php


namespace AppBundle\Controller;


use ApiBundle\Controller\AbstractApiController;
use AppBundle\Entity\Goal;
use AppBundle\Entity\GoalGroup;
use AppBundle\Entity\Score;
use AppBundle\Entity\UserGoal;
use AppBundle\Repository\ScoreRepository;
use AppBundle\Responses\GlobalLeaderBoard;
use AppBundle\ValueObjects\TotalScore;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpFoundation\Request;

class LeaderBoardController extends AbstractApiController
{

	/**
	 * @ApiDoc(
	 *     section="Leaderboard",
	 *     resource=true,
	 *     description="This call returns the global leaderboard",
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
	public function getGlobalLeaderBoardAction(Request $request)
	{
		/** @var ScoreRepository $repo */
		$em = $this->getDoctrine()->getEntityManager();
		$repo = $em->getRepository('AppBundle:Score');

		$result = $repo->getGlobalUserScore($this->getUser());

		if (count($result)) {
			$userScoreResult = array_shift($result);
			/** @var Score $userScore */
			$userScore = array_shift($userScoreResult);

			$userScore->setScore((int) $userScoreResult['totalScore']);
		} else {
			$userScore = new Score();
			$userScore->setUser($this->getUser());
			$userScore->setScore(0);
		}

		$userPosition = $repo->getUserPosition($userScore->getScore(), $this->getUser());

		$topScores = $repo->findTopTen();

		$topTen = array();

		foreach ($topScores as $topScore) {
			/** @var Score $score */
			$score = array_shift($topScore);

			$topTen[] = new TotalScore(
				$score,
				(int) $topScore['totalScore']
			);
		}

		return $this->createApiResponse(new GlobalLeaderBoard($userScore, $userPosition, $topTen, $this->generateBaseUrl($request)), 'list');
	}

	/**
	 * @ApiDoc(
	 *     section="Leaderboard",
	 *     resource=true,
	 *     description="This call returns a single goal leaderboard",
	 *     output="ApiBundle\Responses\SuccessResponse",
	 *     tags={
	 *         "anonymous" = "#408000"
	 *     }
	 * )
	 *
	 * @param Request $request
	 *
	 * @param Goal    $goal
	 *
	 * @return View
	 */
	public function getGoalLeaderBoardAction(Request $request, Goal $goal)
	{
		$em = $this->getDoctrine()->getEntityManager();

		$repo = $em->getRepository('AppBundle:Score');

		/** @var Score $userScore */
		$userScore = $repo->findOneBy(
			array(
				'user' => $this->getUser(),
				'goal' => $goal,
				'group_goal' => null,
			)
		);

		$userPosition = $repo->getUserGoalPosition(0, $goal);

		if ($userScore instanceof Score) {
			$userPosition = $repo->getUserGoalPosition($userScore->getScore(), $goal);
		} else {
			$userScore = new Score();
			$userScore->setUser($this->getUser());
			$userScore->setScore(0);
			$userScore->setGroupGoal(null);
			$userScore->setGoal($goal);
		}

		$goalLeaderBoardResult = $repo->getGoalLeaderBoard($goal);

		$leaderBoard = array();

		foreach ($goalLeaderBoardResult as $goalLeaderBoard) {
			/** @var Score $score */
			$score = array_shift($goalLeaderBoard);

			$leaderBoard[] = new TotalScore(
				$score,
				(int) $goalLeaderBoard['totalScore']
			);
		}

		if (count($leaderBoard) === 0) {
			$leaderBoard[] = new TotalScore(
				$userScore,
				(int) $userScore->getScore()
			);
		}

		return $this->createApiResponse(new GlobalLeaderBoard($userScore, $userPosition, $leaderBoard, $this->generateBaseUrl($request)), 'list');
	}

	/**
	 * @ApiDoc(
	 *     section="Leaderboard",
	 *     resource=true,
	 *     description="This call returns a single group goal leaderboard",
	 *     output="ApiBundle\Responses\SuccessResponse",
	 *     tags={
	 *         "anonymous" = "#408000"
	 *     }
	 * )
	 *
	 * @param Request   $request
	 *
	 * @param Goal      $goal
	 * @param GoalGroup $goalGroup
	 *
	 * @return View
	 */
	public function getGroupGoalLeaderBoardAction(Request $request, Goal $goal, GoalGroup $goalGroup)
	{
		$em = $this->getDoctrine()->getEntityManager();
		/** @var ScoreRepository $repo */
		$repo = $em->getRepository('AppBundle:Score');

		/** @var Score $userScore */
		$userScore = $repo->findOneBy(
			array(
				'user'       => $this->getUser(),
				'goal'       => $goal,
				'group_goal' => $goalGroup
			)
		);

		$userPosition = $repo->getUserGroupGoalPosition(0, $goal, $goalGroup);

		if ($userScore instanceof Score) {
			$userPosition = $repo->getUserGroupGoalPosition($userScore->getScore(), $goal, $goalGroup);
		} else {
			$userScore = new Score();
			$userScore->setUser($this->getUser());
			$userScore->setScore(0);
			$userScore->setGoal($goal);
			$userScore->setGroupGoal(1);
		}

		$goalLeaderBoardResult = $repo->getGroupGoalLeaderBoard($goal, $goalGroup);


		$leaderBoard = array();
		$leaderBoardUsers = array();
		foreach ($goalLeaderBoardResult as $goalLeaderBoard) {

			/** @var Score $score */
			$score = array_shift($goalLeaderBoard);

			$leaderBoard[] = new TotalScore(
				$score,
				(int) $goalLeaderBoard['totalScore']
			);
			$leaderBoardUsers[] = $score->getUser()->getId();
		}

		// add users without scores
		$userGoals = $em->getRepository('AppBundle:UserGoal')->findGroupGoalUsers($goal, $goalGroup);
		foreach ($userGoals as $userGoal) {
			/** @var UserGoal $userGoal */
			if (!in_array($userGoal->getUser()->getId(), $leaderBoardUsers)) {
				$score = new Score();
				$score->setUser($userGoal->getUser());
				$score->setScore(0);
				$score->setGoal($userGoal->getGoal());
				$score->setGroupGoal(1);

				$leaderBoard[] = new TotalScore(
					$score,
					0
				);

				$leaderBoardUsers[] = $userGoal->getUser()->getId(); //Add to array to prevent duplication
			}
		}

		if (count($leaderBoard) === 0) {
			$leaderBoard[] = new TotalScore(
				$userScore,
				(int) $userScore->getScore()
			);
		}

		return $this->createApiResponse(new GlobalLeaderBoard($userScore, $userPosition, $leaderBoard, $this->generateBaseUrl($request)), 'list');
	}

}