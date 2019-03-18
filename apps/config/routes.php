<?php
use Phalcon\Mvc\Router\Group as RouterGroup;

$router = new \Phalcon\Mvc\Router();

$router->setDefaultModule("votingfrontend");

/*
 * Voting Frontend.
 */
$router->add("/:params", array(
    'module' => 'votingfrontend',
    'controller' => 'vote',
    'action' => 'index',
    'params' => 1
));

$router->add("/vote/doVote/:params", array(
    'module' => 'votingfrontend',
    'controller' => 'vote',
    'action' => 'doVote',
    'params' => 1
));

$router->add("/vote/signedMsgCheck", array(
    'module' => 'votingfrontend',
    'controller' => 'vote',
    'action' => 'signedMsgCheck'
));

$router->add("/thankyou/:params", array(
    'module' => 'votingfrontend',
    'controller' => 'vote',
    'action' => 'thankyou',
    'params' => 1
));


$router->notFound(array(
    "module" => 'votingfrontend',
    "controller" => "index",
    "action" => "notFound"
));

/*
 * Voting Backend.
 */
$votingbackend = new RouterGroup(
    [
        "module"     => "votingbackend"
    ]
);

$votingbackend->setHostName($config->application->backendModuleHost);

$votingbackend->add("/", array(
    'controller' => 'auth',
    'action' => 'index'
));

$votingbackend->add("/:controller", array(
    'module' => 'votingbackend',
    'controller' => 1
));

$votingbackend->add("/:controller/:action", array(
    'module' => 'votingbackend',
    'controller' => 1,
    'action' => 2
));

$votingbackend->add("/:controller/:action/:params", array(
    'module' => 'votingbackend',
    'controller' => 1,
    'action' => 2,
    'params' => 3
));

$router->mount($votingbackend);

/*
 * Signup
 */
$signup = new RouterGroup(
    [
        "module"     => "signup"
    ]
);

$signup->setHostName($config->application->signupModuleHost);

$signup->add("/", array(
    'module' => 'signup',
    'controller' => 'signup',
    'action' => 'index'
));

$signup->add("/:controller", array(
    'module' => 'signup',
    'controller' => 1
));

$signup->add("/:controller/:action", array(
    'module' => 'signup',
    'controller' => 1,
    'action' => 2
));

$signup->add("/:controller/:action/:params", array(
    'module' => 'signup',
    'controller' => 1,
    'action' => 2,
    'params' => 3
));

$router->mount($signup);

return $router;