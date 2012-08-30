<?php
return [
    'service_manager' => [
        'aliases' => [
            'AsseticService'       => 'AsseticBundle\Service',
        ],
        'factories' => [
            'AsseticBundle\Service' => 'AsseticBundle\ServiceFactory',
            'Assetic\AssetManager'  => 'AsseticBundle\SimpleFactory',
            'Assetic\FilterManager' => 'AsseticBundle\SimpleFactory',
        ],
    ],

    'assetic_configuration' => [
        'debug' => false,
        'webPath' => __DIR__ . '/../../../../../../public/assets',
        'baseUrl' => '/assets',
        'strategyForRenderer' => [
            'AsseticBundle\View\ViewHelperStrategy' => 'Zend\View\Renderer\PhpRenderer'
        ],
        'routes' => [],
        'modules' => [],
    ],
];
