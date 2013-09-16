<?php
namespace AsseticBundle;

use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\Http\Header\LastModified;
use Zend\Http\PhpEnvironment\Response;
use Zend\Mvc\Application;
use Zend\Mvc\MvcEvent;

class Listener implements ListenerAggregateInterface
{
    /**
     * @var CallbackHandler[]
     */
    protected $listeners = array();

    /**
     * Attach one or more listeners
     *
     * Implementors may add an optional $priority argument; the EventManager
     * implementation will pass this to the aggregate.
     *
     * @param EventManagerInterface $events
     */
    public function attach(EventManagerInterface $events)
    {
        $this->listeners[] = $events->attach(MvcEvent::EVENT_DISPATCH, array($this, 'renderAssets'), 32);
        $this->listeners[] = $events->attach(MvcEvent::EVENT_DISPATCH_ERROR, array($this, 'renderAssets'), 32);
        $this->listeners[] = $events->attach(MvcEvent::EVENT_DISPATCH_ERROR, array($this, 'serveStaticAssets'), 31);
    }

    /**
     * Detach all previously attached listeners
     *
     * @param EventManagerInterface $events
     */
    public function detach(EventManagerInterface $events)
    {
        foreach ($this->listeners as $index => $listener) {
            if ($events->detach($listener)) {
                unset($this->listeners[$index]);
            }
        }
    }

    public function serveStaticAssets(MvcEvent $e)
    {
        $sm     = $e->getApplication()->getServiceManager();
        /** @var Configuration $config */
        $config = $sm->get('AsseticConfiguration');
        if($config->isCombine() || !$config->isDebug()) {
            // combine must be disabled and debug enabled
            return;
        }

        $error = $e->getError();
        if ($error && !in_array($error, array(
                Application::ERROR_CONTROLLER_NOT_FOUND,
                Application::ERROR_CONTROLLER_INVALID,
                Application::ERROR_ROUTER_NO_MATCH,
        ))){
            // this should only be invoked for 404 errors
            return;
        }

        $response = $e->getResponse();
        if (!$response) {
            $response = new Response();
            $e->setResponse($response);
        }

        /** @var $asseticService \AsseticBundle\Service */
        $asseticService = $sm->get('AsseticService');

        // could not find any renderer, so we'll try to match the request URI against asset's target path
        if($asset = $asseticService->findAssetForRequest($e->getRequest())){

            if(count($asset->getFilters())) {
                $dump = $asset->dump();
                $path = tempnam(sys_get_temp_dir(), 'asset-dump');
                file_put_contents($path, $dump);
            } else {
                $path = $asset->getSourceRoot() . '/' . $asset->getSourcePath();
            }

            $lastModified = new LastModified();
            $date = new \DateTime();
            $date->setTimestamp($asset->getLastModified());
            $lastModified->setDate($date);

            $headers = $response->getHeaders();
            $headers->addHeaderLine('Content-Length', filesize($path));
            $headers->addHeader($lastModified);
            $response->setContent(file_get_contents($path));
            $response->setStatusCode(200);

            $ext = pathinfo($asset->getTargetPath(), PATHINFO_EXTENSION);
            if($ext == 'css') {
                $headers->addHeaderLine('Content-Type', 'text/css');
            }elseif($ext == 'js') {
                $headers->addHeaderLine('Content-Type', 'text/javascript');
            } elseif (function_exists('finfo_open')) {
                $db = @finfo_open(FILEINFO_MIME);

                if ($db) {
                    if($mimeType = finfo_file($db, $path)) {
                        $headers->addHeaderLine('Content-Type', $mimeType);
                    }
                }
            }

            // remove temp file
            if(count($asset->getFilters())) {
                unlink($path);
            }

            $e->stopPropagation(true);

            return $response;
        }
    }

    public function renderAssets(MvcEvent $e)
    {
        $sm     = $e->getApplication()->getServiceManager();
        /** @var Configuration $config */
        $config = $sm->get('AsseticConfiguration');
        if ($e->getName() === MvcEvent::EVENT_DISPATCH_ERROR) {
            $error = $e->getError();
            if ($error && !in_array($error, $config->getAcceptableErrors())) {
                // break if not an acceptable error
                return;
            }
        }

        $response = $e->getResponse();
        if (!$response) {
            $response = new Response();
            $e->setResponse($response);
        }

        /** @var $asseticService \AsseticBundle\Service */
        $asseticService = $sm->get('AsseticService');

        $asseticService->setRequest($e->getRequest());

        // setup service if a matched route exist
        $router = $e->getRouteMatch();
        if ($router) {
            $asseticService->setRouteName($router->getMatchedRouteName());
            $asseticService->setControllerName($router->getParam('controller'));
            $asseticService->setActionName($router->getParam('action'));
        }

        // Create all objects
        $asseticService->build();

        // Init assets for modules
        $asseticService->setupRenderer($sm->get('ViewRenderer'));
   }
}