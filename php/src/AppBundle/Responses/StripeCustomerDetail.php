<?php

namespace AppBundle\Responses;

use ApiBundle\Responses\AbstractResponse;
use AppBundle\Entity\UserGoal;
use AppBundle\Entity\UserGoalImage;
use JMS\Serializer\Annotation\Type;
use JMS\Serializer\Annotation\Groups;
use Stripe\Customer;
use Symfony\Component\HttpFoundation\Request;

class StripeCustomerDetail extends AbstractResponse
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
	public function __construct(Customer $customer)
	{
		parent::__construct(200);
		$this->data['customer'] = $customer->getLastResponse()->json;
	}
}