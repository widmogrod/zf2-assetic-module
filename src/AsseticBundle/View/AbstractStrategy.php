<?php
namespace AsseticBundle\View;

use AsseticBundle\Service,
    Zend\View\Renderer\RendererInterface as Renderer;

abstract class AbstractStrategy implements StrategyInterface
{
    protected $renderer;

    protected $baseUrl;

    protected $basePath;

    public function setRenderer(Renderer $renderer)
    {
        $this->renderer = $renderer;
    }

    /**
     * @return \Zend\View\Renderer\RendererInterface
     */
    public function getRenderer()
    {
        return $this->renderer;
    }

    public function setBaseUrl($baseUrl)
    {
        $this->baseUrl = $baseUrl;
    }

    public function getBaseUrl()
    {
        return $this->baseUrl;
    }

    public function setBasePath($basePath)
    {
        $this->basePath = $basePath;
    }

    public function getBasePath()
    {
        return $this->basePath;
    }
}