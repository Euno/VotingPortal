<?php
namespace EunoVoting\Signup;

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
                'EunoVoting\Signup\Controllers' => '../apps/signup/controllers/',
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
            $dispatcher->setDefaultNamespace("EunoVoting\\Signup\\Controllers");

            return $dispatcher;

        });

        $di->set('view', function () {
            $view = new View();
            $view->setViewsDir('../apps/signup/views/');

            return $view;
        });
    }
}