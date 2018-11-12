<?php

use Phalcon\DI\FactoryDefault;
use Phalcon\Mvc\View;
use Phalcon\Mvc\Dispatcher;
use Phalcon\Mvc\Url as UrlResolver;
use Phalcon\Db\Adapter\Pdo\Mysql as DbAdapter;
use Phalcon\Mvc\View\Engine\Volt as VoltEngine;
use Phalcon\Mvc\Model\Metadata\Memory as MetaDataAdapter;
use Phalcon\Session\Adapter\Files as SessionAdapter;
use Phalcon\DI;
use Phalcon\Mvc\Model\Manager as ModelsManager;
use Phalcon\Events\Manager as EventsManager;

/**
 * The FactoryDefault Dependency Injector automatically register the right services providing a full stack framework
 */

/**
 * The URL component is used to generate all kind of urls in the application
 */
$di->set('url', function () use ($config) {
    $url = new UrlResolver();
    $url->setBaseUri($config->application->baseUri);

    return $url;
}, true);


/**
 * Database connection is created based in the parameters defined in the configuration file
 */
$di->set('db', function () use ($config) {
    return new DbAdapter(array(
        'host' => $config->database->host,
        'username' => $config->database->username,
        'password' => $config->database->password,
        'dbname' => $config->database->dbname,
        "charset" => $config->database->charset
    ));
});


$di->set('db_cms', function () use ($config) {
    return new DbAdapter(array(
        'host' => $config->database_cms->host,
        'username' => $config->database_cms->username,
        'password' => $config->database_cms->password,
        'dbname' => $config->database_cms->dbname,
        "charset" => $config->database_cms->charset
    ));
});

/**
 * If the configuration specify the use of metadata adapter use it or use memory otherwise
 */
$di->set('modelsMetadata', function () {
    return new MetaDataAdapter();
});


$di->set(
    "elasticsearch",
    function ()  use ($config) {

        $elasticsearch = new \Elasticsearch\Client(
            [
                "hosts" => [
                    $config->elasticsearch->host . ":" . $config->elasticsearch->port
                ],
            ]
        );

        return $elasticsearch;
    },
    true
);

$di->set(
    "elasticsearchIndexer",
    function ()  use ($config) {

        $elasticsearchIndexer = new \ElasticsearchIndexer\Indexer(
            $config->elasticsearch->index
        );

        return $elasticsearchIndexer;
    },
    true
);

$di->set('router', function() {
    $router = new \Phalcon\CLI\Router();
    $router->handle();
    return $router;
});

$di->set('crypt', function() {
    $crypt = new \Phalcon\Crypt();
	$crypt->setKey('%92.74$e86e$f!9j');
    
	return $crypt;
}, true);