<?php
return array(
    'service_manager'       => array(
        'aliases'   => array(
            'AsseticService'       => 'AsseticBundle\Service',
        ),
        'factories' => array(
            'AsseticBundle\Service' => 'AsseticBundle\ServiceFactory',
        ),
        'invokables' => array(
            'Assetic\AssetManager' => 'Assetic\AssetManager',
            'Assetic\FilterManager' => 'Assetic\FilterManager',
        ),
    ),

    'assetic_configuration' => array(
        'debug'              => false,
        // Relative to application root dir.
        // Path where generated assets will be moved.
        'webPath'            => 'public/assets',
        // The base URL. When null then will be auto detected by ZF2.
        'baseUrl'            => null,
        // The base path.
        // Related path to the base URL.
        // Indicate where asset are and from where will
        'basePath'           => 'assets',
        'rendererToStrategy' => array(
            'Zend\View\Renderer\PhpRenderer'  => 'AsseticBundle\View\ViewHelperStrategy',
            'Zend\View\Renderer\FeedRenderer' => 'AsseticBundle\View\NoneStrategy',
            'Zend\View\Renderer\JsonRenderer' => 'AsseticBundle\View\NoneStrategy',
        ),
        'routes'             => array(),
        'modules'            => array(),
    ),
);
