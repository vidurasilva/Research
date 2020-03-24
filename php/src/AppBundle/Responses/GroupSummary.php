<?php

namespace AppBundle\Responses;

use AppBundle\Entity\GoalGroup;
use AppBundle\Entity\UserGoal;
use JMS\Serializer\Annotation\Type;
use JMS\Serializer\Annotation\Groups;
use ApiBundle\Responses\AbstractResponse;
use Symfony\Component\HttpFoundation\Request;
use UserBundle\Responses\UserSummary;

class GroupSummary extends AbstractResponse
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
    protected $name;

    /**
     * @var boolean
     * @Groups({"list","details"})
     * @Type("boolean")
     */
    protected $enrolled;

    /**
     * @var UserGoalSummary
     * @Groups({"list","details"})
     * @Type("AppBundle\Responses\UserGoalSummary")
     */
    protected $userGoal;

    /**
     * @var UserSummary
     * @Groups({"list","details"})
     * @Type("UserBundle\Responses\UserSummary")
     */
    protected $admin;

    /**
     * GroupSummary constructor.
     * @param GoalGroup $goalGroup
     * @param UserGoal $userGoal
     * @param bool $enrolled
     * @param null $basePath
     */
    public function __construct(GoalGroup $goalGroup, UserGoal $userGoal, $enrolled = false, $basePath = NULL)
    {

        parent::__construct(200);

        $this->charities = [];

        $this->id       = $goalGroup->getId();
        $this->name     = $goalGroup->getName();
        $this->enrolled = $enrolled;

        $this->admin    = new UserSummary($goalGroup->getAdmin(), $basePath);
        $this->userGoal = new UserGoalSummary($userGoal, $basePath . 'goals/');
    }
}