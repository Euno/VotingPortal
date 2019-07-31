<?php

use \Phalcon\DI\FactoryDefault\CLI as CliDI,
    \Phalcon\CLI\Console as ConsoleApp;

define('VERSION', '1.0.0');

date_default_timezone_set('Europe/Amsterdam');

// Using the CLI factory default services container
$di = new CliDI();

// Define path to application directory
defined('APPLICATION_PATH')
|| define('APPLICATION_PATH', realpath(dirname(__FILE__)));

define('PROJECT_PATH', __DIR__ . '/../');

require __DIR__ . "/../vendor/autoload.php";

/**
 * Register the autoloader and tell it to register the tasks directory
 */
$loader = new \Phalcon\Loader();
$loader->registerDirs(
    array(
        APPLICATION_PATH . '/common/models',
        APPLICATION_PATH . '/common/libraries',
        APPLICATION_PATH . '/common/tasks'
    )
);
$loader->register();

$loader->registerNamespaces([
    'EunoVoting\Common\Models' => APPLICATION_PATH.'/common/models/',
    'EunoVoting\Common\Libraries' => APPLICATION_PATH.'/common/libraries/',
])->register();

$application_env = getenv ( 'APPLICATION_ENV' );

switch($application_env){
    case "production" :
        $config = include APPLICATION_PATH . "/config/config.cli.production.php";
        //error_reporting(0);
        break;
    case "development" :
        $config = include APPLICATION_PATH . "/config/config.cli.development.php";
        //error_reporting(E_ALL);
        break;
}

$di->set('config', $config);

include APPLICATION_PATH .  "/config/cli_services.php";

require __DIR__.'/../vendor/autoload.php';

// Create a console application
$console = new ConsoleApp();
$console->setDI($di);

/**
 * Process the console arguments
 */
$arguments = array();
foreach ($argv as $k => $arg) {
    if ($k == 1) {
        $arguments['task'] = $arg;
    } elseif ($k == 2) {
        $arguments['action'] = $arg;
    } elseif ($k >= 3) {
        $arguments['params'][] = $arg;
    }
}

// Define global constants for the current task and action
define('CURRENT_TASK',   (isset($argv[1]) ? $argv[1] : null));
define('CURRENT_ACTION', (isset($argv[2]) ? $argv[2] : null));

try {
    // Handle incoming arguments
    $console->handle($arguments);
} catch (\Phalcon\Exception $e) {
    echo $e->getMessage();
    exit(255);
}