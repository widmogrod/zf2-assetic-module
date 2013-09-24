<?php

namespace AsseticBundle\View\Helper;

use Zend\View\Helper\Placeholder\Container,
    Zend\View\Renderer\PhpRenderer,
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

    /**
     * @param ServiceLocatorInterface $serviceLocator
     */
    public function __construct(ServiceLocatorInterface $serviceLocator)
    {
        $serviceFactory = new ServiceFactory();
        $this->service = $serviceFactory->createService($serviceLocator);
        $this->service->build();
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

        return $this->setupAsset($asset, $options);
    }

    /**
     * @param AssetInterface $asset
     * @param array $options
     * @return string
     */
    protected function setupAsset(AssetInterface $asset, array $options = array())
    {
        $baseUrl = $this->service->getConfiguration()->getBaseUrl();
        $basePath = $this->service->getConfiguration()->getBasePath();
        $ret = '';

        if (
            $this->service->getConfiguration()->isDebug()
            && !$this->service->getConfiguration()->isCombine()
            && $asset instanceof AssetCollection
        ) {
            // Move assets as single instance not as a collection
            foreach ($asset as $value) {
                /** @var AssetCollection $value */
                $path = $baseUrl . $basePath .  $value->getTargetPath();
                $ret .= $this->helper($path, $options) . PHP_EOL;
            }
        } else {
            $path = $baseUrl . $basePath .  $asset->getTargetPath();
            $ret .= $this->helper($path, $options) . PHP_EOL;
        }

        return $ret;
    }

    /**
     * @param string $path
     * @param array $options
     * @return string
     */
    protected function helper($path, array $options = array())
    {
        $extension = pathinfo($path, PATHINFO_EXTENSION);
        $extension = strtolower($extension);

        switch($extension)
        {
            case 'js':
                $type = (isset($options['type']) && !empty($options['type'])) ? $options['type'] : 'text/javascript';
                return $this->getScriptTag($path, $type);

            case 'css':
                $media = (isset($options['media']) && !empty($options['media'])) ? $options['media'] : 'screen';
                $type = (isset($options['type']) && !empty($options['type'])) ? $options['type'] : 'text/css';
                $rel = (isset($options['rel']) && !empty($options['rel'])) ? $options['rel'] : 'stylesheet';
                return $this->getStylesheetTag($path, $media, $type, $rel);
        }

        return '';
    }

    /**
     * @param string $path
     * @param string $type
     * @return string
     */
    protected function getScriptTag($path, $type)
    {
        $renderer = $this->getView()->getEngine();
        if ($renderer instanceof PhpRenderer) {
            if (strpos($path, "head_") !== false) {
                return '<script type="' . $this->escape($type) . '" src="' . $this->escape($path) . '"></script>';
            } else {
                return '<script type="' . $this->escape($type) . '">' . $path . '</script>';
            }
        }

        return '';
    }

    /**
     * @param string $path
     * @param string $media
     * @param string $type
     * @param string $rel
     * @return string
     */
    protected function getStylesheetTag($path, $media, $type, $rel)
    {
        return '<link href="' . $this->escape($path)
            . '" media="' . $this->escape($media)
            . '" rel="' . $this->escape($rel)
            . '" type="' . $this->escape($type) . '">';
    }

}
