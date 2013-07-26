<?php
namespace AsseticBundle;

use Assetic\Filter\FilterInterface;
use Assetic\FilterManager as AsseticFilterManager;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class FilterManager extends AsseticFilterManager implements ServiceLocatorAwareInterface
{
    /**
     * @var ServiceLocatorInterface
     */
    protected $serviceLocator;

    /**
     * Set service locator
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
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
        return parent::has($alias) ? true : $this->getServiceLocator()->has($alias);
    }

    /**
     * @param $alias
     * @throws \InvalidArgumentException    When cant retrieve filter from service manager.
     * @return mixed
     */
    public function get($alias)
    {
        if (parent::has($alias)) {
            return parent::get($alias);
        }

        $service = $this->getServiceLocator();
        if (!$service->has($alias)) {
            throw new \InvalidArgumentException(sprintf('There is no "%s" filter in ZF2 service manager.', $alias));
        }

        $filter = $service->get($alias);
        if (!($filter instanceof FilterInterface)) {
            $givenType = is_object($filter) ? get_class($filter) : gettype($filter);
            $message = 'Retrieved filter "%s" is not instanceof "Assetic\Filter\FilterInterface", but type was given %s';
            $message = sprintf($message, $alias, $givenType);
            throw new \InvalidArgumentException($message);
        }

        $this->set($alias, $filter);

        return $filter;
    }
} 