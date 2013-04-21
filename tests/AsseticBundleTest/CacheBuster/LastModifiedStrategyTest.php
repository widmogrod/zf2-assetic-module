<?php
namespace AsseticBundleTest\CacheBuster;

use AsseticBundle\CacheBuster\LastModifiedStrategy,
    Assetic\Asset\FileAsset;

class LastModifiedStrategyTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->cacheBuster = new LastModifiedStrategy();
    }

    public function testAssetLastModifiedTimestampIsPrependBeforeFileExtension()
    {
        $asset = new FileAsset(TEST_ASSETS_DIR . '/css/global.css');
        $asset->setTargetPath(TEST_PUBLIC_DIR . '/css/global.css');

        $this->cacheBuster->process($asset);

        $this->assertSame(
            TEST_PUBLIC_DIR . '/css/global.' . $asset->getLastModified() . '.css',
            $asset->getTargetPath()
        );
    }
}
