<?php

namespace AsseticBundle;

use Zend\ModuleManager\ModuleManager,
    Zend\ModuleManager\ModuleManagerInterface,
    Zend\Http\Response,
    Zend\EventManager\EventInterface,
    Zend\EventManager\StaticEventManager,
    Zend\ModuleManager\Feature\InitProviderInterface,
    Zend\ModuleManager\Feature\AutoloaderProviderInterface,
    Zend\ModuleManager\Feature\ConfigProviderInterface,
    Zend\ModuleManager\Feature\BootstrapListenerInterface;

class Module implements InitProviderInterface, AutoloaderProviderInterface, ConfigProviderInterface, BootstrapListenerInterface
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
     * @param  \Zend\ModuleManager\ModuleManager $manager
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
    public function onBootstrap(EventInterface $e)
    {
        /**
         * @var $app \Zend\Mvc\Application
         * @var $e \Zend\Mvc\MvcEvent
         */
        $app = $e->getApplication();
        $this->service = $app->getServiceManager();

        $app->getEventManager()->attach('dispatch', array($this, 'renderAssets'), 32);
    }

    public function getProvides()
    {
        return array(
            __NAMESPACE__ => array(
                'version' => '0.1.0'
            ),
        );
    }

    public function getConfig($env = null)
    {
        return include __DIR__ . '/configs/module.config.php';
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\ClassMapAutoloader' => array(
                __DIR__ . '/autoload_classmap.php',
            ),
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    'Assetic' => __DIR__ . '/vendor/assetic/src/Assetic',
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