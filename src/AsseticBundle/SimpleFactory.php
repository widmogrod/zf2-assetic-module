<?php
namespace AsseticBundle;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class SimpleFactory implements FactoryInterface
{
    /**
     * @param ServiceLocatorInterface $serviceLocator
     * @param null $cName
     * @param $rName
     * @return object
     */
    public function createService(ServiceLocatorInterface $serviceLocator, $cName = null, $rName = nulll)
    {
        return new $rName();
    }
}
