# Configuration
## Main configuration

Below are described values in main `assetic_configuration` section.


| Name               | Type       | Default          | Description |
|--------------------|------------|------------------|-------------|
| buildOnRequest     | boolean    | `true`           | Set to `true` if you're working in a development environment and you want on every request update your assets. If set to `false` assets won't be build during http request but you can do it from console `php public/index.php assetic build`
| debug              | boolean    | `false`          | If set to `true` then filters prepended with question mark `?CssMinFilter` won't be used. Great option for development enviroment.
| combine            | boolean    | `true`           | This flag is optional, by default is set to `true`. In debug mode allow you to combine all assets to one file. Setting `false` will result in loading each asset as a separate file.
| rendererToStrategy | array      | -                | Described in separate section
| acceptableErrors   | array      | -                | Described in separate section
| webPath            | string     | `public/assets`  | Here, all assets will be saved
| cachePath          | string     | `data/cache`     | Here, cache metadata will be saved
| cacheEnabled       | boolean    | `true`           | If, true cache will be used on assets using filters. This is very useful if we use filters like scss, closure,...
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

#### Collection section

This section belong to `modules` section and is composed from fallowing options:

| Name    | Type       | Required | Description |
|---------|------------|----------|-------------|
| assets  | `string[]` | yes      | List of relative paths to `root_path` pointing to asset file. Thos files must containt single type of assets i.e. separate for JavaScript, separate for css, spearate for images
| filters | `array`    | no       | Described in separate section
| options | `array`    | no       | Options are passed to `Assetic\Factory\AssetFactory::createAsset` and also are described in separate section.

##### Filters section

This section belong to `collection` section and is composed from fallowing options:

| Name   | Type    | Required | Description |
|--------|---------|----------|-------------|
| name   | `string`| yes      | Filter name must be valid class name i.e `Assetic\Filter\CssRewriteFilter`. By valid, I mean it should be visible through autoloader.
| option | `array` | no       | Some filters are require options in constructor, so by providing them here, you can specialize the filter instance.

##### Options section

This section belong to `collection` section and is composed from fallowing options:

| Name     | Type      | Required | Description |
|----------|-----------|----------|-------------|
| output   | `string`  | no       | You can give your own output file name.
| move_raw | `boolean` | no       | This option is very useful to move images, because we don't want them raw, without any modification.

### Controllers section

Making your module aware of assets is one step but second is to utilize this knowlage.
You can do it by telling `AsseticBundle` what assets should be used in what controller.
If you want to use assets for specifc route then go to next section.

Consider falowing configuration:

```
<?php
return array(
    'assetic_configuration' => array(
        'controllers' => array(
            'Your_Module_Name\Controller\Index' => array(
                '@my_css',
                '@my_js',
            ),
        ),
        
        /* some code ommited for clarity */
    ),
);
```

When you make request to `Your_Module_Name\Controller\Index` asset collections `my_css` and `my_js` will be injected to the layout.

### Routes section

Making your module aware of assets is one step but second is to utilize this knowlage.
You can do it by telling `AsseticBundle` what assets should be used for what route.

Consider falowing configuration:

```
<?php
return array(
    'assetic_configuration' => array(
        'routes' => array(
            'admin(.*)' => array(
                '@specific_admin_js
            ),
            'admin(/dashboard|/reports|/etc)' => array(
                '@admin_css',
                '@admin_js'
            )
        )
            
        /* some code ommited for clarity */
    ),
);
```

1. When you make request to **route name** `admin` only asset collection `specific_admin_js` will be injected to the layout. Its because route name `admin` is matched agains regular expresion `admin(.*)` and `admin(/dashboard|/reports|/etc)` and only first is matched.
2. When you make request to **route name** `admin/dashboard` asset collection `specific_admin_js, admin_css, admin_js` will be used. Its because route name `admin/dashboard` is matched agains regular expresion `admin(.*)` and `admin(/dashboard|/reports|/etc)` and every pattern is matched.


### Defaults section

Making your module aware of assets is one step but second is to utilize this knowlage.
You can do it by telling `AsseticBundle` what assets should be used as default assets or even as base for every request.
This option can be useful when you are building application, without sophisticated module separation.

Consider falowing configuration:

```
<?php
return array(
    'assetic_configuration' => array(
        'default' => array(
            'assets' => array(
                '@base_css',
            ),
            'options' => array(
                'mixin' => true
            ),
        ),

        /* some code ommited for clarity */
    ),
);
```

1. If we can't find matching controller or router configuration for given request then asset collection `base_css` will be injected into layout.
2. If we matched router or controller and option `mixin` is set to `true` then to mached asset will be merged `base_css`.

### RendererToStrategy

Zend Framework 2 is using different rendering strategy depending on specific for each strategy conditions. To prevent `AsseticBundle`from injecting assets during request that don't utilize layout rendering - renderer strategy was introduce.
Also,….

| Renderer | Strategy  |
|----------|-----------|
| `Zend\View\Renderer\PhpRenderer`  | `AsseticBundle\View\ViewHelperStrategy`
| `Zend\View\Renderer\FeedRenderer` | `AsseticBundle\View\NoneStrategy`
| `Zend\View\Renderer\JsonRenderer` | `AsseticBundle\View\NoneStrategy`

### AcceptableErrors

### Combining all together

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

To make your module aware of assets you should create file similar to this:

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

## Which configuration will be used?

_AsseticBundle_ uses the following algorithm to determine the configuration to use when loading assets:

  1. Use assets from 'controller' configuration
  2. If 'controller' not exists, use assets from 'route' configuration
  3. If 'route' not exists, use defaut options or don't load assets
