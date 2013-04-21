<?php

namespace AsseticBundle\CacheBuster;

use Assetic\Asset\AssetInterface,
    Assetic\Factory\Worker\WorkerInterface;

class Null implements WorkerInterface
{
    public function process(AssetInterface $asset)
    {
    }
}

