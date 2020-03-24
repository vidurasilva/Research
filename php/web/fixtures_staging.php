<?php
/**
 * Created by PhpStorm.
 * User: mdubbelman
 * Date: 9/14/16
 * Time: 9:53 AM
 */
$hostname = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : null;

if (strpos($hostname, 'staging') === false ) {
	die('No access');
}

echo '<pre>';
chdir('..');
$commands = [
	'php app/console doctrine:fixtures:load --no-interaction --env=staging',
];
foreach ($commands as $command) {
	echo 'Executing command "'.$command.'"';
	echo shell_exec($command);
	echo "\r\n";
}