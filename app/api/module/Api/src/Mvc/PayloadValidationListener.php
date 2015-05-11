<?php

namespace Dvsa\Olcs\Api\Mvc;

use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\EventManager\ListenerAggregateTrait;
use Zend\Mvc\MvcEvent;
use Zend\Mvc\Router;
use Dvsa\Olcs\Transfer\Util\Annotation\AnnotationBuilder;
use Zend\Http\Request as HttpRequest;
use Zend\Http\Response;

class PayloadValidationListener implements ListenerAggregateInterface
{
    use ListenerAggregateTrait;

    protected $annotationBuilder;

    public function __construct(AnnotationBuilder $annotationBuilder)
    {
        $this->annotationBuilder = $annotationBuilder;
    }

    /**
     * Attach one or more listeners
     *
     * Implementors may add an optional $priority argument; the EventManager
     * implementation will pass this to the aggregate.
     *
     * @param EventManagerInterface $events
     *
     * @return void
     */
    public function attach(EventManagerInterface $events, $priority = 1)
    {
        $this->listeners[] = $events->attach(MvcEvent::EVENT_ROUTE, array($this, 'onRoute'), $priority);
    }

    public function onRoute(MvcEvent $e)
    {
        $request = $e->getRequest();
        if (!($request instanceof HttpRequest)) {
            return;
        }

        $method = $request->getMethod();

        $matches = $e->getRouteMatch();

        if (!$matches instanceof Router\RouteMatch) {
            // Can't do anything without a route match
            return;
        }

        $dtoClass = $matches->getParam('dto', false);

        if (!$dtoClass) {
            // no controller matched, nothing to do
            return;
        }

        $data = $matches->getParams();

        $dto = new $dtoClass();
        $matches->setParam('dto', $dto);

        if ($method === 'GET') {

            $data = array_merge($data, (array) $request->getQuery());
            $dto->exchangeArray($data);

            $query = $this->annotationBuilder->createQuery($dto);

            if (!$query->isValid()) {
                $response = new Response();
                $response->setStatusCode(Response::STATUS_CODE_422);
                $response->setContent(json_encode($query->getMessages(), true));
                return $response;
            }
        }/* else {
            $data = array_merge($data, $request->getContent());
            $dto->exchangeArray($data);

            $command = $this->annotationBuilder->createCommand($dto);
        }*/
    }
}
