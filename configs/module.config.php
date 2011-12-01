<?php
return array(
    'di' => array(

        'definition' => array(
            'class' => array(
                'AsseticBundle\Service' => array(
                    'methods' => array(
                        'setAssetManager' => array(
                            'assetManager' => array(
                                'type' => 'Assetic\AssetManager',
                                'required' => true
                            )
                        ),
                        'setFilterManager' => array(
                            'filterManager' => array(
                                'type' => 'Assetic\FilterManager',
                                'required' => true
                            )
                        ),
                    ),
                )
            ),
        ),

        'instance' => array(
            'alias' => array(
                'assetic-asset-manager'  => 'Assetic\AssetManager',
                'assetic-filter-manager' => 'Assetic\FilterManager',

                'assetic'                => 'AsseticBundle\Service',
                'assetic-configuration'  => 'AsseticBundle\Configuration'
            ),

            'assetic' => array(
                'parameters' => array(
                    'configuration' => 'assetic-configuration'
                ),
                'injections' => array(
                    'assetic-asset-manager',
                    'assetic-filter-manager',
                )
            ),

            'assetic-configuration' => array(
                'parameters' => array(
                    'config' => array(
                        'debug' => false,
                        'webPath' => __DIR__ . '/../../../public',
                        //'load_function' => false,
                        //'append_html_head' => true,

                        /** /
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
                        /**/

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
        ),
    ),
);
