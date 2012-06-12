<?php

namespace AsseticBundle\View;

use Assetic\Asset\AssetInterface;

class NoneStrategy extends AbstractStrategy
{
    public function setupAsset(AssetInterface $asset)
    {}
}