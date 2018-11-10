<?php

namespace AsseticBundle;

use Zend\Mvc\Application;
use Zend\ServiceManager\Factory\InvokableFactory;

return [
    'service_manager' => [
        'aliases' => [
            'AsseticConfiguration'  => 'AsseticBundle\Configuration',
            'AsseticService'        => 'AsseticBundle\Service',
            'Assetic\FilterManager' => 'AsseticBundle\FilterManager',
        ],
        'factories' => [
            'AsseticBundle\Service'       => ServiceFactory::class,
            'Assetic\AssetWriter'         => WriterFactory::class,
            'AsseticBundle\FilterManager' => FilterManagerFactory::class,
            'Assetic\AssetManager'        => InvokableFactory::class,
            'AsseticBundle\Listener'      => InvokableFactory::class,
            'AsseticBundle\Cli'           => Cli\ApplicationFactory::class,
            'AsseticBundle\Configuration' => Factory\ConfigurationFactory::class,
            'AsseticBundle\AsseticMiddleware' => Factory\MiddlewareFactory::class,
        ],
    ],

    'assetic_configuration' => [
        'rendererToStrategy' => [
            'Zend\View\Renderer\PhpRenderer' => 'AsseticBundle\View\ViewHelperStrategy',
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
