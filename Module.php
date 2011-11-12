<?php

namespace AsseticBundle;

use Zend\Config\Config,
    Zend\Module\Manager,
    Zend\Loader\AutoloaderFactory;

#use Zend\EventManager\StaticEventManager;
use Assetic\Asset\AssetCollection;
use Assetic\Asset\FileAsset;
use Assetic\Asset\GlobAsset;
use Assetic\AssetManager;
use Assetic\Asset\HttpAsset;
use Assetic\Factory\AssetFactory;
//use Assetic\Filter\CssMinFilter;
use Assetic\Filter\Yui;
#use Assetic\Factory\AssetFactory;

use Zend\Http\Response;

use Zend\EventManager\StaticEventManager;

class Module
{
    protected $_currentRouteName;

    protected $moduleManager;

    public function init(Manager $moduleManager)
    {
        $this->initAutoloader();

        $this->moduleManager = $moduleManager;

        $events = StaticEventManager::getInstance();
        // pre
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
        return new Config(include __DIR__ . '/configs/module.config.php');
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

        // post
//        $app->events()->attach('route', array($this, 'currentRouteName'), -200);
        $app->events()->attach('dispatch', array($this, 'renderAssets'), -2000);
    }

//    public function currentRouteName(\Zend\Mvc\MvcEvent $e)
//    {
//
//        $this->_currentRouteName = $router->getMatchedRouteName();
//    }

    public function renderAssets(\Zend\Mvc\MvcEvent $e)
    {
        $response = $e->getResponse();
        if (!$response) {
            $response = new Response();
            $e->setResponse($response);
        }

        $router = $e->getRouteMatch();

        /* $var $as \AsseticBundle\Service */
        $as = $this->locator->get('assetic-service');

        $as->setRouteName($router->getMatchedRouteName());
        $as->setControllerName($router->getParam('controller'));
        $as->setActionName($router->getParam('action'));

        $as->initLoadedModules($this->moduleManager->getLoadedModules());

        $content = $response->getContent();
        $content = $as->setupResponseContent($content);
        $response->setContent($content);

        return $response;
    }
}