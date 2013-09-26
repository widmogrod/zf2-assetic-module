<?php

namespace AsseticBundle\View\Helper;

use Zend\View\Helper\Placeholder\Container,
    Zend\ServiceManager\ServiceLocatorInterface;

use AsseticBundle\ServiceFactory,
    AsseticBundle\Exception,
    Assetic\Asset\AssetInterface,
    Assetic\Asset\AssetCollection;

/**
 * Class Asset
 * @package AsseticBundle\View\Helper
 */
class Asset extends Container\AbstractStandalone
{

    /** @var \AsseticBundle\Service|null */
    protected $service = null;
    protected $baseUrl = '';
    protected $basePath = '';

    /**
     * @param ServiceLocatorInterface $serviceLocator
     */
    public function __construct(ServiceLocatorInterface $serviceLocator)
    {
        $serviceFactory = new ServiceFactory();
        $this->service = $serviceFactory->createService($serviceLocator);
        $this->service->build();

        $this->baseUrl = $this->service->getConfiguration()->getBaseUrl();
        $this->basePath = $this->service->getConfiguration()->getBasePath();
    }

    /**
     * @param string $collectionName
     * @param array $options
     * @return string
     * @throws \AsseticBundle\Exception\InvalidArgumentException
     */
    public function __invoke($collectionName, array $options = array())
    {
        if (!$this->service->getAssetManager()->has($collectionName)) {
            throw new Exception\InvalidArgumentException(
                'Collection "' . $collectionName . '" does not exist.'
            );
        }

        $asset = $this->service->getAssetManager()->get($collectionName);
        $this->service->writeAsset($asset);

        return $this->setupAsset($asset, $options);
    }

    /**
     * @param AssetInterface $asset
     * @param array $options
     * @return string
     */
    protected function setupAsset(AssetInterface $asset, array $options = array())
    {
        $ret = '';

        if (
            $this->service->getConfiguration()->isDebug()
            && !$this->service->getConfiguration()->isCombine()
            && $asset instanceof AssetCollection
        ) {
            // Move assets as single instance not as a collection
            foreach ($asset as $value) {
                /** @var AssetCollection $value */
                $ret .= $this->helper($value, $options) . PHP_EOL;
            }
        } else {
            $ret .= $this->helper($asset, $options) . PHP_EOL;
        }

        return $ret;
    }

    /**
     * @param AssetInterface $asset
     * @param array $options
     * @return string
     */
    protected function helper(AssetInterface $asset, array $options = array())
    {
        $path = $this->baseUrl . $this->basePath .  $asset->getTargetPath();

        $extension = pathinfo($path, PATHINFO_EXTENSION);
        $extension = strtolower($extension);

        if (isset($options['addFileMTime']) && $options['addFileMTime']) {
            $path .= '?' . $asset->getLastModified();
        }

        switch($extension)
        {
            case 'js':
                return $this->getScriptTag($path, $options);

            case 'css':
                return $this->getStylesheetTag($path, $options);
        }

        return '';
    }

    /**
     * @param $path
     * @param array $options
     * @return string
     */
    protected function getScriptTag($path, array $options = array())
    {
        $type = (isset($options['type']) && !empty($options['type'])) ? $options['type'] : 'text/javascript';

        return '<script type="' . $this->escape($type) . '" src="' . $this->escape($path) . '"></script>';
    }

    /**
     * @param $path
     * @param array $options
     * @return string
     */
    protected function getStylesheetTag($path, array $options = array())
    {
        $media = (isset($options['media']) && !empty($options['media'])) ? $options['media'] : 'screen';
        $type = (isset($options['type']) && !empty($options['type'])) ? $options['type'] : 'text/css';
        $rel = (isset($options['rel']) && !empty($options['rel'])) ? $options['rel'] : 'stylesheet';

        return '<link href="' . $this->escape($path)
            . '" media="' . $this->escape($media)
            . '" rel="' . $this->escape($rel)
            . '" type="' . $this->escape($type) . '">';
    }

}
