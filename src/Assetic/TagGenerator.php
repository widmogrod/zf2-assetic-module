<?php
namespace Assetic;

use Assetic\Asset\AssetInterface;

class TagGenerator
{
    private $baseUrl;

    private static $type;

    public function __construct($dir)
    {
        $this->baseUrl = rtrim($dir, '/\\');
    }

    public function generateTagFromAssetsManager(AssetManager $am)
    {
        $result = array();
        foreach ($am->getNames() as $name)
        {
            $tag = $this->generateTag($am->get($name));
            if (!isset($result[self::$type])) {
                $result[self::$type] = array();
            }
            $result[self::$type][] = $tag;
        }

        return array_map(function($tags){
            return implode("\n", $tags);
        }, $result);
    }

    public function generateTag(AssetInterface $asset)
    {
        return static::tag($this->baseUrl . '/' . $asset->getTargetPath());
    }

    static protected function tag($path)
    {
        $extension = pathinfo($path, PATHINFO_EXTENSION);
        $extension = strtolower($extension);
        switch($extension)
        {
            case 'js':
                self::$type = $extension;
                return sprintf('<script type="text/javascript" src="%s"></script>', $path);

            case 'css':
                self::$type = $extension;
                return sprintf('<link rel="stylesheet" type="text/css" href="%s">', $path);
        }
    }
}
