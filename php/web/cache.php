<?php
/**
 * Created by PhpStorm.
 * User: mdubbelman
 * Date: 9/14/16
 * Time: 9:53 AM
 */
$hostname = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : null;

if (strpos($hostname, 'ebox') === false && strpos($hostname, 'staging') === false) {
	die('No access');
}

echo '<pre>';
chdir('..');
$commands = [
	'php app/console cache:clear --env=staging --no-debug',
	'php app/console cache:warmup --env=staging --no-debug',
];
foreach ($commands as $command) {
	echo 'Executing command "'.$command.'"';
	echo shell_exec($command);
	echo "\r\n";
}