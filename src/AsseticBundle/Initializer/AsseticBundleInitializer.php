<?php
namespace AsseticBundle\Initializer;

use AsseticBundle\AsseticBundleServiceAwareInterface;
use Zend\ServiceManager\InitializerInterface;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class AsseticBundleInitializer implements InitializerInterface
{
    /**
     * Initialize
     *
     * @param $instance
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function initialize($instance, ServiceLocatorInterface $serviceLocator)
    {
        if ($instance instanceof AsseticBundleServiceAwareInterface) {
            if ($serviceLocator instanceof ServiceLocatorAwareInterface) {
                $serviceLocator = $serviceLocator->getServiceLocator();
            }
            $instance->setAsseticBundleService($serviceLocator->get('AsseticService'));
        }
    }
}