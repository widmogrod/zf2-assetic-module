<?php
namespace AsseticBundle\View;

use AsseticBundle\Service,
    Zend\View\Renderer\PhpRenderer,
    Assetic\Asset\AssetInterface;

class ViewHelperStrategy extends AbstractStrategy
{
    public function setupAsset(AssetInterface $asset)
    {
        $path = $this->getBaseUrl() . $this->getBasePath() .  $asset->getTargetPath();
        $this->helper($path);
    }

    protected function helper($path)
    {
        $extension = pathinfo($path, PATHINFO_EXTENSION);
        $extension = strtolower($extension);

        switch($extension)
        {
            case 'js':
                $this->appendScript($path);
                break;

            case 'css':
                $this->appendStylesheet($path);
                break;
        }
    }

    protected function appendScript($path)
    {
        $renderer = $this->getRenderer();
        switch (true)
        {
            case ($renderer instanceof PhpRenderer):
                if (strpos($path, "head_") !== false) {
                    $renderer->plugin('HeadScript')->appendFile($path);
                } else {
                    $renderer->plugin('InlineScript')->appendFile($path);
                }
                break;
        }
    }

    protected function appendStylesheet($path)
    {
        $renderer = $this->getRenderer();
        switch (true)
        {
            case ($renderer instanceof PhpRenderer):
                $renderer->plugin('HeadLink')->appendStylesheet($path);
                break;
        }
    }
}
