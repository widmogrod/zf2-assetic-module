<?php
namespace AsseticBundle;

use Zend\ModuleManager\ModuleManager,
    Zend\ModuleManager\ModuleManagerInterface,
    Zend\Http\Response,
    Zend\EventManager\EventInterface,
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
        $this->loadedModules = $manager->getLoadedModules();
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

    public function getConfig()
    {
        return include __DIR__ . '/configs/module.config.php';
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__ . '/'
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

        $sm = $e->getApplication()->getServiceManager();

        $router = $e->getRouteMatch();

        /** @var $as \AsseticBundle\Service */
        $as = $sm->get('AsseticService');

        # setup service
        $as->setRouteName($router->getMatchedRouteName());
        $as->setControllerName($router->getParam('controller'));
        $as->setActionName($router->getParam('action'));

        # init assets for modules
        $as->initLoadedModules($this->loadedModules);
        $as->setupRenderer($sm->get('ViewRenderer'));
    }
}