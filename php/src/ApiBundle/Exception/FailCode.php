<?php

namespace ApiBundle\Exception;

interface FailCode
{
    // General fails (100-199)
    const GENERAL_MISSING_PARAMETERS       = 100;
    const GENERAL_INVALID_DATE             = 101;
    const GENERAL_INVALID_EMAIL            = 102;
    const GENERAL_INVALID_PASSWORD         = 103;
    const GENERAL_DAILY_LIMIT              = 104;
    const GENERAL_GROUP_USER_NOT_EXIST     = 105;
    const GENERAL_USER_GOAL_NOT_EXIST      = 106;
    const GENERAL_GOAL_START_DATE_EXCEEDED = 107;
    const GENERAL_USER_GOAL_EXIST          = 108;


    const DOMAIN_STATE_INVALID = 200;

    // General fails (600-699)
    const AUTHORIZATION_UNKNOWN_USER          = 600;
    const AUTHORIZATION_EXISTS_USER           = 601;
    const AUTHORIZATION_FAIL_FACEBOOK         = 602;
    const AUTHORIZATION_RECOVERYTOKEN_EXPIRED = 603;
    const AUTHORIZATION_FAIL_GOOGLE           = 604;
    const AUTHORIZATION_USER_NOT_LOGGED_IN    = 605;

    // General fails (700-799)
    const APP_UNKNOWN_VIDEO   = 700;
    const APP_UNKNOWN_TEAM    = 701;
    const APP_UNKNOWN_DEVICE  = 702;
    const APP_UNKNOWN_SEGMENT = 703;
    const APP_UNKNOWN_MATCH   = 704;
    const APP_UNKNOWN_EVENT   = 705;
    const APP_USER_MISMATCH   = 706;
    const APP_UNKNOWN_CAMERA  = 707;

    const PAYMENT_GENERAL_STRIPE_ERROR = 800;


    // Other fails
    const GENERAL_UNKNOWN_ERROR = 999;
}
