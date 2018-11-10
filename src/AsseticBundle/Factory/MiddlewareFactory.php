<?php

namespace AsseticBundle\Factory;

use AsseticBundle\Service as AsseticService;
use AsseticBundle\AsseticMiddleware;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;
use Zend\View\Renderer\PhpRenderer;

class MiddlewareFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        /** @var $asseticService AsseticService */
        $asseticService = $container->get('AsseticService');

        // Create or retrieve the renderer from the container
        $viewRenderer = ($container->has(PhpRenderer::class))
            ? $container->get(PhpRenderer::class)
            : new PhpRenderer();

        return new AsseticMiddleware($asseticService, $viewRenderer);
    }
}
