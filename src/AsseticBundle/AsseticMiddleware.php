<?php

namespace AsseticBundle;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Zend\Diactoros\Response\RedirectResponse;

class AsseticMiddleware implements MiddlewareInterface
{
    /** @var $asseticService \AsseticBundle\Service */
    protected $asseticService;

    protected $viewRenderer;

    public function __construct($asseticService, $viewRenderer)
    {
        $this->asseticService = $asseticService;
        $this->viewRenderer = $viewRenderer;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $this->renderAssets($request);

        return $handler->handle($request);
    }

    //public function renderAssets(MvcEvent $e)
    public function renderAssets($request)
    {
        //$sm     = $e->getApplication()->getServiceManager();
        /** @var Configuration $config */
        //$config = $sm->get('AsseticConfiguration');
        /*if ($e->getName() === MvcEvent::EVENT_DISPATCH_ERROR) {
            $error = $e->getError();
            if ($error && !in_array($error, $config->getAcceptableErrors())) {
                // break if not an acceptable error
                return;
            }
        }*/

        //$response = $e->getResponse();
        //if (!$response) {
        //    $response = new Response();
        //    $e->setResponse($response);
        //}

        /** @var $asseticService \AsseticBundle\Service */
        //$asseticService = $sm->get('AsseticService');
        $asseticService = $this->asseticService;

        // setup service if a matched route exist
        //$router = $e->getRouteMatch();
        # instance of RouteResult
        $routeResult = $request->getAttribute('Zend\Expressive\Router\RouteResult');
        //$routeName   = $routeResult->getMatchedRouteName();
        if ($routeResult) {
            $actionName = $request->getAttribute('action', 'index');
            $moduleName = $request->getAttribute('controller', $request->getAttribute('module', $actionName));

            $asseticService->setRouteName($routeResult->getMatchedRouteName());
            $asseticService->setControllerName($actionName);
            $asseticService->setActionName($moduleName);
        }

        // Create all objects
        $asseticService->build();

        // Init assets for modules
        $asseticService->setupRenderer($this->viewRenderer);
    }
}