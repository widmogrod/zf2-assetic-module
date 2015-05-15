<?php
namespace AsseticBundle;

use Zend\Console;
use Zend\EventManager\EventInterface;
use Zend\Http\Response;
use Zend\ModuleManager\Feature;

class Module implements
    Feature\ConfigProviderInterface,
    Feature\BootstrapListenerInterface,
    Feature\ConsoleUsageProviderInterface
{
    public function onBootstrap(EventInterface $e)
    {
        /** @var $e \Zend\Mvc\MvcEvent */
        $app = $e->getApplication();
        $em = $app->getEventManager();
        $sm = $app->getServiceManager();

        // Listener have only sense when request is via http.
        if (!Console\Console::isConsole()) {
            $em->attach($sm->get('AsseticBundle\Listener'));
        }
    }

    public function getConfig()
    {
        return array_merge(
            include __DIR__ . '/configs/module.config.php',
            include __DIR__ . '/configs/routes.config.php'
        );
    }

    public function getConsoleUsage(Console\Adapter\AdapterInterface $console)
    {
        return array(
            'assetic setup' => 'Create cache and assets directory with valid permissions.',
            'assetic build' => 'Build all assets',
        );
    }
}