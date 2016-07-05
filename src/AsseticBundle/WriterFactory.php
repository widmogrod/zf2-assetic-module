<?php
namespace AsseticBundle;

use Assetic\AssetWriter;

use Interop\Container\ContainerInterface;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class WriterFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $locator
     * @param String $requestedName
     * @param Array $options, optional
     * @return \AsseticBundle\FilterManager
     */
    public function __invoke(ContainerInterface $locator, $requestedName, array $options = null)
    {
        $asseticConfig = $locator->get('AsseticConfiguration');
        $asseticWriter = new AssetWriter($asseticConfig->getWebPath());

        return $asseticWriter;
    }

    /**
     * @param ServiceLocatorInterface $locator
     * @return \Assetic\AssetWriter;
     */
    public function createService(ServiceLocatorInterface $locator)
    {
        return $this($locator, 'AssetWriter');
    }
}
