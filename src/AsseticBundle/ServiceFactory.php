<?php
namespace AsseticBundle;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class ServiceFactory implements FactoryInterface
{
    /**
     * @param ServiceLocatorInterface $serviceLocator
     * @return \AsseticBundle\Service
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $asseticConfig        = $serviceLocator->get('AsseticConfiguration');
        $asseticAssetManager  = $serviceLocator->get('AsseticAssetManager');
        $asseticAssetWriter   = $serviceLocator->get('AsseticAssetWriter');
        $asseticCacheBuster   = $serviceLocator->get('AsseticCacheBuster');
        $asseticFilterManager = $serviceLocator->get('AsseticFilterManager');

        // inject base url & path
        if ($asseticConfig->detectBaseUrl()) {
            /** @var $request \Zend\Http\PhpEnvironment\Request */
            $request = $serviceLocator->get('Request');
            if (method_exists($request, 'getBaseUrl')) {
                $asseticConfig->setBaseUrl($request->getBaseUrl());
            }
        }

        $asseticService = new Service($asseticConfig);
        $asseticService->setAssetManager($asseticAssetManager);
        $asseticService->setAssetWriter($asseticAssetWriter);
        $asseticService->setCacheBusterStrategy($asseticCacheBuster);
        $asseticService->setFilterManager($asseticFilterManager);

        return $asseticService;
    }
}
