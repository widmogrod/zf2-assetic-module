<?php

namespace AsseticBundle\Factory;

use AsseticBundle\AsseticMiddleware;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

class MiddlewareFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        /** @var $asseticService \AsseticBundle\Service */
        $asseticService = $container->get('AsseticService');
        $viewRenderer = $container->get('ViewRenderer');

        return new AsseticMiddleware($asseticService, $viewRenderer);
    }
}
