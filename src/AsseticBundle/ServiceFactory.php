<?php
namespace AsseticBundle;

use Interop\Container\ContainerInterface;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class ServiceFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $locator
     * @param String $requestedName
     * @param Array $options, optional
     * @return \AsseticBundle\Service
     */
    public function __invoke(ContainerInterface $locator, $requestedName, array $options = null)
    {
        $asseticConfig = $locator->get('AsseticConfiguration');
        if ($asseticConfig->detectBaseUrl()) {
            /** @var $request \Zend\Http\PhpEnvironment\Request */
            $request = $locator->get('Request');
            if (method_exists($request, 'getBaseUrl')) {
                $asseticConfig->setBaseUrl($request->getBaseUrl());
            }
        }

        $asseticService = new Service($asseticConfig);
        $asseticService->setAssetManager($locator->get('AsseticAssetManager'));
        $asseticService->setAssetWriter($locator->get('AsseticAssetWriter'));
        $asseticService->setFilterManager($locator->get('AsseticFilterManager'));

        // Cache buster is not mandatory
        if ($locator->has('AsseticCacheBuster')){
            $asseticService->setCacheBusterStrategy($locator->get('AsseticCacheBuster'));
        }

        return $asseticService;
    }

    /**
     * @param ServiceLocatorInterface $locator
     * @return \AsseticBundle\Service
     */
    public function createService(ServiceLocatorInterface $locator)
    {
        return $this($locator, 'AsseticService');
    }
}
