<?php

namespace AsseticBundle;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class ServiceFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $locator
     * @param string $requestedName
     * @param array $options, optional
     *
     * @return \AsseticBundle\Service
     */
    public function __invoke(ContainerInterface $locator, $requestedName, array $options = null)
    {
        $asseticConfig = $locator->get('AsseticConfiguration');
        if ($asseticConfig->detectBaseUrl()) {
            /** @var $request \Laminas\Http\PhpEnvironment\Request */
            $request = $locator->get('Request');
            if (method_exists($request, 'getBaseUrl')) {
                $asseticConfig->setBaseUrl($request->getBaseUrl());
            }
        }

        $asseticService = new Service($asseticConfig);
        $asseticService->setAssetManager($locator->get('Assetic\AssetManager'));
        $asseticService->setAssetWriter($locator->get('Assetic\AssetWriter'));
        $asseticService->setFilterManager($locator->get('Assetic\FilterManager'));

        // Cache buster is not mandatory
        if ($locator->has('AsseticCacheBuster')) {
            $asseticService->setCacheBusterStrategy($locator->get('AsseticCacheBuster'));
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
