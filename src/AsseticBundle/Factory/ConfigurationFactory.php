<?php

namespace AsseticBundle\Factory;

use AsseticBundle\Configuration;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

class ConfigurationFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $config = $container->get('config');

        return new Configuration($config['assetic_configuration']);
    }
}
