<?php
use Phalcon\Mvc\Model\Metadata\Memory as MetaDataAdapter;
use Phalcon\Security;

/**
 * Shared configuration service
 */
$di->setShared('config', function () use ($config) {
    return $config;
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

$di->set('security', function () {

    $security = new Security();

    // Set the password hashing factor to 12 rounds
    $security->setWorkFactor(12);

    return $security;
}, true);