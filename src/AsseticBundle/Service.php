<?php
namespace AsseticBundle;

use Assetic\AssetManager,
    Assetic\FilterManager,
    Assetic\Factory,
    Assetic\AssetWriter,
    Assetic\Asset\AssetInterface,
    Assetic\Asset\AssetCache,
    Assetic\Cache\FilesystemCache,
    Zend\View\Renderer\RendererInterface as Renderer,
    AsseticBundle\View\StrategyInterface;

class Service
{
    const DEFAULT_ROUTE_NAME = 'default';

    /**
     * @var string
     */
    protected $routeName;

    /**
     * @var string
     */
    protected $controllerName;

    /**
     * @var string
     */
    protected $actionName;

    /**
     * @var \AsseticBundle\Configuration
     */
    protected $configuration;

    /**
     * @var array of \AsseticBundle\View\StrategyInterface
     */
    protected $strategy = array();

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

    public function setRouteName($routeName)
    {
        $this->routeName = basename($routeName);
    }

    public function getRouteName()
    {
        return (null === $this->routeName) ? self::DEFAULT_ROUTE_NAME : $this->routeName;
    }

    /**
     * @Zend\Di\Definition\Annotation\Inject
     * @Required
     */
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

    /**
     * @param string $controllerName
     */
    public function setControllerName($controllerName)
    {
        $this->controllerName = $controllerName;
    }

    /**
     * @return string
     */
    public function getControllerName()
    {
        return $this->controllerName;
    }

    /**
     * @param string $actionName
     */
    public function setActionName($actionName)
    {
        $this->actionName = $actionName;
    }

    /**
     * @return string
     */
    public function getActionName()
    {
        return $this->actionName;
    }

    public function initLoadedModules(array $loadedModules)
    {
        $moduleConfiguration = $this->configuration->getModules();
        foreach($loadedModules as $moduleName => $module)
        {
            $moduleName = strtolower($moduleName);
            if (!isset($moduleConfiguration[$moduleName])) {
                continue;
            }

            $conf = (array) $moduleConfiguration[$moduleName];

            $factory = new Factory\AssetFactory($conf['root_path']);
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

                $filters = $this->initFilters($filters);

                /** @var $asset \Assetic\Asset\AssetCollection */
                $asset = $factory->createAsset($assets, $filters, $options);

                # allow to move all files 1:1 to new directory
                # its particulary usefull when this assets are images.
                if (isset($options['move_raw']) && $options['move_raw'])
                {
                    foreach($asset as $key => $value)
                    {
                        $name = md5($value->getSourceRoot().$value->getSourcePath());
                        $value->setTargetPath($value->getSourcePath());
                        $value = $this->cache($value);
                        $this->assetManager->set($name, $value);
                    }
                } else {
                    $asset = $this->cache($asset);
                    $this->assetManager->set($name, $asset);
                }
            }

            $writer = new AssetWriter($this->configuration->getWebPath());
            $writer->writeManagerAssets($this->assetManager);
        }
    }

    private function cache(AssetInterface $asset)
    {
        return $this->configuration->getCacheEnabled()
            ? new AssetCache($asset, new FilesystemCache($this->configuration->getCachePath()))
            : $asset;
    }

    private function initFilters(array $filters)
    {
        $result = array();

        $fm = $this->getFilterManager();

        foreach($filters as $alias => $options)
        {
            $option = null;
            if (is_array($options))
            {
                if (!isset($options['name'])) {
                    throw new \InvalidArgumentException(
                        'Filter "'.$alias.'" required option "name"'
                    );
                }

                $name = $options['name'];
                $option = isset($options['option']) ?$options['option']: null;
            } elseif (is_string($options)) {
                $name = $options;
                unset($options);
            }

            if (is_numeric($alias)) {
                $alias = $name;
            }

            if ($fm->has($alias)) {
                continue;
            }

            $filter = new $name($option);
            if(is_array($option)) {
                call_user_func_array(array($filter, '__construct'), $option); 
            }

            $fm->set($alias, $filter);

            $result[] = $alias;
        }

        return $result;
    }

    public function setupRenderer(Renderer $renderer)
    {
        #  generate from controller
        $result = $this->setupRendererForController($renderer);

        # if can't, ten from router
        if (!$result) {
            $result = $this->setupRendererForRouter($renderer);
        }

        return $result;
    }

    public function setupRendererForRouter(Renderer $renderer)
    {
        $assetOptions = $this->configuration->getRoute($this->getRouteName());
        if (!$assetOptions) {
            return false;
        }

        $this->setupRendererFromOptions($renderer, $assetOptions);
        return true;
    }

    public function setupRendererForController(Renderer $renderer)
    {
        $assetOptions = $this->configuration->getController($this->getControllerName());
        if (!$assetOptions) {
            return false;
        }

        $this->setupRendererFromOptions($renderer, $assetOptions);
        return true;
    }

    public function setupRendererFromOptions(Renderer $renderer, array $options)
    {
        if (!$this->hasStrategyForRenderer($renderer)) {
            throw new \Exception(sprintf(
                'no strategy defined for renderer "%s"',
                $this->getRendererName($renderer)
            ));
        }

        /** @var $strategy \AsseticBundle\View\StrategyInterface */
        $strategy = $this->getStrategyForRenderer($renderer);
        while($assetAlias = array_shift($options))
        {
            $assetAlias = ltrim($assetAlias, '@');

            /** @var $asset \Assetic\Asset\AssetInterface */
            $asset = $this->assetManager->get($assetAlias);
            $strategy->setupAsset($asset);
        }
    }

    /**
     * @param \Zend\View\Renderer\RendererInterface $renderer
     */
    public function hasStrategyForRenderer(Renderer $renderer)
    {
        $rendererName = $this->getRendererName($renderer);
        return !!$this->configuration->getStrategyNameForRenderer($rendererName);
    }

    /**
     * @param \Zend\View\Renderer\RendererInterface $renderer
     * @return \AsseticBundle\View\StrategyInterface|null
     */
    public function getStrategyForRenderer(Renderer $renderer)
    {
        if (!$this->hasStrategyForRenderer($renderer)) {
            return null;
        }

        $rendererName = $this->getRendererName($renderer);
        if (!isset($this->strategy[$rendererName]))
        {
            $strategyClass = $this->configuration->getStrategyNameForRenderer($rendererName);
            if (!class_exists($strategyClass, true)) {
                throw new \AsseticBundle\Exception\InvalidArgumentException(sprintf(
                    'strategy class "%s" dosen\'t exists',
                    $strategyClass
                ));
            }

            $instance = new $strategyClass();

            if (!($instance instanceof StrategyInterface)) {
                throw new \AsseticBundle\Exception\DomainException(sprintf(
                     'strategy class "%s" is not instanceof "AsseticBundle\View\StrategyInterface"',
                     $strategyClass
                ));
            }

            $this->strategy[$rendererName] = $instance;
        }

        /** @var $strategy \AsseticBundle\View\StrategyInterface */
        $strategy = $this->strategy[$rendererName];
        $strategy->setBaseUrl($this->configuration->getBaseUrl());
        $strategy->setRenderer($renderer);
        return $strategy;
    }

    protected function getRendererName(Renderer $renderer)
    {
        return get_class($renderer);
    }
}