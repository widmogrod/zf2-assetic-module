<?php
namespace AsseticBundle;

use Zend\Stdlib;

class Configuration
{
    protected $routes = array();

    protected $debug = false;

    protected $webPath;

    protected $cachePath;

    protected $cacheEnabled = false;

    protected $baseUrl;

    protected $modules = array();

    protected $controllers = array();

    protected $rendererToStrategy = array();

    public function __construct($config)
    {
        if (!is_null($config)) {
            if (is_array($config)) {
                $this->processArray($config);
            } elseif ($config instanceof \Traversable) {
                $this->processArray(\Zend\Stdlib\ArrayUtils::iteratorToArray($config));
            } else {
                throw new \InvalidArgumentException(
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

    public function setWebPath($path)
    {
        if (!is_dir($path)) {
            throw new \RuntimeException('Directory do not exists: '.$path);
        }

        if (!is_writable($path)) {
            throw new \RuntimeException('Directory is not writable: '.$path);
        }

        $this->webPath = $path;
    }

    public function getWebPath()
    {
        if (null === $this->webPath) {
            throw new \RuntimeException('Web path is not set');
        }

        return $this->webPath;
    }

    public function setCachePath($path)
    {
        if (!is_dir($path)) {
            throw new \RuntimeException('Directory do not exists: '.$path);
        }

        if (!is_writable($path)) {
            throw new \RuntimeException('Directory is not writable: '.$path);
        }

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
        return array_key_exists($name, $this->routes)
                ? $this->routes[$name]
                : $default;
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
        $this->modules = $modules;
    }

    public function getModules()
    {
        return $this->modules;
    }

    public function getModule($name, $default)
    {
        return array_key_exists($name, $this->modules)
                ? $this->modules[$name]
                : $default;
    }

    public function setBaseUrl($baseUrl)
    {
        $this->baseUrl = $baseUrl;
    }

    public function getBaseUrl()
    {
        return rtrim($this->baseUrl, '/') . '/';
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
            throw new \BadMethodCallException(
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

    public function getStrategyNameForRenderer($rendererName, $default = null)
    {
        return array_key_exists($rendererName, $this->rendererToStrategy)
            ? $this->rendererToStrategy[$rendererName]
            : $default;
    }
}
