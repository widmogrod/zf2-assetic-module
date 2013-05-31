#### 2013-05-17
  * Added an ability to match multiple route names with priority sort. Example:

``` php
<?php
return array(
    'assetic_configuration' => array(
        'routes' => array(
            // make sure base assets are on the topmost position
            'routes' => array(
                '(?!admin).*' => array(
                    -100 => '@base_css',
                    -90  => '@base_js',
                )
            ),
        )
    )
);
```

#### 2013-05-12
  * Added possibility to specify `regex` in route name. example:

``` php
<?php
return array(
    'assetic_configuration' => array(
        'routes' => array(
            // in the name of route there can be any regex.
            // it will be automatically prepended by '^' and appended by '$'
            // during matching phase
            'admin/(dashboard|reports|etc)/.+' => array(
                '@admin_css',
                '@admin_js'
            )
        )
    )
);
```

#### 2013-05-06
  * Create console actions, run `php index.php` to see more informations
  * Option to disable/enable generating assets on fly `'buildOnRequest' => true` - by default is set to `true` for backward compatybility. My recomendation is to set this to false on production enviroment.
  * Cleanup, refactoring

#### 2013-04-21
  * Added cache buster strategy
  * Start tagging releases

#### 2013-04-11
  * Optional filters in debug mode

#### 2013-04-10
  * Added configurable umask

#### 2013-04-01
  * Added configurable acceptable errors #54

#### 2012-12-26:
  * Update description how to merge
  * Change behavior for option "baseUrl" now is detected by ZF2 (in ServiceFactory::createService)
  * New configuration option "basePath"
  * Composer is now recommended way of installation.
  * Fix issue #36: Case insesitive module name in configuration required
  * Fix issue #30: Possible Assets may apply where they are not wanted

#### 2012-12-25:
  * Wrote tests
  * Add travis-ci
  * Remove old code
  * Add AsseticBundleServiceAwareInterface

#### 2012-09-04:
  * Composer support added, now is recommended way of installation
  * Remove vendor directory
  * New installation process

#### 2012-08-26:

  * Rewrite AsseticBundle\Service to determinate how to set up template to use resources (link, script) depending on Zend\View\Renderer
  * Assetic configuration namespace was change from:

``` php
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

``` php
array('assetic_configuration' => array(/* configuration */));
```
