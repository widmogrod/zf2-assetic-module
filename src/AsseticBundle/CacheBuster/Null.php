<?php

namespace AsseticBundle\CacheBuster;

use Assetic\Asset\AssetInterface,
    Assetic\Factory\Worker\WorkerInterface,
    Assetic\Factory\AssetFactory;

class Null implements WorkerInterface
{
    public function process(AssetInterface $asset, AssetFactory $factory)
    {
    }
}

