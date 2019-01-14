<?php
namespace EunoVoting\VotingBackend;

use Phalcon\Loader,
    Phalcon\DiInterface,
    Phalcon\Mvc\Dispatcher,
    Phalcon\Mvc\View,
    Phalcon\Mvc\Url,
    Phalcon\Mvc\ModuleDefinitionInterface;

class Module implements ModuleDefinitionInterface
{
    public function registerAutoloaders(\Phalcon\DiInterface $di = null)
    {
        $loader = new Loader();

        $loader->registerNamespaces(
            array(
                'EunoVoting\VotingBackend\Controllers' => '../apps/votingbackend/controllers/',
                'EunoVoting\VotingBackend\Plugins' => '../apps/votingbackend/plugins/',
                'EunoVoting\Common\Models' => '../apps/common/models/',
                'EunoVoting\Common\Libraries' => '../apps/common/libraries/',
            )
        );
        $loader->register();
    }

    public function registerServices(DiInterface $di)
    {
        $di->set('dispatcher', function () use ($di) {
            $eventsManager = $di->getShared('eventsManager');

            $eventsManager->attach('dispatch:beforeDispatch', new \EunoVoting\VotingBackend\Plugins\SecurityPlugin());
            $dispatcher = new Dispatcher();
            $dispatcher->setEventsManager($eventsManager);
            $dispatcher->setDefaultNamespace("EunoVoting\\VotingBackend\\Controllers");

            return $dispatcher;
        });

        $di->set('view', function () {
            $view = new View();
            $view->setViewsDir('../apps/votingbackend/views/');

            return $view;
        });

        $di->set('url', function () use ($di) {
            $url = new Url();

            $config = $di->getShared('config');

            $url->setBaseUri($config->application->backendBaseUri);

            return $url;
        });
    }
}