<?php

namespace AsseticBundle\CacheBuster;

use Assetic\Asset\AssetInterface,
    Assetic\Factory\Worker\WorkerInterface,
    Assetic\Factory\AssetFactory;

class NoCache implements WorkerInterface
{
    public function process(AssetInterface $asset, AssetFactory $factory)
    {
    }
}
