2013-11-15
----------

  * Added the possibility to define assets to actions

    Example controller configuration

    ```php
    'controllers' => array(
        'ModuleName\Controller\ControllerName' => array(
            'actions' => array(
                'actionName' => array(
                    '@collection_name',
                ),
            ),
            '@base_css',
            '@base_js',
        ),
    ),
    ```

2013-09-25
----------

  * Refactored view helper and added an ability to prevent browser cache when the file is updated.
  
    ```php
    <?php
    echo $this->asset('login_ie7_head_js', array('addFileMTime' => true))
    ```

2013-09-25
----------

  * Added view helper to have an ability to include asset collections in the view script manually.
  
    Only the collection name is required.
    
    ```
    <!--[if IE 7]>
        <?php echo $this->asset('login_ie7_head_js', array('type' => 'text/javascript')) ?>
        <?php echo $this->asset('login_ie7_css', array('media' => 'screen', 'type' => 'screen', 'text/css' => 'stylesheet')) ?>
    <![endif]-->
    ```
    
    Register view helper in your module if necessary:
    
    ```php
    <?php
    public function getViewHelperConfig()
    {
        return array(
            'factories' => array(
                'asset' => function($helpers) {
                    /** @var HelperPluginManager $helpers */

                    $sl = $helpers->getServiceLocator();
                    return new Helper\Asset($sl);
                },
            ),
        );
    }
    ```

2013-07-25
----------

  * Availability to get filters from ServiceManager

    ```php
    <?php
    'twitter_bootstrap_css' => array(
         'assets' => array(
               '/path/to/bootstrap/file.less'
          ),
         'filters' => array(
               'BootstrapLessCompiler'
         ),
    );
    ```

    ```php
    <?php
    // BootstrapLessCompiler filter factory class
    public function createService(ServiceLocatorInterface $serviceLocator){
            $filter = new LessphpFilter();
            $defaultPresets = $serviceLocator->get('config');
            $defaultPresets = array_merge($defaultPresets['bootstrap_less']['default'], $defaultPresets['bootstrap_less']['application']);

            $filter->setPresets($defaultPresets);
            return $filter;
    }
    ```

2013-06-21
----------

  * Simply front page
  * Improve documentation
  * Create out of the box configuration `config/assets.config.php.dist`
  * #29 - pass more arguments to filter constructor
  * #74 - now only assets match in request are build
  * #80 - In debug mode, asset collection is move as separate file, not as single file.
  * use cache buster only when cache is enabled or have cache buster enabled

2013-06-10
----------

  * All route configurations that match the current route will now be merged. This is especially useful when used in combination with the regular expressions previously introduced. 

    ``` php
    <?php
    return array(
        'assetic_configuration' => array(
            'routes' => array(
                'admin/.*' => array(
                    '@specific_admin_js
                ),
                'admin/(dashboard|reports|etc)/.+' => array(
                    '@admin_css',
                    '@admin_js'
                )
            )
        )
    );
    ```
    
    If now route `admin/page` gets matched by ZF2 only asset `@specific_admin_js` is selected. If route `admin/reports/x` is matched by ZF2 both assets `@specific_admin_js` and `admin_css` and `admin_js` get selected.

2013-05-12
----------

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

2013-05-12
----------

  * Asset collections defined on route level can now match the current route using regular expression

2013-05-06
----------

  * Create console actions, run `php index.php` to see more informations
  * Option to disable/enable generating assets on fly `'buildOnRequest' => true` - by default is set to `true` for backward compatybility. My recomendation is to set this to false on production enviroment.
  * Cleanup, refactoring

2013-04-21
----------

  * Added cache buster strategy
  * Start tagging releases

2013-04-11
----------

  * Optional filters in debug mode

2013-04-10
----------

  * Added configurable umask

2013-04-01
----------

  * Added configurable acceptable errors #54

2012-12-26
----------

  * Update description how to merge
  * Change behavior for option "baseUrl" now is detected by ZF2 (in ServiceFactory::createService)
  * New configuration option "basePath"
  * Composer is now recommended way of installation.
  * Fix issue #36: Case insesitive module name in configuration required
  * Fix issue #30: Possible Assets may apply where they are not wanted

2012-12-25
----------

  * Wrote tests
  * Add travis-ci
  * Remove old code
  * Add AsseticBundleServiceAwareInterface

2012-09-04
----------

  * Composer support added, now is recommended way of installation
  * Remove vendor directory
  * New installation process

2012-08-26
----------

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
