<?php
namespace AsseticBundle;

use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\Http\PhpEnvironment\Response;
use Zend\Mvc\MvcEvent;
use Zend\Stdlib\CallbackHandler;

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