<?php
namespace EunoVoting\Swap;

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
                'EunoVoting\Swap\Controllers' => '../apps/swap/controllers/',
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
            $dispatcher->setDefaultNamespace("EunoVoting\\Swap\\Controllers");

            return $dispatcher;

        });

        $di->set('view', function () {
            $view = new View();
            $view->setViewsDir('../apps/swap/views/');

            return $view;
        });

        $di->set('url', function () use ($di) {
            $url = new Url();

            $config = $di->getShared('config');

            $url->setBaseUri($config->application->swapBaseUri);

            return $url;
        });
    }
}
