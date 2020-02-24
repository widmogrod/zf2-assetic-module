# How to use AsseticBundle with ZF3
## Introduction
Step by step introduction, how to use `AsseticBundle` with `ZF3`

#### [Install ZF3 skeleton application](https://github.com/zendframework/ZendSkeletonApplication)
```
composer create-project laminas/skeleton-application path/to/project
```

#### Enter ZF3 directory
```
cd path/to/project
```

#### Install `AsseticBundle`
```
composer require widmogrod/zf2-assetic-module
```

#### Declare `AsseticBundle` in the `config/modules.config.php` file:
```php
return [
    'Laminas\Router',
    'Laminas\Validator',
    'AsseticBundle', // <-- put it here
    'Application',
];
```

#### Create cache and assets directory with valid permissions.
```
vendor/bin/assetic setup
```

#### Move resources from `public/` to `module/Application/assets/`
```bash
cd to/your/project/dir
mkdir module/Application/assets
mv public/css module/Application/assets
mv public/js module/Application/assets
mv public/img module/Application/assets
```

#### Edit the module configuration file `module/Application/config/module.config.php` add following configuration:

``` php
return [
    /* ... */
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
];
```

- You could also copy file from `zf2-asseitc-module` to `module/Application`
  ```
  cp vendor/widmogrod/zf2-assetic-module/configs/assets.config.php.dist module/Application/config/assets.config.php
  ```
- Update `module/Application/src/Module.php` 
  ```php
  public function getConfig()
  {
      return array_merge(
          include __DIR__ . '/../config/module.config.php',
          include __DIR__ . '/../config/assets.config.php'
      );
  }
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
    <?= $this->headTitle('ZF Skeleton Application')->setSeparator(' - ')->setAutoEscape(false) ?>

    <?= $this->headMeta() ?>
    <?= $this->headLink() ?>
    <?= $this->headScript() ?>
</head>
```

#### Build your assets
```
vendor/bin/assetic build -vvv
```

#### Start the server
```
php -S 127.0.0.1:8080 -t public/
```

Refresh site and have fun!


#### What next?
- [Configuration](https://github.com/widmogrod/zf2-assetic-module/blob/master/docs/config.md)
- [Tips & Tricks](https://github.com/widmogrod/zf2-assetic-module/blob/master/docs/tips.md)

