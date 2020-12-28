<?php

namespace AsseticBundle\CacheBuster;

use Assetic\Asset\AssetInterface,
    Assetic\Contracts\Factory\Worker\WorkerInterface,
    Assetic\Factory\AssetFactory;

class NoCache implements WorkerInterface
{
    public function process(AssetInterface $asset, AssetFactory $factory)
    {
    }
}
