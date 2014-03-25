<?php
namespace Olcs\Db\Controller;

use Zend\Mvc\Controller\AbstractRestfulController as ZendAbstractRestfulController;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;
use Zend\Stdlib\RequestInterface as Request;
use Zend\Stdlib\ResponseInterface as Response;
use Zend\Mvc\Exception;
use Zend\Mvc\MvcEvent;
use Olcs\Db\Traits\LoggerAwareTrait as OlcsLoggerAwareTrait;
use Olcs\Db\Exceptions\RestResponseException;
use Olcs\Db\Traits\RestResponseTrait;

abstract class AbstractController extends ZendAbstractRestfulController
{
    use RestResponseTrait;
    use OlcsLoggerAwareTrait;

    /**
     * Handle the request
     *
     * @todo   try-catch in "patch" for patchList should be removed in the future
     * @param  MvcEvent $e
     * @return mixed
     * @throws Exception\DomainException if no route matches in event or invalid HTTP method
     */
    public function onDispatch(MvcEvent $e)
    {
        // Log the incoming request headers...
        $this->log('Request Headers: ' . $e->getRequest()->getHeaders()->toString());

        // Log the incoming request parameters...
        $routeParams = $this->plugin('params')->fromRoute();
        $queryParams = $this->plugin('params')->fromQuery();
        $postParams = $this->plugin('params')->fromPost();

        $this->log(sprintf('Input Route Params: %1$s', print_r($routeParams, true)));
        $this->log(sprintf('Input Get Params: %1$s', print_r($queryParams, true)));
        $this->log(sprintf('Input Post Params: %1$s', print_r($postParams, true)));

        // Is action request?...
        $action = $e->getRouteMatch()->getParam('action', false);
        if ($action) {
            $this->log(sprintf('Dispatching Incomming Action Request: \'%1$s\'', $action));
            return $this->doDispatch($e);
        }

        // Is method request?...
        $method = strtolower($e->getRequest()->getMethod());
        if ($method) {
            $this->log(sprintf('Dispatching Incomming Method Request: \'%1$s\' ', $method));
            return $this->doDispatch($e);
        }

        return $this->doDispatch($e);
    }

    /**
     * Wrap the parent dispatch method
     *
     * @param type $e
     * @return type
     */
    private function doDispatch($e)
    {
        try {
            return parent::onDispatch($e);

        } catch (RestResponseException $ex) {

            return $this->respond($ex->getCode(), $ex->getMessage());
        }
    }

    /**
     * Dispatch a request
     *
     * If the route match includes an "action" key, then this acts basically like
     * a standard action controller. Otherwise, it introspects the HTTP method
     * to determine how to handle the request, and which method to delegate to.
     *
     * @events dispatch.pre, dispatch.post
     * @param  Request $request
     * @param  null|Response $response
     * @return mixed|Response
     * @throws Exception\InvalidArgumentException
     */
    public function dispatch(Request $request, Response $response = null)
    {
        $return = parent::dispatch($request, $response);

        if ($return instanceof ViewModel) {
            $return = new JsonModel($return->getVariables());
        }

        return $return;
    }

    /**
     * Basic functionality for when a page is not available
     *
     * @return array
     */
    public function notFoundAction()
    {
        $this->response->setStatusCode(404);

        return new JsonModel(array(
            'content' => 'Page not found'
        ));
    }

    public function getControllerName()
    {
        return $this->params()->fromRoute('controller');
    }
}
