<?php
namespace AsseticBundle;

use Zend\Stdlib;
use AsseticBundle\Exception;

class Configuration
{
    /**
     * Debug option that is passed to Assetic.
     *
     * @var bool
     */
    protected $debug = false;
    
    /**
     * Combine option giving the opportunity not to combine the assets in debug mode.
     *
     * @var bool
     */
    protected $combine = true;

    /**
     * Should build assets on request.
     *
     * @var bool
     */
    protected $buildOnRequest = true;

    /**
     * Full path to public directory where assets will be generated.
     *
     * @var string
     */
    protected $webPath;

    /**
     * Full path to cache directory.
     *
     * @var string
     */
    protected $cachePath;

    /**
     * Is cache enabled.
     *
     * @var bool
     */
    protected $cacheEnabled = false;

    /**
     * The base url.
     *
     * By default this value is set from "\Zend\Http\PhpEnvironment\Request::getBaseUrl()"
     *
     * Example:
     * <code>
     * http://example.com/
     * </code>
     *
     * @var string|null
     */
    protected $baseUrl;

    /**
     * The base path.
     *
     * By default this value is set from "\Zend\Http\PhpEnvironment\Request::getBasePath()"
     *
     * Example:
     * <code>
     * <baseUrl>/~jdo/
     * </code>
     *
     * @var string|null
     */
    protected $basePath;

    /**
     * Asset will be save on disk, only when it's modification time was changed
     *
     * @var bool
     */
    protected $writeIfChanged = true;

    /**
     * Default options.
     *
     * @var array
     */
    protected $default = array(
        'assets' => array(),
        'options' => array(),
    );

    /**
     * Map of routes names and assets configuration.
     *
     * @var array
     */
    protected $routes = array();

    /**
     * Map of modules names and assets configuration.
     *
     * @var array
     */
    protected $modules = array();

    /**
     * Map of controllers names and assets configuration.
     *
     * @var array
     */
    protected $controllers = array();

    /**
     * Map of strategies that will be choose to setup Assetic\AssetInterface
     * for particular Zend\View\Renderer\RendererInterface
     *
     * @var array
     */
    protected $rendererToStrategy = array();

    /**
     * List of error types occurring in EVENT_DISPATCH_ERROR that will use
     * this module to render assets.
     *
     * @var array
     */
    protected $acceptableErrors = array();

    /**
     * Umask
     *
     * @var null|integer
     */
    protected $umask = null;

    public function __construct($config = null)
    {
        if (null !== $config) {
            if (is_array($config)) {
                $this->processArray($config);
            } elseif ($config instanceof \Traversable) {
                $this->processArray(Stdlib\ArrayUtils::iteratorToArray($config));
            } else {
                throw new Exception\InvalidArgumentException(
                    'Parameter to \\AsseticBundle\\Configuration\'s '
                    . 'constructor must be an array or implement the '
                    . '\\Traversable interface'
                );
            }
        }
    }

    public function isDebug()
    {
        return $this->debug;
    }

    public function setDebug($flag)
    {
        $this->debug = (bool) $flag;
    }

    public function isCombine()
    {
    	return $this->combine;
    }
    
    public function setCombine($flag)
    {
    	$this->combine = (bool) $flag;
    }
    
    public function setWebPath($path)
    {
        $this->webPath = $path;
    }

    public function getWebPath($file = null)
    {
        if (null === $this->webPath) {
            throw new Exception\RuntimeException('Web path is not set');
        }

        if (null !== $file) {
            return rtrim($this->webPath, '/\\') . '/' . ltrim($file, '/\\');
        }

        return $this->webPath;
    }

    public function setCachePath($path)
    {
        $this->cachePath = $path;
    }

    public function getCachePath()
    {
        return $this->cachePath;
    }

    public function setCacheEnabled($cacheEnabled)
    {
        $this->cacheEnabled = (bool) $cacheEnabled;
    }

    public function getCacheEnabled()
    {
        return $this->cacheEnabled;
    }

    public function setDefault(array $default)
    {
        if (!isset($default['assets'])) {
            $default['assets'] = array();
        }

        if (!isset($default['options'])) {
            $default['options'] = array();
        }

        $this->default = $default;
    }

    public function getDefault()
    {
        return $this->default;
    }

    public function setRoutes(array $routes)
    {
        $this->routes = $routes;
    }

    public function getRoutes()
    {
        return $this->routes;
    }

