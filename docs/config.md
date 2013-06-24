# Configuration
## Main configuration

Below are described values in main `assetic_configuration` section.


| Name               | Type       | Default          | Description |
|--------------------|------------|------------------|-------------|
| buildOnRequest     | boolean    | `true`           | Set to `true` if you're working in a development environment and you want on every request update your assets. If set to `false` assets won't be build during http request but you can do it from console `php public/index.php assetic build`
| debug              | boolean    | `false`          | If set to `true` then filters prepended with question mark `?CssMinFilter` won't be used. Great option for development enviroment.
| rendererToStrategy | array      | -                | Described in separate section
| acceptableErrors   | array      | -                | Described in separate section
| webPath            | string     | `public/assets`  | Here, all assets will be saved
| cachePath          | string     | `data/cache`     | Here, cache metadata will be saved
| cacheEnabled       | boolean    | `true`           | If, true cache will be used on assets using filters. This is wery useful if we use filters like scss, closure,...
| umask              | integer    | `null`           | Yes, is regular `umask` apply on generated assets
| baseUrl            | string     | `null`           | Define base URL which will prepend your resources. If `null`, then this value will be detected by ZF2
| basePath           | string     | `assets`         | Indicate where assets are and from where will be loaded. In example where `$baseUrl = 'http://example.com/'` `$basePath = 'assets'` `$assetPath = '/main.css'` view strategy will build such resource address `<link href="$baseUrl . $basePath . $assetPath"/>`
| controllers        | array      | -                | Described in separate section
| routes             | array      | -                | Described in separate section
| default            | array      | -                | Described in separate section
| modules            | array      | -                | Described in separate section

## Specific configuration
### Modules section

In this section you should define what assets you have and what filters should be apply on them during build phase.

Following table describes module configuration.

| Name        | Type      | Description |
|-------------|-----------|-------------|
| root_path   | `string`  | Must be absolute path to module directory containing its assets
| collections | `array`   | Collections are named groups of assets. Each collection must containt simgle type of assets i.e. separate for JavaScript, separate for css, spearate for images

### Collection section

This section belong to module section and is composed from fallowing options.

| Name    | Type       | Required | Description |
|---------|------------|----------|-------------|
| assets  | `string[]` | yes      |
| filters | `array`    | no       |
| options | `array`    | no       |


Consider for example falowing module structure:

```
├── Module.php
├── configs
│   ├── module.config.php
├── assets
│   ├── css
│   │   └── global.css
│   └── js
│       ├── jquery.js
│       └── test.js
```

To make your module aware you should create file similar to this:

```
<?php
// configs/assets.config.php
return array(
    'assetic_configuration' => array(
        'modules' => array(
            'Your_Module_Name' => array(
                'root_path' => __DIR__ . '/../assets',

                'collections' => array(
                    'my_css' => array(
                        'assets' => array(
                            // Relative to 'root_path'
                            'css/global.css',
                        ),
                        'filters' => array(
                            '?CssRewriteFilter' => array(
                                'name' => 'Assetic\Filter\CssRewriteFilter'
                            ),
                            '?CssMinFilter' => array(
                                'name' => 'Assetic\Filter\CssMinFilter'
                            ),
                        ),
                    ),
                    'my_js' => array(
                        'assets' => array(
                             // Relative to 'root_path'
                            'js/jquery.js',
                            'js/test.js',
                        ),
                        'filters' => array(
                            '?JSMinFilter' => array(
                                'name' => 'Assetic\Filter\JSMinFilter'
                            ),
                        ),
                    ),
                ),
            ),
        ),
    ),
);

```


### Controllers section
### Routes section
### Defaults section
### RendererToStrategy
### AcceptableErrors

## Which configuration will be used?

_AsseticBundle_ uses the following algorithm to determine the configuration to use when loading assets:

  1. Use assets from 'controller' configuration
  2. If 'controller' not exists, use assets from 'route' configuration
  3. If 'route' not exists, use defaut options or don't load assets