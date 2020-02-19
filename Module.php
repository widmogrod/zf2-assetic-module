<?php

namespace AsseticBundle;

use Laminas\EventManager\EventInterface;
use Laminas\ModuleManager\Feature\AutoloaderProviderInterface;
use Laminas\ModuleManager\Feature\BootstrapListenerInterface;
use Laminas\ModuleManager\Feature\ConfigProviderInterface;

class Module implements
        AutoloaderProviderInterface,
        ConfigProviderInterface,
        BootstrapListenerInterface
{
    /**
     * Listen to the bootstrap event
     *
     * @param \Laminas\EventManager\EventInterface $e
     *
     * @return array
     */
    public function onBootstrap(EventInterface $e)
    {
        /** @var $e \Laminas\Mvc\MvcEvent */
        // Only attach the Listener if the request came in through http(s)
        if (PHP_SAPI !== 'cli') {
            $app = $e->getApplication();

            $app->getServiceManager()->get('AsseticBundle\Listener')->attach($app->getEventManager());
        }
    }

    /**
     * Returns configuration to merge with application configuration
     *
     * @return array|\Traversable
     */
    public function getConfig()
    {
        return require __DIR__ . '/configs/module.config.php';
    }

    /**
     * Return an array for passing to Laminas\Loader\AutoloaderFactory.
     *
     * @return array
     */
    public function getAutoloaderConfig()
    {
        return [
            'Laminas\Loader\StandardAutoloader' => [
                'namespaces' => [
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__ . '/'
                ],
            ],
        ];
    }
}
