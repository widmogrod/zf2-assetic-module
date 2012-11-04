# Introduction

AsseticBundle is module for ZF2 allowing asset managment dependent on route-name, controller-alias-name (di).
Core of module is [assetic](https://github.com/kriswallsmith/assetic) library.

*P.S.* Sorry for my english. If You wish to help me with this project or correct my english description - You are welcome :)

# Requirements

  * Zend Framework 2 (https://github.com/zendframework/zf2). Tested on *Zend Framework 2.0.0rc6*.

# Installation

## Composer installation

  1. `cd my/project/directory`
  2. Create a `composer.json` file with following content:

     ```json
     {
         "require": {
             "widmogrod/zf2-assetic-module": "dev-master"
         }
     }
     ```
  3. Run `php composer.phar install`
  4. Open ``my/project/folder/configs/application.config.php`` and add ``'AsseticBundle'`` to your ``'modules'`` parameter.

## Installation without composer:

  1. ``cd my/project/folder``
  2. ``git clone git@github.com:widmogrod/zf2-assetic-module.git module/AsseticBundle --recursive``
  3. Open ``my/project/folder/configs/application.config.php`` and add ``'AsseticBundle'`` to your ``'modules'`` parameter.

# Changes

### 2012-09-04:
  * composer support added, now is recommended way of installation
  * remove vendor directory
  * new installation process

### 2012-08-26:

  * rewrite AsseticBundle\Service to determinate how to set up template to use resources (link, script) depending on Zend\View\Renderer
  * assetic configuration namespace was change from:
    ```
    array(
        'di' => array(
            'instance' => array(
                'assetic-configuration' => array(
                    'parameters' => array(
                        'config' => array(/* configuration */)
                    )
                )
            )
        )
    );
    ```
    to:
    ```
    array('assetic_configuration' => array(/* configuration */))
    ```

# How to use _AsseticBundle_

## Simple configuration example

This example shows how to convert "ZF2 Skeleton Application" to load assets via AsseticBundle.

1. After installing skeleton application, move resources from public/ to module/Application/assets
  ```bash
  cd to/your/project/dir
  mkdir module/Application/assets
  mv public/css module/Application/assets
  mv public/js module/Application/assets
  mv public/images module/Application/assets
  ```

2. Edit the module configuration file `module/Application/config/module.config.php`, adding the configuration fragment below:
```php
<?php
return array(
    /* ... */

    'assetic_configuration' => array(
        'routes' => array(
            'home' => array(
                '@base_css',
                '@base_js',
            ),
        ),

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

                    'base_js' => array(
                        'assets' => array(
                            'js/html5.js',
                            'js/jquery-1.7.2.min.js'
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
?>
```

3. Update "head" tag in layout file `module/Application/view/layout/layout.phtml`
```html
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

4. Refresh site and have fun!

## Complex configuration example

``` php
<?php
// module.config.php
return array(
    'assetic_configuration' => array(

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
             * Defaine base URL which will prepend your resources adress.
             *
             * @example
             * <link href="http://resources.example.com/witter_bootstrap_css.css?1320257242" media="screen" rel="stylesheet" type="text/css">
             *
             * @optional
             * @default null
             */
             'baseUrl' => '/assets',

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
            * If the mixin option is true, then the listed assets will be merged with any controler / route
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

# Which configuration will be used

_AsseticBundle_ uses the following algorithm to determine the configuration to use when loading assets:

  1. Use assets from 'controller' configuration
  2. If 'controller' not exists, use assets from 'route' configuration
  3. If 'route' not exists, don't load assets


# Projects using _AsseticBundle_

  * [zf2-twitter-bootstrap-module](https://github.com/widmogrod/zf2-twitter-bootstrap-module)

# Project plan

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


# Additional staff

## Layout .phtml example

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
