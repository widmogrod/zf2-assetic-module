# Tips & Tricks

## Production vs development enviroment configuration

### Development

The development environment is characterized by rapid code and functionality changes, which can often lead to bugs if wrongly configured.
The following settings are meant to help you minimize these errors.

```
return array(
    'assetic_configuration' => array(
        'debug'          => true,
        'buildOnRequest' => true,
)
```

**Setting `debug` option to `true` will enable:**

- Turning off filters which have names prepended with question mark (`?`). Thanks to that you can, for example, disable the JS minify tool and have more readable code during development.
- The collection of assets won't be combined to one file. Thanks to that:
  - You can quickly find which line is causing trouble.
  - Changing one file will not cause an asset rebuild [since they aren't combined].

**Setting `buildOnRequest` option to `true` will enable:**

- Changes in assets will cause a rebuild on every browser request. No need to do it manually.
- Every asset is seen as separate file, thanks to this, change in one file will cause a rebuild of this asset only.

### Production

Production environment is characterized by stable code.
No one edits/builds code on production, right? ;)

The following settings help you achieve this task.

```
return array(
    'assetic_configuration' => array(
        'debug'          => false,
        'buildOnRequest' => false,
```

Please also consider configuring the cache busting strategy, described in the config section.

**Setting `debug` option to `false`:**

This is a pure reversal of the "true" option.

- Disabled filters will now be enabled.
- Assets will now be combined to one file.

**Setting `buildOnRequest` option to `false`:**

This is a pure reversal of the "true" option.

Assets are built manually or by the deploy script, only once. 
Thanks to this, you will relieve your web server and improve application speed.

```
php public/index.php assetic build
```

## Cache Busting

Did you change your CSS files and don't see changes in browser?
This is a common problem. 
To prevent the browser from caching any sort of assets `AssetcBundle` provides `LastModifiedStrategy`. 
This strategy adds last modified time into the filename before the extension.
Thanks to that, the browser will always receive fresh assets.

By default, cache busting is disabled.
To enable it you need to:

1. Enable cache by setting option `cacheEnabled` to `true`
2. Tell which cache buster strategy you want to use by initializing `AsseticCacheBuster` name in service manager 
```
return array(
    'service_manager' => array(
        'invokables' => array(
            'AsseticCacheBuster' => 'AsseticBundle\CacheBuster\LastModifiedStrategy',
)));
```

## Using ZfcRbac module?

Please note anyone using `zf2-assetic-module` with `ZfcRbac` you will experience this same issue on (Access Denied)[https://github.com/widmogrod/zf2-assetic-module/pull/41]. This is due to the white list of acceptable errors in assetic. You will need to allow the firewall errors in your assetic configuration to get css on your access denied pages:

```
use Zend\Mvc\Application;
use ZfcRbac\Service\Rbac;

return array(
    'assetic_configuration' => array(
        'acceptableErrors' => array(
            Application::ERROR_CONTROLLER_NOT_FOUND,
            Application::ERROR_CONTROLLER_INVALID,
            Application::ERROR_ROUTER_NO_MATCH,
            Rbac::ERROR_ROUTE_UNAUTHORIZED,
            Rbac::ERROR_CONTROLLER_UNAUTHORIZED,
        ),
);
```

## Minimalistic layout template

```
<!DOCTYPE HTML>
<html>
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
    <?= $this->plugin('InlineScript') ?>
</body>
</html>
```
