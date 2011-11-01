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
        if (null === $this->assetManager)
        {
            $this->assetManager = new AssetManager();
        }
        return $this->assetManager;
    }

    public function setFilterManager(FilterManager $filterManager)
    {
        $this->filterManager = $filterManager;
    }

    public function getFilterManager()
    {
        if (null === $this->filterManager)
        {
            $this->filterManager = new FilterManager();
        }
        return $this->filterManager;
    }

    public function initLoadedModules(array $loadedModules)
    {
        $moduleConfiguration = $this->configuration->getModules();
        foreach($loadedModules as $moduleName => $module)
        {
            $moduleName = strtolower($moduleName);;
            if (!isset($moduleConfiguration[$moduleName])) {
                continue;
            }

            $conf = (array) $moduleConfiguration[$moduleName];

            $factory = new Factory\AssetFactory($conf[g'root_path']);
            $factory->setAssetManager($this->getAssetManager());
            $factory->setFilterManager($this->getFilterManager());
            $factory->setDebug($this->configuration->isDebug());

            $collections = (array) $conf['collections'];
            foreach($collections as $name => $options)
            {
                $assets  = isset($options['assets']) ? $options['assets'] : array();
                $filters = isset($options['filters']) ? $options['filters'] : array();
                $options = isset($options['options']) ? $options['options'] : array();
                $options['output'] = isset($options['output']) ? $options['output'] : $name;
                $asset = $factory->createAsset($assets, $filters, $options);
                $this->assetManager->set($name, $asset);
            }

            $writer = new AssetWriter($this->configuration->getWebPath());
            $writer->writeManagerAssets($this->assetManager);
        }
    }

    public function generateTags()
    {
        $tags = new TagGenerator($this->configuration->getBaseUrl());
        return $tags->generateTagFromAssetsManager($this->getAssetManager());
    }
}
