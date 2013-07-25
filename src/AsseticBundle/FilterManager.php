<?php

namespace AsseticBundle;

use Assetic\Filter\FilterInterface;
use Assetic\FilterManager as AsseticFilterManager;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class FilterManager extends AsseticFilterManager implements ServiceLocatorAwareInterface{

    /**
     * @var ServiceLocatorInterface
     */
    protected $serviceLocator = null;

    /**
     * Set service locator
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;

        return $this;
    }

    /**
     * Get service locator
     *
     * @return ServiceLocatorInterface
     */
    public function getServiceLocator()
    {
        return $this->serviceLocator;
    }

    /**
     * @param $alias
     * @return bool
     */
    public function has($alias)
    {
        $has = parent::has($alias);
        if (!$has){
            if ($this->getServiceLocator()->has($alias)){
                $filter = $this->getServiceLocator()->get($alias);
                // slightly ignore other services
                if ($filter instanceof FilterInterface){
                    $this->set($alias, $filter);
                    return true;
                }
            }
        }

        return $has;
    }

    /**
     * @param $alias
     * @return mixed
     */
    public function get($alias)
    {
        // just load from service manager if available
        $this->has($alias);
        return parent::get($alias);
    }
} 