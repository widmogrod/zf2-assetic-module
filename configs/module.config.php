<?php
return array(

    'di' => array(
        // why this not set setters?
        /**/
        'definition' => array(
            'class' => array(
                'Assetic\Service' => array(
                    'methods' => array(
                        'setAssetManager' => array(
                            'assetManager' => array(
//                                'name' => 'assetManager',
                                'type' => 'Assetic\AssetManager',
                                'required' => true
                            )
                        ),
                        'setFilterManager' => array(
                            'filterManager' => array(
//                                'name' => 'filterManager',
                                'type' => 'Assetic\FilterManager',
                                'required' => true
                            )
                        ),
                    ),
                )
            ),
        ),
        /**/
        'instance' => array(
            'alias' => array(
                'asseticexample' => 'Assetic\Controller\AsseticExampleController',
                'assetic-collection' => 'Assetic\Asset\AssetCollection',
                'assetic-asset-glob' => 'Assetic\Asset\GlobAsset',

                /**/
                'assetic-asset-manager' => 'Assetic\AssetManager',
                'assetic-filter-manager' => 'Assetic\FilterManager',
                /**/

                'assetic-service' => 'Assetic\Service',
                'assetic-configuration' => 'Assetic\Configuration'
            ),

            'assetic-service' => array(
                'parameters' => array(
                    'configuration' => 'assetic-configuration'
                ),
                /**/
                'injections' => array(
//                    'setAssetManager' =>
                    'assetic-asset-manager',
//                    'setFilterManager' =>
                    'assetic-filter-manager',
                )
                /**/
            ),

            'assetic-configuration' => array(
                'parameters' => array(
                    'config' => array(
                        'debug' => false,
                        'webPath' => __DIR__ . '/../../../public',
                        //'load_function' => false,
                        //'append_html_head' => true,

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

                        'modules' => array(
                            /**  /
                            'application' => array(
                                'root_path' => __DIR__ . '/../assets',
                                'collections' => array(
                                    'asset_app_test_css' => array(
                                        'assets' => array(
                                            '@asset_test_css'
                                        )
                                    ),
                                ),
                            ),
                            /** /
                            /** /
                            'assetic' => array(
                                'root_path' => __DIR__ . '/../assets',
                                'collections' => array(
                                    'asset_test_css' => array(
                                        'assets' => array(
                                            'css/*.css'
                                        ),
                                        'filters' => array()
                                    ),
                                    'asset_test_js' => array(
                                        'assets' => array(
                                            'js/jquery.js'
                                        ),
                                        'filters' => array()
                                    ),
                                ),
                            ),
                            /**/
                        ),
                    ),
                ),
            ),

//            'assetic-asset-manager' => array(
//                ''
//            ),

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
