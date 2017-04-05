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
    public function create($data)
    {
        return $this->respondError(405, 'Method Not Allowed');
    }

    public function delete($id)
    {
        return $this->respondError(405, 'Method Not Allowed');
    }

    public function deleteList($data = null)
    {
        return $this->respondError(405, 'Method Not Allowed');
    }

    public function get($id)
    {
        return $this->respondError(405, 'Method Not Allowed');
    }

    public function getList()
    {
        return $this->respondError(405, 'Method Not Allowed');
    }

    public function head($id = null)
    {
        return $this->respondError(405, 'Method Not Allowed');
    }

    public function options()
    {
        return $this->respondError(405, 'Method Not Allowed');
    }

    public function patch($id, $data)
    {
        return $this->respondError(405, 'Method Not Allowed');
    }

    public function replaceList($data)
    {
        return $this->respondError(405, 'Method Not Allowed');
    }

    public function patchList($data)
    {
        return $this->respondError(405, 'Method Not Allowed');
    }

    public function update($id, $data)
    {
        return $this->respondError(405, 'Method Not Allowed');
    }

    public function notFoundAction()
    {
        return $this->respondError(404, 'Page Not Found');
    }

    protected function respondError($code, $message)
    {
        $this->getResponse()
            ->setStatusCode($code)
            ->getHeaders()
            ->addHeaderLine('Content-Type', 'application/problem+json');

        $data = ['status' => $code, 'title' => $message];

        return $this->respond($data);
    }

    protected function respond($data)
    {
        return new JsonModel($data);
    }

    protected function debug($message, $data = [])
    {
        return Logger::debug(
            $message,
            [
                'data' => $data
            ]
        );
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
