<?php

namespace AppBundle\Responses;

use ApiBundle\Responses\AbstractResponse;
use AppBundle\Entity\Charity;
use AppBundle\Entity\GoalGroup;
use AppBundle\Entity\UserGoal;
use AppBundle\Entity\UserGoalImage;
use AppBundle\Service\GoalService;
use JMS\Serializer\Annotation\Type;
use JMS\Serializer\Annotation\Groups;
use AppBundle\Service\UserGoal as UserGoalService;

class UserGoalSummary extends AbstractResponse
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
     * @var boolean
     * @Type("boolean")
     * @Groups({"list","details"})
     */
    protected $globalStake;

    /**
     * @var GoalSummary
     * @Type("AppBundle\Responses\GoalSummary")
     * @Groups({"list","details"})
     */
    protected $goal;

    /**
     * @var integer
     * @Type("integer")
     * @Groups({"list","details"})
     */
    protected $isGroupGoal = 0;

    /**
     * @var boolean
     * @Type("boolean")
     * @Groups({"list","details"})
     */
    protected $finished;

    /**
     * @var string
     * @Type("string")
     * @Groups({"list","details"})
     */
    protected $startDate;

    /**
     * @var string
     * @Type("string")
     * @Groups({"list","details"})
     */
    protected $endDate;

    /**
     * GoalSummary constructor.
     * @param UserGoal $goal
     */
    public function __construct(UserGoal $goal, $basePath, UserGoalService $userGoalService = null)
    {
        parent::__construct(200);
		/** @var UserGoalImage|null $image */
		$image = NULL;
		if ($goal->getUserGoalImages()) {
			$image = $goal->getUserGoalImages()->first();
		}
		$charities = [];
		foreach ($goal->getUserGoalCharities() as $charity) {

			if (!empty($charity)) {
				$charities[] = new UserGoalCharitySummary($charity);
			}
		}

		if ($image !== NULL) {
			$basePath = str_replace('/data/uploads/goals', '/data/uploads', $basePath);
		}


		$this->id           		= $goal->getId();
        $this->supervisor   		= $goal->getSuperVisor();
		$this->requiresCheckinImage = $goal->requiresCheckinImage();
        $this->amount       		= $goal->getStakeAmount() ? (int)$goal->getStakeAmount() : NULL;
        if($userGoalService) {
			$this->amount = $userGoalService->getTotalAmountForUserGoal($goal);
		}
        $this->currency     		= $goal->getCurrency();
        $this->maximumFails 		= (int)$goal->getMaximumFails();
        $this->image        		= $image ? $basePath . GoalService::GOAL_UPLOAD_DIR . '/' . $image->getImage() : NULL;
        $this->source       		= $goal->getPaymentToken();
        $this->charities    		= $charities;
        $this->globalStake  		= $goal->getGlobalStake();
        $this->finished     		= $goal->getFinished();
        $this->goal         		= new GoalSummary($goal->getGoal(), $basePath);

        if ($goal->getStartDate() instanceof \DateTime) {
            $this->startDate = $goal->getStartDate()->format('Y-m-d');
        }

        if ($goal->getEndDate() instanceof \DateTime) {
            $this->endDate = $goal->getEndDate()->format('Y-m-d');
        }

        if ($goal->getGroup() instanceof GoalGroup) {
            $this->isGroupGoal = $goal->getGroup()->getId();
        }
    }
}