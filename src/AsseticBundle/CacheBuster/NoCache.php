<?php

namespace AsseticBundle\CacheBuster;

use Assetic\Asset\AssetInterface;
use Assetic\Contracts\Factory\Worker\WorkerInterface;
use Assetic\Factory\AssetFactory;

class NoCache implements WorkerInterface
{
    public function process(AssetInterface $asset, AssetFactory $factory)
    {
    }
}
