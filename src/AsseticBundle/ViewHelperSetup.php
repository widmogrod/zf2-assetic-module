<?php
/**
 * @author gabriel
 */
 
namespace AsseticBundle;

use Zend\View\PhpRenderer,
    Assetic\AssetManager,
    Assetic\Asset\AssetInterface;

class ViewHelperSetup
{
    private $baseUrl;

    private $view;

    private $assetManager;

    public function __construct($dir, PhpRenderer $view, AssetManager $assetManager)
    {
        $this->baseUrl = rtrim($dir, '/\\');
        $this->view = $view;
        $this->assetManager = $assetManager;
    }

    public function setupFromOptions(array $options)
    {
        $result = array();
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
        return static::helper($this->baseUrl . '/' . $asset->getTargetPath());
    }

    protected function helper($path)
    {
        $extension = pathinfo($path, PATHINFO_EXTENSION);
        $extension = strtolower($extension);
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