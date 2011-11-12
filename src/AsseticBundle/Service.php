<?php
namespace AsseticBundle;

use Assetic\AssetManager,
    Assetic\FilterManager,
    Assetic\Factory,
    Assetic\AssetWriter;

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
                $asset = $factory->createAsset($assets, $filters, $options);
                $this->assetManager->set($name, $asset);
            }

            $writer = new AssetWriter($this->configuration->getWebPath());
            $writer->writeManagerAssets($this->assetManager);
        }
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

        # if can't, ten from all assets
        if (!$tags) {
            $tags = $this->generateTagsForAllAssets();
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
