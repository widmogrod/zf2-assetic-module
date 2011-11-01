<?php
namespace Assetic;

use Zend\Stdlib;

class Configuration
{
    protected $data = array();

    protected $routes = array();

    protected $debug = false;

    protected $webPath;

    protected $baseUrl;

    public function __construct(array $data)
    {
        if ($data instanceof Traversable) {
            if (method_exists($data, 'toArray')) {
                $data = $data->toArray();
            } else {
                $data = iterator_to_array($data, true);
            }
        } elseif (!is_array($data)) {
            throw new Exception\InvalidArgumentException(
                'Configuration data must be of type Zend\Config\Config or an array'
            );
        }

        $this->data = array();
        foreach ($data as $key => $value)
        {
            $method = 'set'.ucfirst($key);
            if (method_exists($this, $method))
            {
                $this->$method($value);
            }
            else
            {
                $this->data[$key] = $value;
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


}
