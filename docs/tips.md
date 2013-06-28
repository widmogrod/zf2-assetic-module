# Tips & Tricks

## Production vs development enviroment configuration

### Development

Development environment is characterized by rapid code and functionality changes, which leads to bugs and most important demand to remove those bugs.

Fallowing settings help you with this task.

```
return array(
    'assetic_configuration' => array(
        'debug'          => true,
        'buildOnRequest' => true,
)
```

**Setting `debug` option to `true` will enable:**

- Turn off filters which have names prepended with question mark (`?`). Thanks to that, you can disable i.e. JS minify tool and have more readable code during development.
- The collection of asset won't be combined to one file. Thanks to that:
  - You can quickly find line which causing trouble.
  - Changing one file will cause to update only this one file.

**Setting `buildOnRequest` option to `true` will enable:**

- Changes in assets will cause build of this asset on every browser request. No need to do it manually.
- Every asset is seen as separate file, thanks to this, change in one file will cause update only of this file.

### Production

Production environment is characterized by stable code.
No one edit/build code on production, aren't you? ;)

Fallowing settings help you with this task.

```
return array(
    'assetic_configuration' => array(
        'debug'          => false,
        'buildOnRequest' => false,
```

Please consider to configure also the cache busting strategy, described in separate section.

**Setting `debug` option to `false`:**

This is pure reverse of true option.

- Disabled filters now will be enable.
- Asset now will be combined to one file.

**Setting `buildOnRequest` option to `false`:**

This is pure reverse of true option.

Now to build assets you need to do it manually or by the deploy script. 
Thanks to this, you will relieve your web server and improve application speed.

```
php public/index.php assetic build
```

## Cache Busting

You changed your CSS files and you don't see changes in browser?
This is common problem. 
To prevent the browser from caching any sort of assets `AssetcBundle` provides `LastModifiedStrategy`. 
This strategy adds last modified time into to the filename before the extension.
Thanks to that, the browser will always recieve fresh assets.

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