<?php

namespace AsseticBundle;

use Interop\Container\ContainerInterface;
use Zend\Expressive\Helper\UrlHelper;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

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
            //$request = $locator->get('Request');
            //$request = $locator->get(\Zend\Expressive\Router\RouterInterface::class);
            //if (method_exists($request, 'getBaseUrl')) {
            //    $asseticConfig->setBaseUrl($request->getBaseUrl());
            //}

            $urlHelper = $locator->get(UrlHelper::class);
            //$this->helper->setBasePath($locale);
            //$basePath = $this->getBasePath();
            $asseticConfig->setBaseUrl($urlHelper->getBasePath());

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
