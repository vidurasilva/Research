<?php
/**
 * Created by PhpStorm.
 * User: ngeleedst
 * Date: 11/10/16
 * Time: 10:43
 */

namespace AppBundle\Event;

use AppBundle\Entity\UserGoal;
use Symfony\Component\EventDispatcher\Event;

class GoalEvent extends Event
{
    /**
     * @var UserGoal
     */
    protected $userGoal;

    /**
     * GoalEvent constructor.
     * @param UserGoal $userGoal
     */
    public function __construct(UserGoal $userGoal)
    {
        $this->userGoal = $userGoal;
    }

    /**
     * @return UserGoal
     */
    public function getUserGoal()
    {
        return $this->userGoal;
    }
}