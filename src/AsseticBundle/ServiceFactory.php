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
        $configuration = $serviceLocator->get('Configuration');

        $asseticConfig = new Configuration($configuration['assetic_configuration']);
        $asseticAssetManager = $serviceLocator->get('Assetic\AssetManager');
        $asseticFilterManager = $serviceLocator->get('Assetic\FilterManager');

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
        $asseticService->setFilterManager($asseticFilterManager);

        return $asseticService;
    }
}
