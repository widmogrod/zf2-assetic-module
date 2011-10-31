<?php
namespace Assetic;

class Service
{
    const DEFAULT_ROUTE_NAME = 'default';

    protected $routeName;

    /**
     * @var \Assetic\Configuration
     */
    protected $configuration;

    /**
     * @var \Assetic\AssetManager
     */
    protected $assetManager;

    /**
     * @var \Assetic\FilterManager
     */
    protected $filterManager;

    public function __construct(Configuration $configuration)
    {
        $this->configuration = $configuration;
    }

    public function setRoute($namespace)
    {
        $this->namespace = (string) $namespace;
    }

    public function setRouteName($routeName)
    {
        $this->routeName = basename($routeName);
    }

    public function getRouteName()
    {
        return (null === $this->routeName) ? self::DEFAULT_ROUTE_NAME : $this->routeName;
    }

    public function setAssetManager(AssetManager $assetManager)
    {
        $this->assetManager = $assetManager;
    }

    public function getAssetManager()
    {
        return $this->assetManager;
    }

    public function setFilterManager(FilterManager $filterManager)
    {
        $this->filterManager = $filterManager;
    }

    public function getFilterManager()
    {
        return $this->filterManager;
    }
}
