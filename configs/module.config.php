<?php
return array(
    'di' => array(
        'instance' => array(
            'alias' => array(
                'asseticexample' => 'Assetic\Controller\AsseticExampleController',
            ),
            'Zend\View\PhpRenderer' => array(
                'parameters' => array(
                    'options'  => array(
                        'script_paths' => array(
                            'asseticexample' => __DIR__ . '/../views',
                        ),
                    ),
                ),
            ),
        ),
    ),
);
