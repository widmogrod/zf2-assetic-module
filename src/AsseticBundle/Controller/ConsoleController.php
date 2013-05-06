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
        $this->assetic->initLoadedModules();
    }

    public function setupAction()
    {

    }

    public function setAsseticBundleService(Service $service)
    {
        $this->assetic = $service;
    }
}