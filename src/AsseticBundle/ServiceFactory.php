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

        $asseticService = new Service($asseticConfig);
        $asseticService->setAssetManager($asseticAssetManager);
        $asseticService->setFilterManager($asseticFilterManager);

        $strategies = isset($configuration['assetic_configuration']['strategies'])
            ? $configuration['assetic_configuration']['strategies']
            : array();

        return $asseticService;
    }
}
