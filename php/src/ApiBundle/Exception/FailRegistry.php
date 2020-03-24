<?php

namespace ApiBundle\Exception;

use AppBundle\Entity\UserGoal;
use UserBundle\Entity\User;

class FailRegistry implements FailCode
{
    /**
     * @var array
     */
    protected static $registry = [
        // General fails (100-199)
        self::GENERAL_MISSING_PARAMETERS       => 'Missing parameter(s): %s',
        self::GENERAL_INVALID_DATE             => 'String "%s" is not a valid date',
        self::GENERAL_INVALID_EMAIL            => 'String "%s" is not a e-mail',
        self::GENERAL_INVALID_PASSWORD         => 'The minimum password length is 6 characters.',
        self::GENERAL_DAILY_LIMIT              => 'User is only able to check-in 1 task for each goal a day.',
        self::GENERAL_GOAL_START_DATE_EXCEEDED => 'The goal start date is exceeded.',
        self::GENERAL_USER_GOAL_EXIST          => 'The user/group goal already exist.',

        self::DOMAIN_STATE_INVALID                => '%s',

        // General fails (600-699)
        self::AUTHORIZATION_EXISTS_USER           => 'User already exists',
        self::AUTHORIZATION_UNKNOWN_USER          => 'User not found',
        self::AUTHORIZATION_FAIL_FACEBOOK         => 'Facebook call failed with message: %s',
        self::AUTHORIZATION_FAIL_GOOGLE           => 'Google call failed with message: %s',
        self::AUTHORIZATION_RECOVERYTOKEN_EXPIRED => 'The recovery token has expired',
        self::AUTHORIZATION_USER_NOT_LOGGED_IN    => 'User must be logged in',

        // General fails (700-799)
        self::APP_UNKNOWN_VIDEO                   => 'Video with ID "%s" could not been found',
        self::APP_UNKNOWN_TEAM                    => 'Team with ID "%s" could not been found',
        self::APP_UNKNOWN_DEVICE                  => 'Unknown device "%s"',
        self::APP_UNKNOWN_SEGMENT                 => 'Unknown segment "%s"',
        self::APP_UNKNOWN_MATCH                   => 'Match with ID "%s" could not been found',
        self::APP_UNKNOWN_EVENT                   => 'No event with name "%s" registered',
        self::APP_UNKNOWN_CAMERA                  => 'No camera with id "%s" found',
        self::APP_USER_MISMATCH                   => 'The user id does not match with the current user',

        // payment fails (800-)
        self::PAYMENT_GENERAL_STRIPE_ERROR        => 'Stripe API error',


        // Other fails (999)
        self::GENERAL_UNKNOWN_ERROR               => 'Unknown error occurred'
    ];

    /**
     * @param integer $code
     * @throws \Exception
     */
    public static function getByCode($code)
    {
        if (!isset(self::$registry[$code])) {
            throw new \Exception('Exception ' . $code . ' is not registered yet');
        }

        return self::$registry[$code];
    }
}
