<?php

use Laminas\Mvc\Application;

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
            'Assetic\AssetManager'        => 'Laminas\ServiceManager\Factory\InvokableFactory',
            'AsseticBundle\Listener'      => 'Laminas\ServiceManager\Factory\InvokableFactory',
            'AsseticBundle\Cli'           => 'AsseticBundle\Cli\ApplicationFactory',
            'AsseticBundle\Configuration' => 'AsseticBundle\Factory\ConfigurationFactory',
        ],
    ],

    'assetic_configuration' => [
        'rendererToStrategy' => [
            'Laminas\View\Renderer\PhpRenderer'  => 'AsseticBundle\View\ViewHelperStrategy',
            'Laminas\View\Renderer\FeedRenderer' => 'AsseticBundle\View\NoneStrategy',
            'Laminas\View\Renderer\JsonRenderer' => 'AsseticBundle\View\NoneStrategy',
        ],
        'acceptableErrors' => [
            Application::ERROR_CONTROLLER_NOT_FOUND,
            Application::ERROR_CONTROLLER_INVALID,
            Application::ERROR_ROUTER_NO_MATCH
        ],
    ],
];
