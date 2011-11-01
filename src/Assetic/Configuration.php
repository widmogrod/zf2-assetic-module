<?php
namespace Assetic;

use Zend\Stdlib;

class Configuration
{
    protected $routes = array();

    protected $debug = false;

    protected $webPath;

    protected $baseUrl;

    public function __construct(array $config)
    {
        if (!is_null($config)) {
            if (is_array($config) || $config instanceof \Traversable) {
                $this->processArray($config);
            } else {
                throw new \InvalidArgumentException(
                    'Parameter to \\Zend\\Stdlib\\Configuration\'s '
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

    protected $modules;

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
        return (null === $this->baseUrl) ? '/' : $this->baseUrl;
    }


    protected function processArray($config)
    {
        foreach ($config as $key => $value) {
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

    protected function assembleGetterNameFromConfigKey($key)
    {
        $parts = explode('_', $key);
        $parts = array_map('ucfirst', $parts);
        $getter = 'get' . implode('', $parts);
        if (!method_exists($this, $getter)) {
            throw new \BadMethodCallException(
                'The configuration key "' . $key . '" does not '
                . 'have a matching ' . $getter . ' getter method '
                . 'which must be defined'
            );
        }
        return $getter;
    }

    public function __set($key, $value)
    {
        $setter = $this->assembleSetterNameFromConfigKey($key);
        $this->{$setter}($value);
    }

    public function __get($key)
    {
        $getter = $this->assembleGetterNameFromConfigKey($key);
        return $this->{$getter}();
    }

    public function __isset($key)
    {
        $getter = $this->assembleGetterNameFromConfigKey($key);
        return !is_null($this->{$getter}());
    }

    public function __unset($key)
    {
        $setter = $this->assembleSetterNameFromConfigKey($key);
        $this->{$setter}(null);
    }
}
