<?php

namespace AsseticBundle;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class FilterManagerFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $locator
     * @param string $requestedName
     * @param array $options, optional
     *
     * @return \AsseticBundle\FilterManager
     */
    public function __invoke(ContainerInterface $locator, $requestedName, array $options = null)
    {
        $filterManager = new FilterManager($locator);

        return $filterManager;
    }

    /**
     * @param ServiceLocatorInterface $locator
     *
     * @return \AsseticBundle\FilterManager
     */
    public function createService(ServiceLocatorInterface $locator)
    {
        return $this($locator, 'FilterManager');
    }
}
