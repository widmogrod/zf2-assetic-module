<?php

namespace AsseticBundle\Factory;

use AsseticBundle\Configuration;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

class ConfigurationFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $configuration = $container->get('Configuration');

        return new Configuration($configuration['assetic_configuration']);
    }
}
