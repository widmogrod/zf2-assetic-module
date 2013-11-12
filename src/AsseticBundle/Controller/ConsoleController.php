<?php
namespace AsseticBundle\Controller;

use AsseticBundle\AsseticBundleServiceAwareInterface;
use AsseticBundle\Service;
use Zend\Mvc\Controller\AbstractActionController;

class ConsoleController extends AbstractActionController implements AsseticBundleServiceAwareInterface
{
    /**
     * @var Service
     */
    protected $assetic;

    public function buildAction()
    {
        $config = $this->assetic->getConfiguration();
        $config->setBuildOnRequest(true);
        $this->assetic->build();
        $manager = $this->assetic->getAssetManager();
        $writer = $this->assetic->getAssetWriter();
        $writer->writeManagerAssets($manager);
    }

    public function setupAction()
    {
        $config = $this->assetic->getConfiguration();
        $mode = (null !== ($mode = $config->getUmask())) ? $mode : 0775;
        $displayMode = decoct($mode);

        $cachePath = $config->getCachePath();
        $pathExists = is_dir($cachePath);
        if ($cachePath && !$pathExists) {
            mkdir($cachePath, $mode, true);
            echo "Cache path created '$cachePath' with mode '$displayMode' \n";
        } else if ($pathExists) {
            echo "Creation of cache path '$cachePath' skipped - path exists \n";
        } else {
            echo "Creation of cache path '$cachePath' skipped - no path provided \n";
        }

        $webPath = $config->getWebPath();
        $pathExists = is_dir($webPath);
        if ($webPath && !$pathExists) {
            mkdir($webPath, $mode, true);
            echo "Web path created '$webPath' with mode '$displayMode' \n";
        } else if ($pathExists) {
            echo "Creation of web path '$webPath' skipped - path exists \n";
        } else {
            echo "Creation of web path '$webPath' skipped - no path provided \n";
        }
    }

    public function setAsseticBundleService(Service $service)
    {
        $this->assetic = $service;
    }
}