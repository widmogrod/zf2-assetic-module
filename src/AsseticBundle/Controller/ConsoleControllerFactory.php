<?php
namespace AsseticBundle\Controller;

use Interop\Container\ContainerInterface;

use Zend\ServiceManager\Factory\FactoryInterface;

class ConsoleControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $instance = new ConsoleController();
        $instance->setAsseticBundleService($container->get('AsseticService'));

        return $instance;
    }
}
