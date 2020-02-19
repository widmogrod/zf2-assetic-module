<?php

namespace AsseticBundle;

use Assetic\Filter\FilterInterface;
use Assetic\FilterManager as AsseticFilterManager;
use Laminas\ServiceManager\ServiceLocatorInterface;

class FilterManager extends AsseticFilterManager
{
    /**
     * @var ServiceLocatorInterface
     */
    protected $serviceLocator;

    /**
     * @param ServiceLocatorInterface $locator
     */
    public function __construct(ServiceLocatorInterface $locator)
    {
        $this->serviceLocator = $locator;
    }

    /**
     * @param $alias
     *
     * @return bool
     */
    public function has($alias)
    {
        return parent::has($alias) ? true : $this->serviceLocator->has($alias);
    }

    /**
     * @param $alias
     *
     * @throws \InvalidArgumentException    When cant retrieve filter from service manager.
     *
     * @return mixed
     */
    public function get($alias)
    {
        if (parent::has($alias)) {
            return parent::get($alias);
        }

        $service = $this->serviceLocator;
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
