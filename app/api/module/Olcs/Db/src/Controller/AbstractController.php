<?php

namespace Olcs\Db\Controller;

use Zend\Mvc\Controller\AbstractRestfulController as ZendAbstractRestfulController;
use Zend\Mvc\Exception;
use Zend\Mvc\MvcEvent;
use Zend\Http\Response;
use Olcs\Db\Traits\LoggerAwareTrait as OlcsLoggerAwareTrait;
use Olcs\Db\Exceptions\RestResponseException;
use Olcs\Db\Traits\RestResponseTrait;

/**
 * Abstract Controller
 *
 * @author someone <someone@valtech.co.uk>
 */
abstract class AbstractController extends ZendAbstractRestfulController
{
    use RestResponseTrait,
        OlcsLoggerAwareTrait;

    /**
     * Handle the request
     *
     * @param  MvcEvent $e
     * @return mixed
     * @throws Exception\DomainException if no route matches in event or invalid HTTP method
     */
    public function onDispatch(MvcEvent $e)
    {
        $this->doDispatch($e);
    }

    public function doDispatch(MvcEvent $e)
    {
        try {

            return parent::onDispatch($e);

        } catch (RestResponseException $ex) {

            return $this->respond($ex->getCode(), $ex->getMessage());
        }
    }

    /**
     * Basic functionality for when a page is not available
     *
     * @return array
     */
    public function notFoundAction()
    {
        return $this->respond(Response::STATUS_CODE_404, 'Resource not found');
    }

    /**
     * Get Controller name from route
     *
     * @return string
     */
    public function getControllerName()
    {
        $controller = $this->formatControllerName(
            $this->params()->fromRoute('controller')
        );

        return $controller;
    }

    private function formatControllerName($controller)
    {
        return str_replace(' ', '', ucwords(str_replace('-', ' ', $controller)));
    }

    /**
     * Format data from json
     *
     * @param mixed $data
     */
    public function formatDataFromJson($data)
    {
        $data = (is_array($data) && isset($data['data'])) ? $data['data'] : $data;

        if (!is_string($data)) {

            return $this->respond(Response::STATUS_CODE_400, 'Expected JSON request data');
        }

        $data = json_decode($data, true);

        if (json_last_error() !== JSON_ERROR_NONE) {

            return $this->respond(Response::STATUS_CODE_400, 'JSON request data is invalid');
        }

        return $data;
    }

    /**
     *  We should try and catch all known exceptions and provide a reasonable
     *  response, if we get here, then we have no idea what went wrong
     *
     * @param \Exception $ex
     * @return Response
     */
    protected function unknownError($ex)
    {
        if (is_string($ex)) {
            return $this->respond(Response::STATUS_CODE_500, 'An unknown error occurred: ' . $ex);
        }

        return $this->respond(
            Response::STATUS_CODE_500,
            'An unknown error occurred: ' . $ex->getMessage(),
            [(array)$ex, $ex->getTrace()]
        );
    }
}
