<?php
/**
 * Created by JetBrains PhpStorm.
 * User: gabriel
 * Date: 30.10.2011
 * Time: 23:28
 * To change this template use File | Settings | File Templates.
 */

namespace Assetic\Service;

class Service
{
    const DEFAULT_NAMESPACE = 'default';

    protected $namespace;

    public function __construct()
    {
        
    }

    public function setNamespace($namespace)
    {
        $this->namespace = (string) $namespace;
    }

    public function getNamespace()
    {
        return (null === $this->namespace) ? self::DEFAULT_NAMESPACE : $this->namespace;
    }
}
