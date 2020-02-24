<?php

namespace AsseticBundle\Cli;

use Interop\Container\ContainerInterface;
use Symfony\Component\Console\Application;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class ApplicationFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array $options, optional
     *
     * @return Application
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $cliApplication = new Application('AsseticBundle', '1.7.0');

        $cliApplication->add(new BuildCommand($container->get('AsseticService')));
        $cliApplication->add(new SetupCommand($container->get('AsseticService')));

        return $cliApplication;
    }

    /**
     * @param ServiceLocatorInterface $locator
     *
     * @return \AsseticBundle\FilterManager
     */
    public function createService(ServiceLocatorInterface $locator)
    {
        return $this($locator, 'AsseticBundle\Cli');
    }
}