    public function getRoute($name, $default = null)
    {
        $assets = array();
        $routeMatched = false;

        // Merge all assets configuration for which regular expression matches route
        foreach ($this->routes as $spec => $config) {
            if (preg_match('(^' . $spec . '$)i', $name)) {
                $routeMatched = true;
                $assets = Stdlib\ArrayUtils::merge($assets, (array) $config);
            }
        }

        // Only return default if none regular expression matched
        return $routeMatched ? $assets : $default;
    }

    public function setControllers(array $controllers)
    {
        $this->controllers = $controllers;
    }

    public function getControllers()
    {
        return $this->controllers;
    }

    public function getController($name, $default = null)
    {
        return array_key_exists($name, $this->controllers)
            ? $this->controllers[$name]
            : $default;
    }

    public function setModules(array $modules)
    {
        $this->modules = array();
        foreach ($modules as $name => $options) {
            $this->addModule($name, $options);
        }
    }

    public function addModule($name, array $options) {
        $name = strtolower($name);
        $this->modules[$name] = $options;
    }

    public function getModules()
    {
        return $this->modules;
    }

    public function getModule($name, $default = null)
    {
        $name = strtolower($name);
        return array_key_exists($name, $this->modules)
            ? $this->modules[$name]
            : $default;
    }

    public function detectBaseUrl()
    {
        return (null === $this->baseUrl || 'auto' === $this->baseUrl);
    }

    public function setBaseUrl($baseUrl)
    {
        if (null !== $baseUrl && 'auto' !== $baseUrl) {
            $baseUrl = rtrim($baseUrl, '/') . '/';
        }
        $this->baseUrl = $baseUrl;
    }

    public function getBaseUrl()
    {
        return $this->baseUrl;
    }

    /**
     * @param null|string $basePath
     */
    public function setBasePath($basePath)
    {
        if (null !== $basePath) {
            $basePath = trim($basePath, '/');
            $basePath = $basePath . '/';
        }
        $this->basePath = $basePath;
    }

    /**
     * @return null|string
     */
    public function getBasePath()
    {
        return $this->basePath;
    }

    protected function processArray($config)
    {
        foreach ($config as $key => $value)
        {
            $setter = $this->assembleSetterNameFromConfigKey($key);
            $this->{$setter}($value);
        }
    }

    protected function assembleSetterNameFromConfigKey($key)
    {
        $parts = explode('_', $key);
        $parts = array_map('ucfirst', $parts);
        $setter = 'set' . implode('', $parts);
        if (!method_exists($this, $setter)) {
            throw new Exception\BadMethodCallException(
                'The configuration key "' . $key . '" does not '
                . 'have a matching ' . $setter . ' setter method '
                . 'which must be defined'
            );
        }
        return $setter;
    }

    public function setRendererToStrategy(array $strategyForRenderer)
    {
        $this->rendererToStrategy = $strategyForRenderer;
    }

    public function addRendererToStrategy($rendererClass, $strategyClass)
    {
        $this->rendererToStrategy[$rendererClass] = $strategyClass;
    }

    public function getStrategyNameForRenderer($rendererName, $default = null)
    {
        return array_key_exists($rendererName, $this->rendererToStrategy)
            ? $this->rendererToStrategy[$rendererName]
            : $default;
    }

    public function setAcceptableErrors(array $acceptableErrors)
    {
        $this->acceptableErrors = $acceptableErrors;
    }

    public function getAcceptableErrors()
    {
        return $this->acceptableErrors;
    }

    /**
     * @return integer|null
     */
    public function getUmask()
    {
        return $this->umask;
    }

    /**
     * @param null|integer $umask
     */
    public function setUmask($umask)
    {
        $this->umask = null;
        if (is_int($umask)) {
            $this->umask = (int) $umask;
        }
    }

    /**
     * @param boolean $flag
     */
    public function setBuildOnRequest($flag)
    {
        $this->buildOnRequest = (bool) $flag;
    }

    /**
     * @return boolean
     */
    public function getBuildOnRequest()
    {
        return $this->buildOnRequest;
    }

    /**
     * @param boolean $flag
     */
    public function setWriteIfChanged($flag)
    {
        $this->writeIfChanged = (bool) $flag;
    }

    /**
     * @return boolean
     */
    public function getWriteIfChanged()
    {
        return $this->writeIfChanged;
    }
}
