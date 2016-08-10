<?php
namespace AsseticBundle;

use Zend\EventManager\EventInterface;
use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\ModuleManager\Feature\BootstrapListenerInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\ModuleManager\Feature\ServiceProviderInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class Module implements
        AutoloaderProviderInterface,
        ConfigProviderInterface,
        BootstrapListenerInterface,
        ServiceProviderInterface
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
        // Only attach the Listener if the request came in through http(s)
        if (PHP_SAPI == 'cli') {
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
}
