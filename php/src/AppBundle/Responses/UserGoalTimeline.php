<?php

namespace AppBundle\Responses;

use ApiBundle\Responses\AbstractResponse;
use AppBundle\Entity\Milestone;
use AppBundle\Entity\UserGoal;
use AppBundle\Entity\UserGoalImage;
use AppBundle\Service\GoalService;
use JMS\Serializer\Annotation\Type;
use JMS\Serializer\Annotation\Groups;
use Symfony\Component\HttpFoundation\Request;

class UserGoalTimeline extends AbstractResponse
{
	protected $status;

    /**
     * @var integer
     * @Type("integer")
     * @Groups({"list","details"})
     */
    protected $id;

	/**
	 * @var string
	 * @Type("string")
	 * @Groups({"list","details"})
	 */
	protected $supervisor;

	/**
	 * @var bool
	 * @Type("boolean")
	 * @Groups({"list","details"})
	 */
	protected $requiresCheckinImage;

	/**
	 * @var integer
	 * @Type("integer")
	 * @Groups({"list","details"})
	 */
	protected $amount;

	/**
	 * @var string
	 * @Type("string")
	 * @Groups({"list","details"})
	 */
	protected $currency;

	/**
	 * @var integer
	 * @Type("integer")
	 * @Groups({"list","details"})
	 */
	protected $maximumFails;

	/**
	 * @var string
	 * @Type("string")
	 * @Groups({"list","details"})
	 */
	protected $image;

	/**
	 * @var string
	 * @Type("string")
	 * @Groups({"list","details"})
	 */
	protected $source;

	/**
	 * @var array
	 * @Type("array")
	 * @Groups({"list","details"})
	 */
	protected $charities;

	/**
	 * @var GoalSummary
	 * @Type("AppBundle\Responses\GoalSummary")
	 * @Groups({"list","details"})
	 */
	protected $goal;

	/**
	 * @var array
	 * @Type("array")
	 * @Groups({"list","details"})
	 */
	protected $milestones;

    /**
     * GoalSummary constructor.
     * @param UserGoal $goal
     */
    public function __construct(UserGoal $goal, $basePath, $finishedTasks = [], $nextCheckin = NULL)
    {
        parent::__construct(200);
	    /** @var UserGoalImage|null $image */
	    $image = null;
	    if ($goal->getUserGoalImages()) {
		    $image = $goal->getUserGoalImages()->first();
	    }
	    $charities = [];
	    foreach ($goal->getUserGoalCharities() as $charity) {
	    	$charities[] = new UserGoalCharitySummary($charity);
	    }

        $this->id = $goal->getId();
        $this->supervisor = $goal->getSuperVisor();
		$this->requiresCheckinImage = $goal->requiresCheckinImage();
        $this->amount = $goal->getStakeAmount() ? (int) $goal->getStakeAmount() : null;
        $this->currency = $goal->getCurrency();
        $this->maximumFails = (int) $goal->getMaximumFails();
        $this->image = $image ? $basePath . GoalService::GOAL_UPLOAD_DIR . '/' . $image->getImage() : null;
        $this->source = $goal->getPaymentToken();
        $this->charities = $charities;
	    $this->goal = new GoalSummary($goal->getGoal(), $basePath);


	    $this->milestones = array_map(
            function (Milestone $milestone) use ($finishedTasks, $goal, $nextCheckin) {
                return new MilestoneSummary($milestone, null, $finishedTasks, $goal->getStartDate(), $nextCheckin);
            },
	        iterator_to_array($goal->getGoal()->getMilestones())
        );
    }
}