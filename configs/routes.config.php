<?php
return [
    'console' => [
        'router' => [
            'routes' => [
                'assetic-build' => [
                    'options' => [
                        'route'    => 'assetic build',
                        'defaults' => [
                            'controller' => 'AsseticBundle\Controller\Console',
                            'action'     => 'build'
                        ],
                    ],
                ],
                'assetic-setup' => [
                    'options' => [
                        'route'    => 'assetic setup',
                        'defaults' => [
                            'controller' => 'AsseticBundle\Controller\Console',
                            'action'     => 'setup'
                        ],
                    ],
                ],
            ],
        ],
    ],
];