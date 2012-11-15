<?php
return array(
    'service_manager'       => array(
        'aliases'   => array(
            'AsseticService'       => 'AsseticBundle\Service',
        ),
        'factories' => array(
            'AsseticBundle\Service' => 'AsseticBundle\ServiceFactory',
            'Assetic\AssetManager'  => 'AsseticBundle\SimpleFactory',
            'Assetic\FilterManager' => 'AsseticBundle\SimpleFactory',
        ),
    ),

    'assetic_configuration' => array(
        'debug'              => false,
        'webPath'            => 'public/assets', //
        'baseUrl'            => '@zfBaseUrl/assets',
        'rendererToStrategy' => array(
            'Zend\View\Renderer\PhpRenderer'  => 'AsseticBundle\View\ViewHelperStrategy',
            'Zend\View\Renderer\FeedRenderer' => 'AsseticBundle\View\NoneStrategy',
            'Zend\View\Renderer\JsonRenderer' => 'AsseticBundle\View\NoneStrategy',
        ),
        'routes'             => array(),
        'modules'            => array(),
    ),
);
