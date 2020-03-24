<?php

define('ENV_DEVELOPMENT', 'dev');
define('ENV_TEST', 'test');
define('ENV_STAGING', 'staging');
define('ENV_PRODUCTION', 'prod');
define('ENV_LOCAL', 'local');

if (!defined('ENVIRONMENT')) {
    if (($environment = getenv('ENVIRONMENT')) == false || getenv('ENVIRONMENT') == ENV_PRODUCTION) {
        if (php_sapi_name() == 'cli') {
            if (strpos(__DIR__, 'C:') !== false) {
                $environment = ENV_DEVELOPMENT;
            } elseif (strpos(__DIR__, '/Users/') !== false) {
                $environment = ENV_DEVELOPMENT;
            } else {
                $environment = ENV_PRODUCTION;
            }
        } elseif (!empty($_SERVER['ESITES_SERVERTYPE'])) {
            switch (@$_SERVER['ESITES_SERVERTYPE']) {

                case'local':
                    $environment = ENV_LOCAL;
                    break;

                case'dev':
                    $environment = ENV_DEVELOPMENT;
                    break;

                case 'staging':
                    $environment = ENV_STAGING;
                    break;

                default:
                    $environment = ENV_PRODUCTION;
                    break;

            }
        } else {
            $environment = ENV_PRODUCTION;
        }
    }

    define('ENVIRONMENT', $environment);
}