<?php

namespace AppBundle\Responses;

use AppBundle\Entity\Charity;
use AppBundle\Entity\UserGoalCharity;
use JMS\Serializer\Annotation as Serializer;
use JMS\Serializer\Annotation\Type;
use JMS\Serializer\Annotation\Groups;
use ApiBundle\Responses\AbstractResponse;

class UserGoalCharitySummary extends AbstractResponse
{
	protected $status;

    /**
     * @var int
     * @Groups({"list","details"})
     * @Type("integer")
     */
    protected $id;

    /**
     * @var string
     * @Groups({"list","details"})
     * @Type("string")
     */
    protected $title;

	/**
	 * @var integer
	 * @Groups({"list","details"})
	 * @Type("integer")
	 */
	protected $percentage;

	/**
	 * @var array
	 * @Groups({"list","details"})
	 * @Type("AppBundle\Responses\CharityCategorySummary")
	 */
	protected $category;


	/**
	 * GoalSummary constructor.
	 * @param Charity|UserGoalCharity $goalCharity
	 */
    public function __construct(UserGoalCharity $goalCharity)
    {
	    parent::__construct(200);

        $this->id = $goalCharity->getCharity()->getId();
	    $this->percentage = $goalCharity->getPercentage();
        $this->title = $goalCharity->getCharity()->getTitle();
        if($charityCategory = $goalCharity->getCharity()->getCharityCategory()) {
			$this->category = new CharityCategorySummary($charityCategory);
		}
    }
}