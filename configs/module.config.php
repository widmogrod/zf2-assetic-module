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
        'routes' => array(
            'application' => array(
                '@base_css',
                '@base_js',
            ),
            'home' => array(
                '@base_css',
            ),
        ),
        'modules' => array(
            'application' => array(
                'root_path' => '/Users/cel/Desktop/ZendSkeletonApplication/module/Application/assets',
                'collections' => array(
                    'base_css' => array(
                        'assets' => array(
                            'css/global.css',
                            'css/*.css',
                        ),
                    ),
                    'base_js' => array(
                        'assets' => array(
                            'http://code.jquery.com/jquery-1.5.2.min.js',
                            'js/test.js',
                        ),
                    ),
                ),
            ),
        ),
    ),
);
