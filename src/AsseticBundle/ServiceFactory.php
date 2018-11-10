<?php

namespace AsseticBundle;

use Interop\Container\ContainerInterface;
use Zend\Expressive\Helper\UrlHelper;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class ServiceFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array $options, optional
     * @return \AsseticBundle\Service
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $asseticConfig = $container->get('AsseticConfiguration');
        if ($asseticConfig->detectBaseUrl()) {
            if (class_exists(\Zend\Expressive\Application::class)) { // is expressive app
                $urlHelper = $container->get(UrlHelper::class);
                $asseticConfig->setBaseUrl($urlHelper->getBasePath());
            } else {
                /** @var $request \Zend\Http\PhpEnvironment\Request */
                $request = $container->get('Request');
                if (method_exists($request, 'getBaseUrl')) {
                    $asseticConfig->setBaseUrl($request->getBaseUrl());
                }
            }
        }

        $asseticService = new Service($asseticConfig);
        $asseticService->setAssetManager($container->get('Assetic\AssetManager'));
        $asseticService->setAssetWriter($container->get('Assetic\AssetWriter'));
        $asseticService->setFilterManager($container->get('Assetic\FilterManager'));
        // Cache buster is not mandatory
        if ($container->has('AsseticCacheBuster')) {
            $asseticService->setCacheBusterStrategy($container->get('AsseticCacheBuster'));
        }
        return $asseticService;
    }

    /**
     * @param ServiceLocatorInterface $locator
     *
     * @return \AsseticBundle\Service
     */
    public function createService(ServiceLocatorInterface $locator)
    {
        return $this($locator, 'AsseticService');
    }
}
