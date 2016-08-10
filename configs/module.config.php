<?php
use Zend\Mvc\Application;

return array(
    'service_manager' => array(
        'aliases' => array(
            'AsseticConfiguration'  => 'AsseticBundle\Configuration',
            'AsseticService'        => 'AsseticBundle\Service',
            'Assetic\FilterManager' => 'AsseticBundle\FilterManager',
        ),
        'factories' => array(
            'AsseticBundle\Service'       => 'AsseticBundle\ServiceFactory',
            'Assetic\AssetWriter'         => 'AsseticBundle\WriterFactory',
            'AsseticBundle\FilterManager' => 'AsseticBundle\FilterManagerFactory',
            'Assetic\AssetManager'        => 'Zend\ServiceManager\Factory\InvokableFactory',
            'AsseticBundle\Listener'      => 'Zend\ServiceManager\Factory\InvokableFactory',
            'AsseticBundle\Cli'           => 'AsseticBundle\Cli\ApplicationFactory',
        ),
    ),

    'assetic_configuration' => array(
        'rendererToStrategy' => array(
            'Zend\View\Renderer\PhpRenderer'  => 'AsseticBundle\View\ViewHelperStrategy',
            'Zend\View\Renderer\FeedRenderer' => 'AsseticBundle\View\NoneStrategy',
            'Zend\View\Renderer\JsonRenderer' => 'AsseticBundle\View\NoneStrategy',
        ),
        'acceptableErrors' => array(
            Application::ERROR_CONTROLLER_NOT_FOUND,
            Application::ERROR_CONTROLLER_INVALID,
            Application::ERROR_ROUTER_NO_MATCH
        ),
    ),
);
