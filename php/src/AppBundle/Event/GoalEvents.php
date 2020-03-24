<?php
/**
 * Created by PhpStorm.
 * User: ngeleedst
 * Date: 11/10/16
 * Time: 10:49
 */

namespace AppBundle\Event;

final class GoalEvents
{
    const GOAL_SUBSCRIBED = 'app.send_invites.after';
    const GOAL_GROUP_ATTACH_EXISTING_USERS = 'app.attach_existing_users.before';
}