<?php

namespace AsseticBundle;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Interop\Http\Server\MiddlewareInterface;
use Zend\Expressive\Router\RouteResult;

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

    public function renderAssets($request)
    {
        /** @var Configuration $config */
        #$config = $sm->get('AsseticConfiguration');
        #if ($e->getName() === MvcEvent::EVENT_DISPATCH_ERROR) {
        #    $error = $e->getError();
        #    if ($error && !in_array($error, $config->getAcceptableErrors())) {
        #        // break if not an acceptable error
        #        return;
        #    }
        #}

        $asseticService = $this->asseticService;
        $routeResult = $request->getAttribute(RouteResult::class);
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