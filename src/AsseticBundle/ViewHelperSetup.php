<?php
/**
 * @author gabriel
 */
 
namespace AsseticBundle;

use Zend\View\Renderer,
    Assetic\AssetManager,
    Assetic\Asset\AssetInterface;

class ViewHelperSetup
{
    private $baseUrl;

    private $view;

    private $assetManager;

    public function __construct($dir, Renderer $view, AssetManager $assetManager)
    {
        $this->baseUrl = rtrim($dir, '/\\');
        $this->view = $view;
        $this->assetManager = $assetManager;
    }

    public function setupFromOptions(array $options)
    {
        while($assetAlias = array_shift($options))
        {
            $assetAlias = ltrim($assetAlias, '@');

            /** @var $asset \Assetic\Asset\AssetInterface */
            $asset = $this->assetManager->get($assetAlias);

            $this->setupHelper($asset);
        }
    }

    public function setupHelper(AssetInterface $asset)
    {
        $path = (empty($this->baseUrl) ? '' : $this->baseUrl . '/');
        $path .= $asset->getTargetPath();
        return $this->helper($path, $asset->getLastModified());
    }

    protected function helper($path, $lastModified = null)
    {
        $extension = pathinfo($path, PATHINFO_EXTENSION);
        // $extension = empty($extension) ? substr(strrchr($path,'_'), 1) : $extension;
        $extension = strtolower($extension);

        if (null !== $lastModified)
        {
            $path = strpos($path, '?')
                ? sprintf('%s&%s', $path, $lastModified)
                : sprintf('%s?%s', $path, $lastModified);
        }

        switch($extension)
        {
            case 'js':
                /** @var $helper \Zend\View\Helper\InlineScript */
                $helper = $this->view->plugin('InlineScript');
                $helper->appendFile($path);
                break;

            case 'css':
                /** @var $helper \Zend\View\Helper\HeadLink */
                $helper = $this->view->plugin('HeadLink');
                $helper->appendStylesheet($path);
                break;
        }
    }
}