<?php

namespace AsseticBundle\CacheBuster;

use Assetic\Asset\AssetInterface,
    Assetic\Factory\Worker\WorkerInterface,
    Assetic\Factory\AssetFactory;

class LastModifiedStrategy implements WorkerInterface
{
    public function process(AssetInterface $asset)
    {
        $path = $asset->getTargetPath();
        $ext  = pathinfo($path, PATHINFO_EXTENSION);

        $lastModified = $asset->getLastModified();
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

