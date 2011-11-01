<?php
namespace Assetic;

use Assetic\Asset\AssetInterface;

class TagGenerator
{
    private $dir;

    public function __construct($dir)
    {
        $this->dir = rtrim($dir, '/\\');
    }

    public function generateTagFromAssetsManager(AssetManager $am)
    {
        $result = array();
        foreach ($am->getNames() as $name) {
            $result[] = $this->generateTag($am->get($name));
        }
        return implode("\n", $result);
    }

    public function generateTag(AssetInterface $asset)
    {
        return static::tag($this->dir . '/' . $asset->getTargetPath());
    }

    static protected function tag($path)
    {
        $extension = pathinfo($path, PATHINFO_EXTENSION);
        $extension = strtolower($extension);
        switch($extension)
        {
            case 'js':
                return sprintf('<script type="text/javascript" src="%s"></script>', $path);

            case 'css':
                return sprintf('<link rel="stylesheet" type="text/css" href="%s">', $path);
        }
    }
}
