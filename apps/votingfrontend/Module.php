<?php
namespace EunoVoting\VotingFrontend;

use Phalcon\Loader,
    Phalcon\DiInterface,
    Phalcon\Mvc\Dispatcher,
    Phalcon\Mvc\View,
    Phalcon\Mvc\ModuleDefinitionInterface;

class Module implements ModuleDefinitionInterface
{
    public function registerAutoloaders(\Phalcon\DiInterface $di = null)
    {
        $loader = new Loader();

        $loader->registerNamespaces(
            array(
                'EunoVoting\VotingFrontend\Controllers' => '../apps/votingfrontend/controllers/',
                'EunoVoting\Common\Models' => '../apps/common/models/',
                'EunoVoting\Common\Libraries' => '../apps/common/libraries/'
            )
        );
        $loader->register();
    }

    public function registerServices(DiInterface $di)
    {
        $di->set('dispatcher', function () use ($di) {

            $dispatcher = new Dispatcher();
            $dispatcher->setDefaultNamespace("EunoVoting\\VotingFrontend\\Controllers");

            return $dispatcher;

        });

        $di->set('view', function () {
            $view = new View();
            $view->setViewsDir('../apps/votingfrontend/views/');

            return $view;
        });
    }
}