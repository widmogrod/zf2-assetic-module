<?php
namespace AsseticBundle\Initializer;

use AsseticBundle\AsseticBundleServiceAwareInterface;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\InitializerInterface;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class AsseticBundleInitializer implements InitializerInterface
{
    /**
     * invoke
     *
     * @param ContainerInterface $container
     * @param $instance
     */
    public function __invoke(ContainerInterface $container, $instance)
    {
        if ($instance instanceof AsseticBundleServiceAwareInterface) {
            if ($container instanceof ServiceLocatorAwareInterface) {
                $container = $container->getServiceLocator();
            }
            $instance->setAsseticBundleService($container->get('AsseticService'));
        }
    }

    /**
     * Initialize
     *
     * @param $instance
     * @param ServiceLocatorInterface $serviceLocator
     */
    public function initialize($instance, ServiceLocatorInterface $serviceLocator)
    {
        $this($serviceLocator, $instance);
    }
}
