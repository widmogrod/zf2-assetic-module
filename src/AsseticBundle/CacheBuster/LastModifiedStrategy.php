<?php

namespace AsseticBundle\CacheBuster;

use Assetic\Asset\AssetInterface,
    Assetic\Contracts\Factory\Worker\WorkerInterface,
    Assetic\Factory\AssetFactory;

class LastModifiedStrategy implements WorkerInterface
{
    public function process(AssetInterface $asset, AssetFactory $factory)
    {
        $path = $asset->getTargetPath();
        $ext  = pathinfo($path, PATHINFO_EXTENSION);

        $lastModified = $factory->getLastModified($asset);
        if (null !== $lastModified) {
            $path = substr_replace(
                $path,
                "$lastModified.$ext",
                -1 * strlen($ext)
            );
            $asset->setTargetPath($path);
        }
    }
}
