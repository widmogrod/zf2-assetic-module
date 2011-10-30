<?php
return array(

    'assetic' => array(
        'load_function' => true,

        'routes' => array(
            'default' => array(
                '@jquery',
                '@base_css',
            ),
            'home' => array(
                // powinno zadbać by dodać jquery
                '@bootstrap_twitter_js',
                '@bootstrap_twitter_css',
            )
        ),
    ),

    'di' => array(
        'instance' => array(
            'alias' => array(
                'asseticexample' => 'Assetic\Controller\AsseticExampleController',
                'assetic-service' => 'Assetic\Service\Service',
                'assetic-collection' => 'Assetic\Asset\AssetCollection',
                'assetic-asset-glob' => 'Assetic\Asset\GlobAsset'
            ),

            'assetic-manager' => array(
                'parameters' => array(
                    'filter' => array(
                        'sass' => array('SassFilter', '/path/to/parser/sass'),
                        'yui_css' => array('Yui\CssCompressorFilter', '/path/to/yuicompressor.jar'),
                    ),
                    'asset'  => array(
                        'jquery' => array(),
                        'jquery-ui' => array(
                            '@jquery',
                            '/path/to/jquery-ui-*'
                        ),
                    )
                )
            ),

            'assetic-container' => array(
                'parameters' => array(
                    
                )
            ),

            'assetic-collection' => array(
                'parameters' => array(
                    'assets' => array('alias:assetic-asset-glob'),
                    'filters' => array(),
                    'sourceRoot' => ''
                )
            ),

            'assetic-asset-glob' => array(
                'parameters' => array(
                    'globs' => __DIR__ . '/../assets/css/*'
                )
            ),

            'assetic-asset-glob-param1' => array(
                'parameters' => array(
                    'assetic-asset-glob'
                )
            ),

            'Zend\View\PhpRenderer' => array(
                'parameters' => array(
                    'options'  => array(
                        'script_paths' => array(
                            'asseticexample' => __DIR__ . '/../views',
                        ),
                    ),
                ),
            ),
        ),
    ),
);
