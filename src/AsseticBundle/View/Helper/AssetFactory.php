<?php
namespace AsseticBundle\View\Helper;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class AssetFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return Asset
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new Asset($serviceLocator->get('AsseticService'));
    }
}