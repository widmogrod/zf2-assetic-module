<?php
namespace AsseticBundle;

use Assetic\AssetWriter;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class WriterFactory implements FactoryInterface
{
    /**
     * @param ServiceLocatorInterface $serviceLocator
     * @return \Assetic\AssetWriter;
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $asseticConfig = $serviceLocator->get('AsseticConfiguration');
        $asseticWriter = new AssetWriter($asseticConfig->getWebPath());

        return $asseticWriter;
    }
}
