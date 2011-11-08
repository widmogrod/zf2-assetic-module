<?php

namespace Assetic;

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
                    __NAMESPACE__ => __DIR__ . '/library/assetic/src/' . __NAMESPACE__,
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
        $app->events()->attach('route', array($this, 'currentRouteName'), -200);
        $app->events()->attach('dispatch', array($this, 'renderAssets'), -2000);
    }

    public function currentRouteName(\Zend\Mvc\MvcEvent $e)
    {
        /* @var $router \Zend\Mvc\Router\Http\TreeRouteStack */
        $router = $e->getRouter();
//        $this->_currentRouteName = $router->getCurrentRouteName();
    }

    public function renderAssets(\Zend\Mvc\MvcEvent $e)
    {
        $response = $e->getResponse();
        if (!$response) {
            $response = new Response();
            $e->setResponse($response);
        }

        /* $var $as \Assetic\Service */
        $as = $this->locator->get('assetic-service');
//        $as->setRouteName($this->_currentRouteName);
        $as->initLoadedModules($this->moduleManager->getLoadedModules());



        $content = $response->getContent();
        $tags = $as->generateTags();

        // @todo fix this temporary solution

        if (isset($tags['css'])) {
            $content = str_replace('<head>', '<head>'.$tags['css'], $content);
        }

        if (isset($tags['js'])) {
            $content = str_replace('</body>', $tags['js'] . '</body>', $content);
        }

        $response->setContent($content);
        return $response;
    }
}