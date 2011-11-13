<?php
namespace AsseticBundle;

use Assetic\AssetManager,
    Assetic\Asset\AssetInterface;

class TagGenerator
{
    private $baseUrl;

    private $assetManager;

    private static $type;

    public function __construct($dir, AssetManager $assetManager)
    {
        $this->baseUrl = rtrim($dir, '/\\');
        $this->assetManager = $assetManager;
    }

    public function getnerateTagFromOptions(array $options)
    {
        $result = array();
        while($assetAlias = array_shift($options))
        {
            $assetAlias = ltrim($assetAlias, '@');

            /** @var $asset \Assetic\Asset\AssetInterface */
            $asset = $this->assetManager->get($assetAlias);

            $tag = $this->generateTag($asset);
            if (!isset($result[self::$type])) {
                $result[self::$type] = array();
            }
            $result[self::$type][] = $tag;
        }

        return array_map(function($tags){
            return implode("\n", $tags);
        }, $result);
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
