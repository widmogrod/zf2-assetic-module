<?php
namespace AsseticBundle;

use Zend\ModuleManager\ModuleManager,
    Zend\ModuleManager\ModuleManagerInterface,
    Zend\Http\Response,
    Zend\EventManager\EventInterface,
    Zend\ServiceManager\ServiceLocatorInterface,
    Zend\ModuleManager\Feature\InitProviderInterface,
    Zend\ModuleManager\Feature\AutoloaderProviderInterface,
    Zend\ModuleManager\Feature\ConfigProviderInterface,
    Zend\ModuleManager\Feature\ServiceProviderInterface,
    Zend\ModuleManager\Feature\BootstrapListenerInterface,
    Zend\Mvc\MvcEvent,
    Zend\Mvc\Application;

class Module
    implements
        InitProviderInterface,
        AutoloaderProviderInterface,
        ConfigProviderInterface,
        BootstrapListenerInterface,
        ServiceProviderInterface
{
    /**
     * @var ModuleManagerInterface
     */
    protected $moduleManager;

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
        $app->getEventManager()->attach('dispatch', array($this, 'renderAssets'), 32);
        $app->getEventManager()->attach(MvcEvent::EVENT_DISPATCH_ERROR, array($this, 'renderAssets'), 32);
    }

    public function getConfig()
    {
        return include __DIR__ . '/configs/module.config.php';
    }

    /**
     * Expected to return \Zend\ServiceManager\Config object or array to
     * seed such an object.
     *
     * @return array|\Zend\ServiceManager\Config
     */
    public function getServiceConfig()
    {
        return array(
            'initializers' => array(
                function($object, ServiceLocatorInterface $serviceManager) {
                    if ($object instanceof AsseticBundleServiceAwareInterface) {
                        $object->setAsseticBundleService($serviceManager->get('AsseticService'));
                    }
                }
            ),
        );
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

    public function renderAssets(MvcEvent $e)
    {
        $sm     = $e->getApplication()->getServiceManager();
        $config = $sm->get('AsseticConfiguration');
        if ($e->getName() === MvcEvent::EVENT_DISPATCH_ERROR) {
            $error = $e->getError();
            if ($error && !in_array($error, $config->getAcceptableErrors())) {
                // break if not an acceptable error
                return;
            }
        }

        $response = $e->getResponse();
        if (!$response) {
            $response = new Response();
            $e->setResponse($response);
        }

        /** @var $asseticService \AsseticBundle\Service */
        $asseticService = $sm->get('AsseticService');

        # setup service if a matched route exist
        $router = $e->getRouteMatch();
        if ($router) {
            $asseticService->setRouteName($router->getMatchedRouteName());
            $asseticService->setControllerName($router->getParam('controller'));
            $asseticService->setActionName($router->getParam('action'));
        }

        # init assets for modules
        $asseticService->initLoadedModules($this->moduleManager->getLoadedModules());
        $asseticService->setupRenderer($sm->get('ViewRenderer'));
    }
}
