<?php

namespace AsseticBundle;

class ConfigProvider
{
    public function __invoke()
    {
        $config = include __DIR__ . '/configs/module.config.php';
        $config['dependencies'] = $config['service_manager'];
        unset($config['service_manager']);

        return $config;
    }
}