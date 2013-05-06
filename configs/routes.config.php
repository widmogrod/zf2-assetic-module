<?php
return array(
    'console' => array(
        'router' => array(
            'routes' => array(
                'assetic-build' => array(
                    'options' => array(
                        'route'    => 'assetic build',
                        'defaults' => array(
                            'controller' => 'AsseticBundle\Controller\Console',
                            'action'     => 'build'
                        ),
                    ),
                ),
                'assetic-setup' => array(
                    'options' => array(
                        'route'    => 'assetic setup',
                        'defaults' => array(
                            'controller' => 'AsseticBundle\Controller\Console',
                            'action'     => 'setup'
                        ),
                    ),
                ),
            ),
        ),
    ),
);