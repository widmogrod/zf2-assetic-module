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
    Zend\ModuleManager\Feature\BootstrapListenerInterface;

class Module
    implements
        InitProviderInterface,
        AutoloaderProviderInterface,
        ConfigProviderInterface,
        BootstrapListenerInterface,
        ServiceProviderInterface
{
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
        $app->getEventManager()->attach('dispatch', array($this, 'renderAssets'), 32);
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

    public function renderAssets(\Zend\Mvc\MvcEvent $e)
    {
        $response = $e->getResponse();
        if (!$response) {
            $response = new Response();
            $e->setResponse($response);
        }

        $sm = $e->getApplication()->getServiceManager();

        $router = $e->getRouteMatch();

        /** @var $asseticService \AsseticBundle\Service */
        $asseticService = $sm->get('AsseticService');

        # setup service
        $asseticService->setRouteName($router->getMatchedRouteName());
        $asseticService->setControllerName($router->getParam('controller'));
        $asseticService->setActionName($router->getParam('action'));

        # init assets for modules
        $asseticService->initLoadedModules($this->loadedModules);
        $asseticService->setupRenderer($sm->get('ViewRenderer'));
    }
}