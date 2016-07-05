<?php
namespace AsseticBundle;

use AsseticBundle\FilterManager;

use Interop\Container\ContainerInterface;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class FilterManagerFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $locator
     * @param String $requestedName
     * @param Array $options, optional
     * @return \AsseticBundle\FilterManager
     */
    public function __invoke(ContainerInterface $locator, $requestedName, array $options = null)
    {
        $filterManager = new FilterManager($locator);

        return $filterManager;
    }

    /**
     * @param ServiceLocatorInterface $locator
     * @return \AsseticBundle\FilterManager
     */
    public function createService(ServiceLocatorInterface $locator)
    {
        return $this($locator, 'FilterManager');
    }
}
