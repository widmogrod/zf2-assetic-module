<?php
namespace AsseticBundle;

use Assetic\AssetWriter;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class WriterFactory implements FactoryInterface
{
    /**
     * @param ServiceLocatorInterface $locator
     * @return \Assetic\AssetWriter;
     */
    public function createService(ServiceLocatorInterface $locator)
    {
        $asseticConfig = $locator->get('AsseticConfiguration');
        $asseticWriter = new AssetWriter($asseticConfig->getWebPath());

        return $asseticWriter;
    }
}
