# Introduction

AsseticBundle is module for ZF2 allowing asset managment depended of route-name, controller-alias-name (di).
Core of module is [assetic](https://github.com/kriswallsmith/assetic) library.

*P.S.* Sorry for my english. If You wish to help me with this project or correct my english description - You are welcome :)

# Requirements

  * Zend Framework 2 (https://github.com/zendframework/zf2). Tested on **release-2.0.0beta3**.

# Installation

Simplest way:

  1. cd my/project/folder
  2. git clone git@github.com:widmogrod/zf2-assetic-module.git module/AsseticBundle --recursive
  3. open my/project/folder/configs/application.config.php and add 'AsseticBundle' to your 'modules' parameter.

# How to use _AsseticBundle_

Open and add to your module.config.php following section:

``` php
<?php
// module.config.php
return array(
        // configuration namespace
        'assetic_configuration' => array(
        'debug' => false,
        'webPath' => __DIR__ . '/../../../public/assets',
        'baseUrl' => '/assets',
        'strategyForRenderer' => array(
            'AsseticBundle\View\ViewHelperStrategy' => 'Zend\View\Renderer\PhpRenderer'
        ),
        'routes' => array(
            'application' => array(
                '@base_css',
                '@base_js',
            ),
            'home' => array(
                '@base_css',
            ),
        ),
        'modules' => array(
            'application' => array(
                'root_path' => '/Users/cel/Desktop/ZendSkeletonApplication/module/Application/assets',
                'collections' => array(
                    'base_css' => array(
                        'assets' => array(
                            'css/global.css',
                            'css/*.css',
                        ),
                    ),
                    'base_js' => array(
                        'assets' => array(
                            'http://code.jquery.com/jquery-1.5.2.min.js',
                            'js/test.js',
                        ),
                    ),
                ),
            ),
        ),
    ),
?>


# Which configuration will be used

_AsseticBundle_ resolve which configuration will be used to setup and load assets using this algoritm:

  1. use assets from 'controller' configuration
  2. if 'controller' not exists, use assets from 'route' configuration
  3. if 'route' not exists, don't load assets


# Projects using _AsseticBundle_

  * [zf2-twitter-bootstrap-module](https://github.com/widmogrod/zf2-twitter-bootstrap-module)

# Project plan

  * Todo
     * more examples & better description
     * filter support
     * rewrite initViewHelpers - add event handler

  * Done
     * setup assets by view helpers
     * add cache for assets
     * add ?timestamp url query parameter (now path is appended by ?1233213123)


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
