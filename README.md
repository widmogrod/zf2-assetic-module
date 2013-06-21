# AsseticBundle v1.3.0 [![Build Status](https://travis-ci.org/widmogrod/zf2-assetic-module.png?branch=master)](https://travis-ci.org/widmogrod/zf2-assetic-module)
## Introduction
Assets managment per module made easy.

## Key features

  * `Optimaze your assets.` Minify your css, js; Compile scss, and more.
  * `Adapt to your need.` Using custom template engine? Impleent your own `AsseticBundle\View\StrategyInterface`
  * `Well tested`. Besides unit test this solution is production ready.
  * `Bring your idea`. Hava a great idea? Brig your tested pull request!
  * `Every change tracked`. Want knew whats new? Take a look at [CHANGELOG.md](https://github.com/widmogrod/zf2-assetic-module/blob/master/CHANGELOG.md)
  * `Exellent community`. Everything is thanks to great support from Github.com & PHP community! Thank you.


## Installation
### Composer

``` json
{
    "require": {
        "widmogrod/zf2-assetic-module": "1.*"
    }
}
```

Don't know how? [Read this introduction to composer](http://getcomposer.org/doc/00-intro.md#introduction)

## Documentation

  * [How to use](https://github.com/widmogrod/zf2-assetic-module/blob/master/CHANGELOG.md)
  * [Configuration](https://github.com/widmogrod/zf2-assetic-module/blob/master/CHANGELOG.md)
  * [Tips & Tricks](https://github.com/widmogrod/zf2-assetic-module/blob/master/CHANGELOG.md)

## How to use _AsseticBundle_
### ZF2 Skeleton Application - migration to _AsseticBundle_

This example shows how to convert "ZF2 Skeleton Application" to load assets via _AsseticBundle_.

1. After installing skeleton application, move resources from public/ to module/Application/assets
  ```bash
  cd to/your/project/dir
  mkdir module/Application/assets
  mv public/css module/Application/assets
  mv public/js module/Application/assets
  mv public/images module/Application/assets
  mkdir public/assets
  chmod 777 public/assets
  ```

2. Edit the module configuration file `module/Application/config/module.config.php`, adding the configuration fragment below:
```php
<?php
return array(
    /* ... */

    'assetic_configuration' => array(
        'routes' => array(
            'home' => array(
                // Is disabled because 'default' option key will mix with this configuration section
                // and provide @base_css assets.
                // '@base_css',
                '@base_js',
            ),
        ),

        'default' => array(
            'assets' => array(
                '@base_css',
            ),
            'options' => array(
                'mixin' => true
            ),
        ),

        'modules' => array(
            /*
             * Application module - assets configuration
             */
            'application' => array(

                # module root path for your css and js files
                'root_path' => __DIR__ . '/../assets',

                # collection od assets
                'collections' => array(

                    'base_css' => array(
                        'assets' => array(
                            'css/bootstrap-responsive.min.css',
                            'css/style.css',
                            'css/bootstrap.min.css'
                        ),
                        'filters' => array(
                            'CssRewriteFilter' => array(
                                'name' => 'Assetic\Filter\CssRewriteFilter'
                            )
                        ),
                        'options' => array(),
                    ),

                    'lib_css' => array(
                        'assets' => array(
                            'css/lib.css'
                        ),
                        'filters' => array(
                            '?CssRewriteFilter' => array( // filter is not active in debug mode
                                'name' => 'Assetic\Filter\CssRewriteFilter'
                            )
                        )
                    ),

                    'base_js' => array(
                        'assets' => array(
                            'js/html5.js',
                            'js/jquery.min.js',
                            'js/bootstrap.min.js',
                        )
                    ),

                    'base_images' => array(
                        'assets' => array(
                            'images/*.png',
                            'images/*.ico',
                        ),
                        'options' => array(
                            'move_raw' => true,
                        )
                    ),
                ),
            ),
        )
    )
);
```

3. Update "head" tag in layout file `module/Application/view/layout/layout.phtml`
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

4. run `php index.php assetic setup` - this will create the directory structure
5. run `php index.php assetic build` - this will build all assets
6. Refresh site and have fun!

### Complex configuration example

``` php
<?php
// module.config.php
return array(
    'assetic_configuration' => array(
            /**
             * Set to true if you're working in a development environment and you want
             * every asset to be moved to public directory after some changes.
             * Set to false on production environment - to boost your application.
             * Default true - for backward compatibility.
             */
            'buildOnRequest'     => true,

            /**
             * Map how given view renderer instance will be interpreted by AsseticBundle.
             * Those are default options.
             */
            'rendererToStrategy' => array(
                'Zend\View\Renderer\PhpRenderer'  => 'AsseticBundle\View\ViewHelperStrategy',
                'Zend\View\Renderer\FeedRenderer' => 'AsseticBundle\View\NoneStrategy',
                'Zend\View\Renderer\JsonRenderer' => 'AsseticBundle\View\NoneStrategy',
            ),

            /**
              * Module is not enabled if an MvcEvent::EVENT_DISPATCH_ERROR event occurs.
              * However, we may still want our assets for pages with dispatch errors.
              * So, we can build up a whitelist of errors for the module to be enabled for.
              */
            'acceptableErrors' => array(
                //defaults
                \Zend\Mvc\Application::ERROR_CONTROLLER_NOT_FOUND,
                \Zend\Mvc\Application::ERROR_CONTROLLER_INVALID,
                \Zend\Mvc\Application::ERROR_ROUTER_NO_MATCH,

                //allow assets when authorisation fails when using the BjyAuthorize module
                \BjyAuthorize\Guard\Route::ERROR,
            ),

            /**
             * Define location, where assets should be move.
             * This is default option. You should create this directory by hand.
             */
            'webPath' => __DIR__ . '/../../../public/assets',

            /*
             * Enable cache
             */
            'cacheEnabled' => true,

            /*
             * Cache dir
             */
            'cachePath' => __DIR__ . '/../../../data/cache',

            /*
             * Debug on (used via \Assetic\Factory\AssetFactory::setDebug)
             *
             * @optional
             */
            'debug' => false,

            /*
             * set Umask
             *
             * @optional
             */
            'umask' => null,

            /*
             * Define base URL which will prepend your resources address
             *
             * @example
             * <link href="http://resources.example.com/twitter_bootstrap_css.css?1320257242" media="screen" rel="stylesheet" type="text/css">
             *
             * @optional
             * @default autodetect by ZF2
             */
            'baseUrl' => null,

            /*
             * When controller name will be found in this section then fallowing assets will be loaded:
             * INFO: assets with names prepended by '@' are alias for specific configuration resource.
             */
            'controllers' => array(
                // when 'error' controller will be loaded then
                'error' => array(
                    '@base_css',
                    '@error_css',
                ),
            ),

            /*
             * If the following routes are matched then the corresponding assets will be loaded:
             * INFO: assets with names prepended by '@' are alias for specific configuration resource.
             */
            'routes' => array(
                /*
                 * when router 'default' will be used then this set of assets will be loaded
                 */
                'default' => array(
                    '@base_css',
                    '@base_js',
                ),
                
                /*
                 * These assets will only be loaded for routes starting with admin
                 */
                'admin/.* => array(
                    '@admin_assets'
                ),

                /*
                 * when router 'home' will be used then this set of asset will be loaded
                 */
                'home' => array(
                    '@base_css',
                    '@home_css',
                    '@base_js',
                ),

                /*
                 * when router 'quizapp' will be used then this set of asset will be loaded
                 */
                'quizapp' => array(
                    '@quiz_admin_js',
                ),
            ),

            /*
             * The default assets to load.
             * If the "mixin" option is true, then the listed assets will be merged with any controller / route
             * specific assets. If it is false, the default assets will only be used when no routes or controllers
             * match
             */
            'default' => array(
                'assets' => array(
                    '@base_css',
                    '@base_js',
                ),

                'options' => array(
                    'mixin' => true,
                ),
            ),

            /*
             * In this configuration section, you can define which js, and css resources the module has.
             */
            'modules' => array(

                /*
                 * Application moodule - assets configuration
                 */
                'application' => array(

                    # module root path for yout css and js files
                    'root_path' => __DIR__ . '/../assets',

                    # collection od assets
                    'collections' => array(

                        'base_css' => array(
                            'assets' => array(
                                # relative to 'root_path'
                                'css/my/reset.css',
                                'css/*.css'
                            ),
                            'filters' => array(),
                            'options' => array(),
                        ),

                        'base_js' => array(
                            'assets' => array(
                                'http://code.jquery.com/jquery-1.5.2.min.js', // global
                                'js/setup.js' // relative to 'root_path'
                            )
                        ),
                        
                        'admin_css' => array(
                            'assets' => array(
                                'css/admin.css'
                            )
                        )
                    ),
                ),

                /*
                 * Quiz module - example configuration
                 */
                'quiz' => array(

                    'root_path' => __DIR__ . '/../../modules/Quiz/assets',

                    'collections' => array(

                        /*
                         * If You want to move not only CSS files, but also images relative to CSS resource
                         * You must set option flag 'move_raw' to true.
                         */
                        'quiz_app_images' => array(
                            'assets' => array(
                                'images/*.png',
                                'images/*.gif',
                            ),
                            'options' => array(
                                'move_raw' => true,
                            )
                        ),

                        'quiz_admin_js' => array(
                            'assets' => array(
                                'js/jquery.min.js',
                                'http://html5shim.googlecode.com/svn/trunk/html5.js',
                                '@twitter_bootstrap_scrollspy_js',
                                '@twitter_bootstrap_modal_js',
                                '@twitter_bootstrap_dropdown_js',
                                'js/admin.js',
                            ),

                            /*
                             * If Your assets collection is using a @reference its required to define
                             * a output filename for this collection.
                             *
                             * Without it, \AsseticBundle\ViewHelperSetup can't determinate
                             * what kind of content is't is: ie. *.js or *.css; and how to setup view helpers.
                             */
                            'options' => array(
                                'output' => 'quiz_admin.js'
                            )
                        )
                    ),
                ),
            ),
        ),

        //(...)
        )))
?>
```

## Which configuration will be used

_AsseticBundle_ uses the following algorithm to determine the configuration to use when loading assets:

  1. Use assets from 'controller' configuration
  2. If 'controller' not exists, use assets from 'route' configuration
  3. If 'route' not exists, use defaut options or don't load assets

## Cache Busting
By default the asset's last modified time is added into to the filename before the extension.
To change this behaviour a different cache buster strategy must be injected into the service.
To prevent a cache buster url being used, add the Null cachebuster to the service

## Projects using _AsseticBundle_

  * [zf2-twitter-bootstrap-module](https://github.com/widmogrod/zf2-twitter-bootstrap-module)

## Project plan

  * Todo
     * more examples & better description
     * create fork of ZendSkeletonApplication using _AsseticBundle_

  * Done
     * add composer installation
     * setup assets by view helpers
     * add cache for assets
     * add ?timestamp url query parameter (now path is appended by ?1233213123)
     * rewrite initViewHelpers
     * filter support


### Additional staff
#### Layout .phtml example

``` php
<?php echo $this->plugin('doctype')->setDoctype(\Zend\View\Helper\Doctype::HTML5); ?>
<html lang="en">
<head>
    <meta charset="utf-8">
    <?php
        echo $this->plugin('HeadTitle');
        echo $this->plugin('HeadMeta');
        echo $this->plugin('HeadLink');
        echo $this->plugin('HeadStyle');
        echo $this->plugin('HeadScript');
    ?>
</head>
<body>
<div id="container">
    <div id="header">

    </div>
    <div id="wrapper">
        <div id="main">
            <?php echo $this->content ?>
        </div>
    </div>
    <div id="footer">

    </div>
</div>
<?php
    echo $this->plugin('InlineScript');
?>
</body>
</html>
```

## Stability
[![Build Status](https://travis-ci.org/widmogrod/zf2-assetic-module.png?branch=master)](https://travis-ci.org/widmogrod/zf2-assetic-module)  on branch master
[![Build Status](https://travis-ci.org/widmogrod/zf2-assetic-module.png?branch=devel)](https://travis-ci.org/widmogrod/zf2-assetic-module)  on branch devel
