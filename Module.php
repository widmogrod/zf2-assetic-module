<?php

namespace AsseticBundle;

use Zend\ModuleManager\ModuleManagerInterface,
    Zend\Http\Response,
    Zend\EventManager\Event,
    Zend\EventManager\StaticEventManager,
    Zend\ModuleManager\Feature\InitProviderInterface,
    Zend\ModuleManager\Feature\AutoloaderProviderInterface,
    Zend\ModuleManager\Feature\ConfigProviderInterface,
    Zend\ModuleManager\Feature\BootstrapListenerInterface;

class Module implements
    InitProviderInterface, AutoloaderProviderInterface,
    ConfigProviderInterface, BootstrapListenerInterface
{
    /**
     * @var \Zend\ServiceManager\ServiceManager
     */
    protected $service;

    /**
     * @var \Zend\ModuleManager\ModuleManager $manager
     */
    protected $moduleManager;

    /**
     * @var array
     */
    protected $loadedModules;

    /**
     * Initialize workflow
     *
     * @param  ModuleManagerInterface $manager
     * @return void
     */
    public function init(ModuleManagerInterface $manager)
    {
        $this->moduleManager = $manager;
    }

    /**
     * Listen to the bootstrap event
     *
     * @return array
     */
    public function onBootstrap(Event $e)
    {
        $app = $e->getApplication();
        $app->getEventManager()->attach('dispatch', array($this, 'renderAssets'), 32);
        $this->service = $app->getServiceManager();
    }

    public function getProvides()
    {
        return array(
            __NAMESPACE__ => array(
                'version' => '0.1.0'
            ),
        );
    }

    /**
     * Returns configuration to merge with application configuration
     * 
     * @return array|\Traversable
     */
    public function getConfig($env = null)
    {
        return include __DIR__ . '/configs/module.config.php';
    }

    /**
     * Return an array for passing to Zend\Loader\AutoloaderFactory.
     *
     * @return array
     */
    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\ClassMapAutoloader' => array(
                __DIR__ . '/autoload_classmap.php',
            ),
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }

    public function renderAssets(\Zend\Mvc\MvcEvent $e)
    {
        $response = $e->getResponse();
        if (!$response) {
            $response = new Response();
            $e->setResponse($response);
        }

        $router = $e->getRouteMatch();

        /** @var $as \AsseticBundle\Service */
        $as = $this->service->get('AsseticService');

        # setup service
        $as->setRouteName($router->getMatchedRouteName());
        $as->setControllerName($router->getParam('controller'));
        $as->setActionName($router->getParam('action'));

        # init assets for modules
        $as->initLoadedModules($this->getLoadedModules());
        $as->setupRenderer($this->getRenderer());
    }

    private function getLoadedModules()
    {
        if (null === $this->loadedModules) {
            $this->loadedModules = $this->moduleManager->getLoadedModules();
        }
        return $this->loadedModules;
    }

    private function getRenderer()
    {
        return $this->service->get('ViewRenderer');
    }
}
