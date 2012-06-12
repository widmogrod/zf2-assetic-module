<?php
return array(

    'service_manager' => array(
        'aliases' => array(
            'AsseticService'       => 'AsseticBundle\Service',
        ),
        'factories' => array(
            'AsseticBundle\Service' => 'AsseticBundle\ServiceFactory',
            'Assetic\AssetManager'  => 'AsseticBundle\SimpleFactory',
            'Assetic\FilterManager' => 'AsseticBundle\SimpleFactory',
        ),
    ),

    'assetic_configuration' => array(
        'debug' => false,
        'webPath' => __DIR__ . '/../../../public/assets',
        'baseUrl' => '/assets',
        'strategyForRenderer' => array(
            'AsseticBundle\View\ViewHelperStrategy' => 'Zend\View\Renderer\PhpRenderer'
        ),
        'routes' => array(),
        'modules' => array(),
    ),
);
