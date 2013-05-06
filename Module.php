<?php
namespace AsseticBundle;

use Zend\Console\Adapter\AdapterInterface;
use Zend\Console\Console;
use Zend\ModuleManager\Feature\ConsoleUsageProviderInterface;
use Zend\Http\Response;
use Zend\EventManager\EventInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\ModuleManager\Feature\ServiceProviderInterface;
use Zend\ModuleManager\Feature\BootstrapListenerInterface;
use Zend\Mvc\MvcEvent;

class Module implements
        AutoloaderProviderInterface,
        ConfigProviderInterface,
        BootstrapListenerInterface,
        ServiceProviderInterface,
        ConsoleUsageProviderInterface
{
    /**
     * Listen to the bootstrap event
     *
     * @param \Zend\EventManager\EventInterface $e
     * @return array
     */
    public function onBootstrap(EventInterface $e)
    {
        /** @var $e \Zend\Mvc\MvcEvent */
        $app = $e->getApplication();
        $em = $app->getEventManager();
        $sm = $app->getServiceManager();

        // Listener have only sense when request is via http.
        if (!Console::isConsole()) {
            $em->attach($sm->get('AsseticBundle\Listener'));
        }
    }

    /**
     * Returns configuration to merge with application configuration
     *
     * @return array|\Traversable
     */
    public function getConfig()
    {
        return array_merge(
            include __DIR__ . '/configs/module.config.php',
            include __DIR__ . '/configs/routes.config.php'
        );
    }

    /**
     * Expected to return \Zend\ServiceManager\Config object or array to
     * seed such an object.
     *
     * @return array|\Zend\ServiceManager\Config
     */
    public function getServiceConfig()
    {
        return array(
            'factories' => array(
                'AsseticBundle\Configuration' => function (ServiceLocatorInterface $serviceLocator) {
                    $configuration = $serviceLocator->get('Configuration');
                    return new Configuration($configuration['assetic_configuration']);
                }
            ),
        );
    }

    /**
     * Return an array for passing to Zend\Loader\AutoloaderFactory.
     *
     * @return array
     */
    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__ . '/'
                ),
            ),
        );
    }

    /**
     * Returns an array or a string containing usage information for this module's Console commands.
     * The method is called with active Zend\Console\Adapter\AdapterInterface that can be used to directly access
     * Console and send output.
     *
     * If the result is a string it will be shown directly in the console window.
     * If the result is an array, its contents will be formatted to console window width. The array must
     * have the following format:
     *
     *     return array(
     *                'Usage information line that should be shown as-is',
     *                'Another line of usage info',
     *
     *                '--parameter'        =>   'A short description of that parameter',
     *                '-another-parameter' =>   'A short description of another parameter',
     *                ...
     *            )
     *
     * @param AdapterInterface $console
     * @return array|string|null
     */
    public function getConsoleUsage(AdapterInterface $console)
    {
        return array(
            'assetic setup' => 'Create cache and assets directory with valid permissions.',
            'assetic build' => 'Build all assets',
        );
    }
}