<?php
require '../vendor/autoload.php';
$logLevel = 'error';

if (isset($_GET['logLevel'])) {
	$logLevel = $_GET['logLevel'];
}

$validLogLevels = Monolog\Logger::getLevels();

if (!array_key_exists(strtoupper($logLevel), $validLogLevels)) {
	$logLevel = array_keys($validLogLevels)[0];
}

chdir('..');
echo '<pre>';
echo shell_exec('php app/console log:view staging.log --level=' . strtolower($logLevel));