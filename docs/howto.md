# How to

This example shows how to convert "ZF2 Skeleton Application" to use `AsseticBundle`.

1. Install ZF2 skeleton application
2. Install `AsseticBundle`
3. Move resources from public/ to module/Application/assets
```bash
cd to/your/project/dir
mkdir module/Application/assets
mv public/css module/Application/assets
mv public/js module/Application/assets
mv public/images module/Application/assets
```

4. Edit the module configuration file `module/Application/config/module.config.php` add following configuration:
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

5. Update "head" tag in layout file `module/Application/view/layout/layout.phtml` 
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

6. Refresh site and have fun!
