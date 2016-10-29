<?php

use Zend\Mvc\Application;

return [
    'service_manager' => [
        'aliases' => [
            'AsseticConfiguration'  => 'AsseticBundle\Configuration',
            'AsseticService'        => 'AsseticBundle\Service',
            'Assetic\FilterManager' => 'AsseticBundle\FilterManager',
        ],
        'factories' => [
            'AsseticBundle\Service'       => 'AsseticBundle\ServiceFactory',
            'Assetic\AssetWriter'         => 'AsseticBundle\WriterFactory',
            'AsseticBundle\FilterManager' => 'AsseticBundle\FilterManagerFactory',
            'Assetic\AssetManager'        => 'Zend\ServiceManager\Factory\InvokableFactory',
            'AsseticBundle\Listener'      => 'Zend\ServiceManager\Factory\InvokableFactory',
            'AsseticBundle\Cli'           => 'AsseticBundle\Cli\ApplicationFactory',
        ],
    ],

    'assetic_configuration' => [
        'rendererToStrategy' => [
            'Zend\View\Renderer\PhpRenderer'  => 'AsseticBundle\View\ViewHelperStrategy',
            'Zend\View\Renderer\FeedRenderer' => 'AsseticBundle\View\NoneStrategy',
            'Zend\View\Renderer\JsonRenderer' => 'AsseticBundle\View\NoneStrategy',
        ],
        'acceptableErrors' => [
            Application::ERROR_CONTROLLER_NOT_FOUND,
            Application::ERROR_CONTROLLER_INVALID,
            Application::ERROR_ROUTER_NO_MATCH
        ],
    ],
];
