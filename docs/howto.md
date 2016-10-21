# How to

This example shows how to convert "ZF2 Skeleton Application" to use `AsseticBundle`.

#### [Install ZF2 skeleton application](https://github.com/zendframework/ZendSkeletonApplication)
```
composer create-project -sdev zendframework/skeleton-application path/to/install
```
#### Install `AsseticBundle`
```
composer require widmogrod/zf2-assetic-module
```
#### Move resources from public/ to module/Application/assets
```bash
cd to/your/project/dir
mkdir module/Application/assets
mv public/css module/Application/assets
mv public/js module/Application/assets
mv public/img module/Application/assets
```

#### Edit the module configuration file `module/Application/config/module.config.php` add following configuration:

``` php
return array(
    'assetic_configuration' => [
        'debug' => true,
        'buildOnRequest' => true,

        'webPath' => __DIR__ . '/../../../public/assets',
        'basePath' => 'assets',

        'routes' => [
            'home' => [
                '@base_js',
                '@base_css',
            ],
        ],

        'modules' => [
            'application' => [
                'root_path' => __DIR__ . '/../assets',
                'collections' => [
                    'base_css' => [
                        'assets' => [
                            'css/style.css',
                            'css/bootstrap.min.css'
                        ],
                        'filters' => [
                            'CssRewriteFilter' => [
                                'name' => 'Assetic\Filter\CssRewriteFilter'
                            ]
                        ],
                    ],

                    'base_js' => [
                        'assets' => [
                            'js/jquery-3.1.0.min.js',
                            'js/bootstrap.min.js',
                        ]
                    ],

                    'base_images' => [
                        'assets' => [
                            'img/*.png',
                            'img/*.ico',
                        ],
                        'options' => [
                            'move_raw' => true,
                        ]
                    ],
                ],
            ],
        ],
    ],
);
```

#### Check if your `application.config.php` file has bellow options set to `false` for development mode.
```php
return [
    /* (...) */
    'module_listener_options' => [
        'config_cache_enabled' => false,
        'module_map_cache_enabled' => false,
    ],
];
```

#### Update "head" tag in layout file `module/Application/view/layout/layout.phtml` 
```
<head>
    <meta charset="utf-8">
    <?php echo $this->headTitle('ZF2 '. $this->translate('Skeleton Application'))->setSeparator(' - ')->setAutoEscape(false) ?>
    <?php echo $this->headMeta()->appendName('viewport', 'width=device-width, initial-scale=1.0') ?>
    <?php
        echo $this->plugin('HeadLink');
        echo $this->plugin('HeadStyle');
        echo $this->plugin('HeadScript');
    ?>
</head>
```

#### Build your assets
```
vendor/bin/assetic build -vvv
```

#### Refresh site and have fun!
