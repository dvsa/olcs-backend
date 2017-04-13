<?php

/**
 * Abstract controller
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Scanning\Controller;

use Olcs\Logging\Log\Logger;
use Zend\Mvc\Controller\AbstractRestfulController;
use Zend\View\Model\JsonModel;

/**
 * Abstract controller
 *
 * @NOTE This abstract extends zends abstract restful controller to standardise response
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
abstract class AbstractController extends AbstractRestfulController
{
    /**
     * Create a new resource
     *
     * @param array $data parameters to create new record with Entity
     *
     * @return JsonModel
     */
    public function create($data)
    {
        return $this->respondError(405, 'Method Not Allowed');
    }

    /**
     * Delete an existing resource
     *
     * @param int $id ID or resource number to delete
     *
     * @return JsonModel
     */
    public function delete($id)
    {
        return $this->respondError(405, 'Method Not Allowed');
    }

    /**
     * Delete list
     *
     * @param array $data Parameters used to query which records to delete
     *
     * @return JsonModel
     */
    public function deleteList($data)
    {
        return $this->respondError(405, 'Method Not Allowed');
    }

    /**
     * Return single resource
     *
     * @param int $id ID to retrieve
     *
     * @return JsonModel
     */
    public function get($id)
    {
        return $this->respondError(405, 'Method Not Allowed');
    }

    /**
     * Return list of resources
     *
     * @return JsonModel
     */
    public function getList()
    {
        return $this->respondError(405, 'Method Not Allowed');
    }

    /**
     * Retrieve HEAD metadata for the resource
     *
     * Not marked as abstract, as that would introduce a BC break
     * (introduced in 2.1.0); instead, raises an exception if not implemented.
     *
     * @param null|int $id ID
     *
     * @return mixed
     */
    public function head($id = null)
    {
        return $this->respondError(405, 'Method Not Allowed');
    }

    /**
     * Options
     *
     * @return JsonModel
     */
    public function options()
    {
        return $this->respondError(405, 'Method Not Allowed');
    }

    /**
     * Patch record
     *
     * @param int   $id   Resource ID to patch
     * @param mixed $data Data to patch
     *
     * @return JsonModel
     */
    public function patch($id, $data)
    {
        return $this->respondError(405, 'Method Not Allowed');
    }

    /**
     * Replace list
     *
     * @param mixed $data Data to replace
     *
     * @return JsonModel
     */
    public function replaceList($data)
    {
        return $this->respondError(405, 'Method Not Allowed');
    }

    /**
     * Patch list
     *
     * @param mixed $data Data to patch
     *
     * @return JsonModel
     */
    public function patchList($data)
    {
        return $this->respondError(405, 'Method Not Allowed');
    }

    /**
     * Update record
     *
     * @param int   $id   ID
     * @param mixed $data Data to update
     *
     * @return JsonModel
     */
    public function update($id, $data)
    {
        return $this->respondError(405, 'Method Not Allowed');
    }

    /**
     * Not found response
     *
     * @return JsonModel
     */
    public function notFoundAction()
    {
        return $this->respondError(404, 'Page Not Found');
    }

    /**
     * Respond with an error
     *
     * @param int    $code    Status Code
     * @param string $message Error message to display
     *
     * @return JsonModel
     */
    protected function respondError($code, $message)
    {
        $this->getResponse()
            ->setStatusCode($code)
            ->getHeaders()
            ->addHeaderLine('Content-Type', 'application/problem+json');

        $data = ['status' => $code, 'title' => $message];

        return $this->respond($data);
    }

    /**
     * API response
     *
     * @param array $data Data to respond with
     *
     * @return JsonModel
     */
    protected function respond($data)
    {
        return new JsonModel($data);
    }

    /**
     * API Debugger
     *
     * @param string $message Message to debug
     * @param array  $data    Query to add to log
     *
     * @return \Zend\Log\LoggerInterface
     */
    protected function debug($message, $data = [])
    {
        return Logger::debug($message, ['data' => $data]);
    }

    /**
     * Add an ERR log entry
     *
     * @param string $message Message to log
     * @param array  $data    Additional data
     *
     * @return Logger
     */
    protected function logError($message, $data = [])
    {
        return Logger::err(
            $message,
            $data
        );
    }
}
