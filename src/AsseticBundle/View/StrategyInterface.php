<?php

namespace AsseticBundle\View;

use AsseticBundle\Service;
use Zend\View\Renderer\RendererInterface as Renderer;
use Assetic\Asset\AssetInterface;

interface StrategyInterface
{
    public function setRenderer(Renderer $renderer);
    public function getRenderer();

    public function setBaseUrl($baseUrl);
    public function getBaseUrl();

    public function setBasePath($basePath);
    public function getBasePath();

    public function setDebug($flag);
    public function isDebug();

    public function setCombine($flag);
    public function isCombine();

    public function setupAsset(AssetInterface $asset);
}
