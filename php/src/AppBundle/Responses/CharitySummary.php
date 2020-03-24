<?php

namespace AppBundle\Responses;

use AppBundle\Entity\Charity;
use JMS\Serializer\Annotation\Type;
use JMS\Serializer\Annotation\Groups;
use ApiBundle\Responses\AbstractResponse;

class CharitySummary extends AbstractResponse
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
     * GoalSummary constructor.
     * @param Charity $goalCharity
     */
    public function __construct(Charity $goalCharity)
    {
	    parent::__construct(200);
        $this->id = $goalCharity->getId();
        $this->title = $goalCharity->getTitle();
    }
}