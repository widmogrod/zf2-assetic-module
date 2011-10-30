<?php

namespace Assetic\Controller;

use Zend\Mvc\Controller\ActionController;
use Zend\Mvc\LocatorAware;
use Zend\Di\Di;

class AsseticExampleController extends ActionController implements LocatorAware
{
    public function indexAction()
    {

        $locator = $this->getLocator();
        /* @var $ac Assetic\Asset\AssetCollection */
//        $as = $locator->get('assetic-service');
//        $as->getFilterManager();
//        $as->getAssetManager();
//        $as->
        //var_dump($ac);
        //echo $ac->dump();
        
        //var_dump(__DIR__ . '/../../../assets/css/test.css');

//        $di = new Di();
//        $di->get('Assetic\Asset\AssetCollection', array(
//            'assets' => array(__DIR__ . '/../../../../assets/css/test.css'),
//            'filters' => array(),
//            'sourceRoot' => ''
//        ));

//        $di->getInstanceManager()->setParameters('A', array(
//            'username' => 'MyUsernameValue',
//            'password' => 'MyHardToGuessPassword%$#'
//        );

        return array();
    }
}
