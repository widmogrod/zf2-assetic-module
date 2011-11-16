<?php
namespace AsseticBundle;

use Assetic\AssetManager,
    Assetic\FilterManager,
    Assetic\Factory,
    Assetic\AssetWriter,
    Assetic\Asset\AssetInterface,
    Assetic\Asset\AssetCache,
    Assetic\Cache\FilesystemCache;

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

    public function initLoadedModules(array $loadedModules)
    {
        /*
         * TODO: Add cache mechanism using file modification date.
         */
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

//            FilesystemCache

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
                $option = isset($options['option']) ?: null;
            } elseif (is_string($options)) {
                $name = $options;
            }

            if (is_numeric($alias)) {
                $alias = $name;
            }

            if ($fm->has($alias)) {
                continue;
            }

            $filter = new $name($option);

            $fm->set($alias, $filter);

            $result[] = $alias;
        }

        return $result;
    }

    public function setupViewHelpers(\Zend\View\PhpRenderer $view)
    {
        #  generate from controller
        $result = $this->setupViewHelperForController($view);

        # if can't, ten from router
        if (!$result) {
            $result = $this->setupViewHelpersForRouter($view);
        }

        return $result;
    }

    public function setupViewHelpersForRouter(\Zend\View\PhpRenderer $view)
    {
        $assetOptions = $this->configuration->getController($this->getControllerName());
        if (!$assetOptions) {
            return false;
        }

        $viewSetup = new ViewHelperSetup(
            $this->configuration->getBaseUrl(),
            $view,
            $this->getAssetManager()
        );

        $viewSetup->setupFromOptions($assetOptions);

        return true;
    }

    public function setupViewHelperForController(\Zend\View\PhpRenderer $view)
    {
        $assetOptions = $this->configuration->getController($this->getControllerName());
        if (!$assetOptions) {
            return false;
        }

        $viewSetup = new ViewHelperSetup(
            $this->configuration->getBaseUrl(),
            $view,
            $this->getAssetManager()
        );

        $viewSetup->setupFromOptions($assetOptions);

        return true;
    }

    public function setupResponseContent($content)
    {
        $tags = $this->generateTags();

        if (isset($tags['css'])) {
            $content = str_replace('<head>', '<head>'.$tags['css'], $content);
        }

        if (isset($tags['js'])) {
            $content = str_replace('</body>', $tags['js'] . '</body>', $content);
        }

        return $content;
    }

    public function generateTags()
    {
        #  generate from controller
        $tags = $this->generateTagsForController();

        # if can't, ten from router
        if (!$tags) {
            $tags = $this->generateTagsForRouter();
        }

        return $tags;
    }

    public function generateTagsForController()
    {
        $assetOptions = $this->configuration->getController($this->getControllerName());
        if (!$assetOptions) {
            return false;
        }

        $am = $this->getAssetManager();

        $tags = new TagGenerator($this->configuration->getBaseUrl(), $am);
        return $tags->getnerateTagFromOptions($assetOptions);
    }

    public function generateTagsForRouter()
    {
        $assetOptions = $this->configuration->getRoute($this->getRouteName());
        if (!$assetOptions) {
            return false;
        }

        $am = $this->getAssetManager();

        $tags = new TagGenerator($this->configuration->getBaseUrl(), $am);
        return $tags->getnerateTagFromOptions($assetOptions);
    }

    public function generateTagsForAllAssets()
    {
        $am = $this->getAssetManager();
        $tags = new TagGenerator($this->configuration->getBaseUrl(), $am);
        return $tags->generateTagFromAssetsManager($this->getAssetManager());
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
}
