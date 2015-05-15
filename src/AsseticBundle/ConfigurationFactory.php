<?php
namespace AsseticBundle;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class ConfigurationFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return Configuration
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new Configuration(
            $serviceLocator->get('Configuration')['assetic_configuration']
        );
    }
}