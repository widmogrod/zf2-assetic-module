# AsseticBundle v1.3.4 [![Build Status](https://travis-ci.org/widmogrod/zf2-assetic-module.png?branch=master)](https://travis-ci.org/widmogrod/zf2-assetic-module) [![](http://stillmaintained.com/widmogrod/zf2-assetic-module.png)](http://stillmaintained.com/widmogrod/zf2-assetic-module)

Assets managment per module made easy.

  * **Optimize your assets**. Minify your css, js; Compile scss, and more...
  * **Adapts To Your Needs**. Using custom template engine and want to use power of this module, just implement `AsseticBundle\View\StrategyInterface`
  * **Well tested**. Besides unit test this solution is also ready for the production use.
  * **Great fundations**. Based on [Assetic](https://github.com/kriswallsmith/assetic) and [ZF2](https://github.com/zendframework/zf2)
  * **Excellent community**. Everything is thanks to great support from GitHub & PHP community!
  * **Every change is tracked**. Want to know whats new? Take a look at [CHANGELOG.md](https://github.com/widmogrod/zf2-assetic-module/blob/master/CHANGELOG.md)
  * **Listen to your ideas**. Have a great idea? Bring your tested pull request or open a new issue.


## Installation

1. Install package by composer. Don't know how? [Take a look here](http://getcomposer.org/doc/00-intro.md#introduction)
``` json
{"require": {
    "widmogrod/zf2-assetic-module": "1.*"
}}
```

2. Create cache and assets directory with valid permissions.
```
php public/index.php assetic setup
```

3. Setup your asset configuration.
```
cp vendor/widmogrod/zf2-assetic-module/configs/assets.config.php.dist modules/My_Module/configs/assets.config.php
```
and read [how to start](https://github.com/widmogrod/zf2-assetic-module/blob/master/docs/howto.md) guide.

## Documentation

  * [How to start?](https://github.com/widmogrod/zf2-assetic-module/blob/master/docs/howto.md)
  * [Configuration](https://github.com/widmogrod/zf2-assetic-module/blob/master/docs/config.md)
  * [Tips & Tricks](https://github.com/widmogrod/zf2-assetic-module/blob/master/docs/tips.md)

[![Build Status](https://travis-ci.org/widmogrod/zf2-assetic-module.png?branch=devel)](https://travis-ci.org/widmogrod/zf2-assetic-module)  on branch `devel`
