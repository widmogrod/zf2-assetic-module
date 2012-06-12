<?php
namespace AsseticBundle\View;

use AsseticBundle\Service,
    Zend\View\Renderer\RendererInterface as Renderer,
    Assetic\Asset\AssetInterface;

interface StrategyInterface
{
    public function setRenderer(Renderer $renderer);
    public function getRenderer();

    public function setBaseUrl($baseUrl);
    public function getBaseUrl();

    public function setupAsset(AssetInterface $asset);
}