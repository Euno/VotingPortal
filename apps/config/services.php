<?php

use Phalcon\Mvc\View;
use Phalcon\Mvc\View\Engine\Php as PhpEngine;
use Phalcon\Mvc\Url as UrlResolver;
use Phalcon\Mvc\View\Engine\Volt as VoltEngine;
use Phalcon\Mvc\Model\Metadata\Memory as MetaDataAdapter;
use Phalcon\Session\Adapter\Files as SessionAdapter;
use Phalcon\Flash\Direct as FlashDirect;
use Phalcon\Flash\Session as FlashSession;

use Phalcon\Mvc\Dispatcher;
use Phalcon\Events\Manager as EventsManager;
use Phalcon\Security;


/**
 * Shared configuration service
 */
$di->setShared('config', function () use ($config) {
    return $config;
});

/*$di->set('dispatcher', function () {

    // Create an events manager
    $eventsManager = new EventsManager();

    // Listen for events produced in the dispatcher using the Security plugin
    $eventsManager->attach('dispatch:beforeDispatch', new SecurityPlugin);

    $dispatcher = new Dispatcher();

    // Assign the events manager to the dispatcher
    $dispatcher->setEventsManager($eventsManager);

    return $dispatcher;
});*/

/**
 * The URL component is used to generate all kind of urls in the application
 */
$di->setShared('url', function () use ($config) {
    $config = $this->getConfig();

    $url = new UrlResolver();
    $url->setBaseUri($config->application->baseUri);

    return $url;
});

/**
 * Setting up the view component
 */
$di->setShared('view', function () use ($config) {
    $config = $this->getConfig();

    $view = new View();
    $view->setDI($this);
    $view->setViewsDir($config->application->viewsDir);

    $view->registerEngines([
        '.phtml' => 'Phalcon\Mvc\View\Engine\Php'
    ]);

    return $view;
});

/**
 * Database connection is created based in the parameters defined in the configuration file
 */
$di->setShared('db', function () use ($config) {
    $config = $this->getConfig();

    $class = 'Phalcon\Db\Adapter\Pdo\\' . $config->database->adapter;
    $connection = new $class([
        'host'     => $config->database->host,
        'username' => $config->database->username,
        'password' => $config->database->password,
        'dbname'   => $config->database->dbname,
        'charset'  => $config->database->charset
    ]);

    return $connection;
});


/**
 * If the configuration specify the use of metadata adapter use it or use memory otherwise
 */
$di->setShared('modelsMetadata', function () {
    return new MetaDataAdapter();
});

/**
 * Register the session flash service with the Twitter Bootstrap classes
 */
// Set up the flash service
$di->set(
    "flash",
    function () {
        $flash = new FlashDirect(
            [
                "error"   => "alert alert-danger",
                "success" => "alert alert-success",
                "notice"  => "alert alert-info",
                "warning" => "alert alert-warning",
            ]
        );

        return $flash;
    }
);

// Set up the flash session service
$di->set(
    "flashSession",
    function () {
        return new FlashSession([
            "error"   => "alert alert-danger",
            "success" => "alert alert-success",
            "notice"  => "alert alert-info",
            "warning" => "alert alert-warning",
        ]);
    }
);

/**
 * Start the session the first time some component request the session service
 */
$di->setShared('session', function () {
    $session = new SessionAdapter();
    $session->start();

    return $session;
});

$di->set('assets', function () {
    return new Phalcon\Assets\Manager();
}, true);

$di->set('security', function () {

    $security = new Security();

    // Set the password hashing factor to 12 rounds
    $security->setWorkFactor(12);

    return $security;
}, true);

$di->set('response', function() {

    return new \Phalcon\Http\Response();

});

$di->set('router', function() use ($config) {
    require 'routes.php';
    return $router;
});