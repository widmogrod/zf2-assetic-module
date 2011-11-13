<?php

namespace AsseticBundle;

use Zend\Module\Manager,
    Zend\Loader\AutoloaderFactory,
    Zend\Http\Response,
    Zend\EventManager\StaticEventManager;

class Module
{
    protected $_currentRouteName;

    /**
     * @var \Zend\Module\Manager
     */
    protected $moduleManager;

    public function init(Manager $moduleManager)
    {
        $this->initAutoloader();

        $this->moduleManager = $moduleManager;

        # pre bootstrap
        $events = StaticEventManager::getInstance();
        $events->attach('bootstrap', 'bootstrap', array($this, 'initAssetsListner'), 200);
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

    public function initAutoloader()
    {
        AutoloaderFactory::factory(array(
            'Zend\Loader\ClassMapAutoloader' => array(
                __DIR__ . '/autoload_classmap.php',
            ),
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    'Assetic' => __DIR__ . '/library/assetic/src/Assetic',
                ),
            ),
        ));
    }

    protected $locator;

    public function initAssetsListner(\Zend\EventManager\Event $e)
    {
        /* @var $app \Zend\Mvc\Application */
        $app = $e->getParam('application');

        $this->locator = $app->getLocator();

        # post dispatch action
        $app->events()->attach('dispatch', array($this, 'renderAssets'), 32);
    }

    public function renderAssets(\Zend\Mvc\MvcEvent $e)
    {
        $response = $e->getResponse();
        if (!$response) {
            $response = new Response();
            $e->setResponse($response);
        }

        $router = $e->getRouteMatch();

        /* $var $as \AsseticBundle\Service */
        $as = $this->locator->get('assetic');

        # setup service
        $as->setRouteName($router->getMatchedRouteName());
        $as->setControllerName($router->getParam('controller'));
        $as->setActionName($router->getParam('action'));

        # init assets for modules
        $as->initLoadedModules($this->moduleManager->getLoadedModules());

        # setup response content (attache stylesheet's nad scripts)
        # @todo allow to setup view helpers.
//        $content = $response->getContent();
//        $content = $as->setupResponseContent($content);
//        $response->setContent($content);

        try {
            $as->setupViewHelpers($this->locator->get('view'));
        } catch(\Exception $e) {
            \Zend\Debug::dump($e->getMessage());
            \Zend\Debug::dump($e->getFile());
            \Zend\Debug::dump($e->getLine());
        }


//        return $response;
    }
}