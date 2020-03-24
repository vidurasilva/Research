<?php

namespace AppBundle\Responses;

use ApiBundle\Responses\AbstractResponse;
use AppBundle\Entity\GoalGroup;
use AppBundle\Entity\UserGoal;
use AppBundle\Entity\UserGoalImage;
use JMS\Serializer\Annotation\Type;
use JMS\Serializer\Annotation\Groups;
use Stripe\Customer;
use Symfony\Component\HttpFoundation\Request;

class GroupDetail extends AbstractResponse
{
	/**
	 * @var array
	 * @Type("array")
	 * @Groups({"list","details"})
	 */
	protected $data;

	/**
	 * GoalSummary constructor.
	 * @param UserGoal $userGoal
	 */
	public function __construct(UserGoal $userGoal, $enrolled = false, $basePath = NULL)
	{
		parent::__construct(200);
		$this->data['group'] = new UserGoalSummary($userGoal, $basePath . 'goals/');
	}
}