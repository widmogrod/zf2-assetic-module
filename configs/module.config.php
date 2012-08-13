<?php
return array(

    'service_manager' => array(
        'aliases' => array(
            'AsseticService'       => 'AsseticBundle\Service',
        ),
        'factories' => array(
            'AsseticBundle\Service' => 'AsseticBundle\ServiceFactory',
            'Assetic\AssetManager'  => 'AsseticBundle\SimpleFactory',
            'Assetic\FilterManager' => 'AsseticBundle\SimpleFactory',
        ),
    ),
);    
