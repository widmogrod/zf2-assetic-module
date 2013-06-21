# Tips & Tricks

## Production vs development enviroment configuration

// todo

## Cache Busting

By default the asset's last modified time is added into to the filename before the extension.
To change this behaviour a different cache buster strategy must be injected into the service.
To prevent a cache buster url being used, add the Null cachebuster to the service

## Minimalistic layout template

``` php
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