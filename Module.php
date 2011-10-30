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

    public function init(Manager $moduleManager)
    {
        $this->initAutoloader();

        $events = StaticEventManager::getInstance();
        // pre
        $events->attach('bootstrap', 'bootstrap', array($this, 'initAssetsListner'), 200);

        //$events = StaticEventManager::getInstance();
        //$events->attach('bootstrap', 'bootstrap', array($this, 'initializeView'), 100);

        #require_once __DIR__ . '/library/assetic/src/functions.php';

        $js = new AssetCollection(array(
            new GlobAsset(__DIR__ .'/assets/css/*'),
//            new FileAsset(__DIR__ .'/assets/js/test.js'),
        ));

        // the code is merged when the asset is dumped
        //var_dump($js->dump());

        $fa = new FileAsset(__DIR__ .'/assets/js/jquery.js');
        $fa->setTargetPath('jquery.js');

        $ga = new GlobAsset(__DIR__ .'/assets/css/*');
        $ga->setTargetPath('all.css');

        $am = new AssetManager();
//        $am->set('jquery', $fa);
//        $am->set('base_css', $ga);

        $css = new AssetCollection(array(
            new GlobAsset(__DIR__ .'/assets/css/*'),
//            new GlobAsset(__DIR__ .'/assets/css/*'),
        ), array(
//            new Yui\CssCompressorFilter('/Users/gabriel/Downloads/yuicompressor-2.4.6/build/yuicompressor-2.4.6.jar'),
        ));

//        $writer = new AssetWriter(__DIR__ .'/assets/all.jc');
//        $writer->writeManagerAssets($am);

        $factory = new AssetFactory(__DIR__ .'/assets/');
        $factory->setAssetManager($am);
//        $factory->setFilterManager($fm);
        $factory->setDebug(true);

        $css = $factory->createAsset(array(
//            '@jquery',         // load the asset manager's "reset" asset
            'css/*.css', // load every scss files from "/path/to/asset/directory/css/src/"
        ), array(
//            'scss',           // filter through the filter manager's "scss" filter
//            '?yui_css',       // don't use this filter in debug mode
        ), array(
            'output' => 'aaa'
        ));

        // this will echo CSS compiled by LESS and compressed by YUI
//        echo $css->dump();

        $am->set('my', $css);

        $writer = new AssetWriter(__DIR__ .'/assets');
        $writer->writeManagerAssets($am);
//        die;

//        $factory = new AssetFactory('/path/to/asset/directory/');
//        $factory->setAssetManager($am);
//        $factory->setFilterManager($fm);
//        $factory->setDebug(true);
//        $css = $factory->createAsset(array(
//            '@reset',         // load the asset manager's "reset" asset
//            'css/src/*.scss', // load every scss files from "/path/to/asset/directory/css/src/"
//        ), array(
//            'scss',           // filter through the filter manager's "scss" filter
//            '?yui_css',       // don't use this filter in debug mode
//        ));
//        echo $css->dump();

//        assetic_init();
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
        $this->_currentRouteName = $router->getCurrentRouteName();
    }

    public function renderAssets(\Zend\Mvc\MvcEvent $e)
    {
        $response = $e->getResponse();
        if (!$response) {
            $response = new Response();
            $e->setResponse($response);
        }

        /* $var $as \Assetic\Service\Service */
        $as = $this->locator->get('assetic-service');
        $as->setNamespace($this->_currentRouteName);

        $content = $response->getContent();
        $response->setContent($content);
        return $response;
    }
}