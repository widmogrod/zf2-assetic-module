# How to use AsseticBundle with ZF-Expressive
## Introduction
Step by step introduction, how to use `AsseticBundle` with `ZF-Expressive`

#### [Install ZF-Expressive skeleton application](https://github.com/zendframework/zend-expressive-skeleton)
```
composer create-project zendframework/zend-expressive-skeleton <project-path>
```

#### Enter ZF-Expressive directory
```
cd path/to/project
```

#### Install `AsseticBundle`
```
composer require widmogrod/zf2-assetic-module
```

#### Register module in your `config/config.php` with `AsseticBundle\ConfigProvider::class`
```php
$aggregator = new ConfigAggregator([
    \Zend\Cache\ConfigProvider::class,
    \Zend\Form\ConfigProvider::class,
    //...
    \AsseticBundle\ConfigProvider::class,
    //...
], $cacheConfig['config_cache_path']);
```

#### Add *AsseticMiddleware* to list in `config/pipeline.php` before `$app->pipeDispatchMiddleware() with $app->pipe(\AsseticBundle\AsseticMiddleware::class);` 

#### Create cache and assets directory with valid permissions.
```
vendor/bin/assetic setup
```

#### Move resources from `public/` to `src/App/templates/assets/`
```bash
cd to/your/project/dir
mkdir src/App/templates/assets
mv public/css src/App/templates/assets
mv public/js src/App/templates/assets
mv public/img src/App/templates/assets
```

#### Edit the module configuration file `src/App/src/ConfigProvider.php`
Add following configuration to `\App\ConfigProvider::__invoke` method or move this config to `src/App/config/module.config.php`:
``` php
public function __invoke()
{
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
                    'root_path' => __DIR__ . '/templates/assets',
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
}
```

- You could also copy file from `zf2-asseitc-module` to `module/App`
  ```
  cp vendor/widmogrod/zf2-assetic-module/configs/assets.config.php.dist module/App/config/assets.config.php
  ```
- Update `module/App/src/ConfigProvicer.php` 
  ```php
class ConfigProvider
{
    public function __invoke()
    {
        return array_merge(
            include __DIR__ . '/config/module.config.php',
            include __DIR__ . '/config/assets.config.php'
        );
    }
}
  ```

#### Enable development mode
```composer development-enable```

Check if your `config/development.config.php` file has bellow options set to `false` for development mode.
```php
return [
    //...
    'debug' => true,
    ConfigAggregator::ENABLE_CACHE => false,
];

```

#### Update "head" tag in layout file `src/App/templates/layout/default.phtml` 
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

#### Extended usage
You can use standard MVC way in assetic configuration if declare module with next `routes`config:
```
return [
    'routes' => [
        [
            'name' => 'default/action',
            'path' => '/{controller:[a-z-]{3,}}[/[{action:[a-z-]{3,}}[/[{id:\d+}]]]]',
            'middleware' => YourMiddleware::class,
            'allowed_methods' => ['GET', 'POST', 'PUT', 'DELETE'],
        ],
    ],
    //...	
]
```
After that *AsseticBundle* automatically determine `controller` and `action` value and you can create config like this:
```
return [
    'controllers' => [
        'test' => [
            '@test_css',
            '@test_js',
        ],
        'other-test' => [
            '@other_test_js',
        ],
    ],
	//...
];
```

#### What next?
- [Configuration](https://github.com/widmogrod/zf2-assetic-module/blob/master/docs/config.md)
- [Tips & Tricks](https://github.com/widmogrod/zf2-assetic-module/blob/master/docs/tips.md)

