<?php
namespace AsseticBundle;

use AsseticBundle\FilterManager;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class FilterManagerFactory implements FactoryInterface
{
    /**
     * @param ServiceLocatorInterface $locator
     * @return \AsseticBundle\FilterManager
     */
    public function createService(ServiceLocatorInterface $locator)
    {
        $filterManager = new FilterManager($locator);

        return $filterManager;
    }
}
