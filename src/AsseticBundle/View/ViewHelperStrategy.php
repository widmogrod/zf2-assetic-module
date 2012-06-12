<?php

namespace AsseticBundle\View;

use AsseticBundle\Service,
    Zend\View\Renderer\RendererInterface as Renderer,
    Zend\View\Renderer\PhpRenderer,
    Assetic\Asset\AssetInterface;

class ViewHelperStrategy extends AbstractStrategy
{
    public function setupAsset(AssetInterface $asset)
    {
        $path = $this->getBaseUrl() . $asset->getTargetPath();
        return $this->helper($path, $asset->getLastModified());
    }

    protected function helper($path, $lastModified = null)
    {
        $extension = pathinfo($path, PATHINFO_EXTENSION);
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
                $renderer->plugin('InlineScript')->appendFile($path);
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