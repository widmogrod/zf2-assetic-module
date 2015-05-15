<?php
use Zend\Mvc\Application;

return [
    'controllers' => [
        'invokables' => [
            'AsseticBundle\Controller\Console' => 'AsseticBundle\Controller\ConsoleController',
        ],
        'initializers' => [
            'AsseticBundleInitializer' => 'AsseticBundle\Initializer\AsseticBundleInitializer',
        ],
    ],
    'service_manager' => [
        'aliases' => [
            'AsseticConfiguration' => 'AsseticBundle\Configuration',
            'AsseticService' => 'AsseticBundle\Service',
            'Assetic\FilterManager' => 'AsseticBundle\FilterManager',
        ],
        'factories' => [
            'AsseticBundle\Service' => 'AsseticBundle\ServiceFactory',
            'AsseticBundle\Configuration' => 'AsseticBundle\ConfigurationFactory',
            'Assetic\AssetWriter' => 'AsseticBundle\WriterFactory',
        ],
        'invokables' => [
            'Assetic\AssetManager' => 'Assetic\AssetManager',
            'AsseticBundle\FilterManager' => 'AsseticBundle\FilterManager',
            'AsseticBundle\Listener' => 'AsseticBundle\Listener',
        ],
        'initializers' => [
            'AsseticBundleInitializer' => 'AsseticBundle\Initializer\AsseticBundleInitializer',
        ],
    ],
    'view_helpers' => [
        'aliases' => [
            'asset' => 'AsseticBundle\View\Helper\Asset',
        ],
        'factories' => [
            'AsseticBundle\View\Helper\Asset' => 'AsseticBundle\View\Helper\AssetFactory'
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
