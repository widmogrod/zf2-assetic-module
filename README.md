# Introduction

AsseticBundle is module for ZF2 allowing asset managment depended of route-name, controller-alias-name (di).
Core of module is [assetic](https://github.com/kriswallsmith/assetic) library.

*P.S.* Sory for my english. If You wish to help me with this project or correct my english description - You are welcome :)

# Requirements

  * Zend Framework 2 (https://github.com/zendframework/zf2)

# Installation

Simplest way:

  1. cd my/project/folder
  2. git clone git@github.com:widmogrod/zf2-datagrid-bundle.git modules/AsseticBundle --recursive
  3. open my/project/folder/configs/application.config.php and add 'AsseticBundle' to your 'modules' parameter.

# How to use _AsseticBundle_

Open and add to your module.config.php following section:

``` php
<?php
// module.config.php
return array(
    'di' => array(
        'instance' => array(

            // (...)

            // configuration namespace
            'assetic-configuration' => array(
                'parameters' => array(
                    'config' => array(
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
                         * When route will be mached then following assets will be loaded:
                         * INFO: assets with names prepended by '@' are alias for specific configuration resource.
                         */
                        'routers' => array(
                            /*
                             * when router 'default' will be used then this set of asset will be loaded
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
                        ),

                        /*
                         * In this configuration section, you can define which js, css, resources module have.
                         */
                        'modules' => array(

                            /*
                             * application moodule assets configuration
                             */
                            'application' => array(
                                # module root path for yout css and js files
                                'root_path' => __DIR__ . '/../assets',
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
                        ),
                    ),
                ),
            )

        //(...)
?>
```

# Which configuration will be used

_AsseticBundle_ resolve which configuration will be used to setup and load assets using this algoritm:

  1. use assets from 'controller' configuration
  2. if 'controller' not exists, use assets from 'route' configuration
  3. if 'route' not exists, don't load assets


# Projects using _AsseticBundle_

  * [zf2-twitter-bootstrap-module](https://github.com/widmogrod/zf2-twitter-bootstrap-module)

# Project plan

  * Done
     * setup assets by view helpers

  * Todo
     * add cache for assets
     * more examples
     * better description

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
            <?php echo $this->raw('content') ?>
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