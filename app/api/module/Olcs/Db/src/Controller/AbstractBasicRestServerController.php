<?php
namespace Olcs\Db\Controller;

use Olcs\Db\Utility\RestServerInterface as OlcsRestServerInterface;
use Zend\Http\Response;
use Olcs\Db\Exceptions\RestResponseException;
use Olcs\Db\Traits\RestResponseTrait;
use Olcs\Db\Exceptions\NoVersionException;
use Doctrine\ORM\OptimisticLockException;

abstract class AbstractBasicRestServerController extends AbstractController implements OlcsRestServerInterface
{
    use RestResponseTrait;

    protected $serviceName;

    protected $allowedMethods = array(
        'create',
        'get',
        'getList',
        'update',
        'patch',
        'delete'
    );

    /**
     * Create an entity
     *
     * @param mixed $data
     * @return Response
     */
    public function create($data)
    {
        $this->checkMethod(__METHOD__);

        $data = $this->formatDataFromJson($data);

        if ($data instanceof Response) {

            return $data;
        }

        try {
            $id = $this->getService()->create($data);

            if (is_numeric($id) && $id > 0) {

                return $this->respond(Response::STATUS_CODE_201, 'Entity Created', array('id' => $id));
            }

            throw new \Exception();

        } catch (\Exception $ex) {

            return $this->unknownError($ex);
        }
    }

    /**
     * Get an entity by it's id
     *
     * @param int $id
     * @return Response
     */
    public function get($id)
    {
        $this->checkMethod(__METHOD__);

        try {

            $data = $this->getDataFromQuery();

            $result = $this->getService()->get($id, $data);

            if (empty($result)) {

                return $this->respond(Response::STATUS_CODE_404, 'Entity not found');
            }

            return $this->respond(Response::STATUS_CODE_200, 'Entity found', $result);

        } catch (\Exception $ex) {

            return $this->unknownError($ex);
        }
    }

    /**
     * Get a list of entities
     *
     * @return Response
     */
    public function getList()
    {
        $this->checkMethod(__METHOD__);

        $data = $this->getDataFromQuery();

        try {
            $result = $this->getService()->getList($data);

            if (empty($result)) {

                return $this->respond(Response::STATUS_CODE_200, 'No results found');
            }

            return $this->respond(Response::STATUS_CODE_200, 'Results found', $result);

        } catch (\Exception $ex) {

            return $this->unknownError($ex);
        }
    }

    /**
     * Update a record
     *
     * @param id $id
     * @param mixed $data
     * @return Response
     */
    public function update($id, $data)
    {
        $this->checkMethod(__METHOD__);

        return $this->updateOrPatch($id, $data, 'update');
    }

    /**
     * Patch a record
     *
     * @param id $id
     * @param mixed $data
     * @return Response
     */
    public function patch($id, $data)
    {
        $this->checkMethod(__METHOD__);

        return $this->updateOrPatch($id, $data, 'patch');
    }

    /**
     * Update and patch give the same response so no need to duplicate
     *
     * @param id $id
     * @param mixed $data
     * @param string $method
     * @return Response
     */
    protected function updateOrPatch($id, $data, $method)
    {
        $data = $this->formatDataFromJson($data);

        if ($data instanceof Response) {

            return $data;
        }

        try {
            if ($this->getService()->$method($id, $data)) {

                return $this->respond(Response::STATUS_CODE_200, 'Entity updated');
            }

            return $this->respond(Response::STATUS_CODE_404, 'Entity not found');

        } catch (NoVersionException $ex) {

            return $this->respond(Response::STATUS_CODE_400, 'No version number sent');

        } catch (OptimisticLockException $ex) {

            $result = $this->getService()->get($id);

            return $this->respond(Response::STATUS_CODE_409, 'This entity has been updated since', $result);

        } catch (\Exception $ex) {

            return $this->unknownError($ex);
        }
    }

    /**
     * Delete a record
     *
     * @param id $id
     * @return Response
     */
    public function delete($id)
    {
        $this->checkMethod(__METHOD__);

        try {
            if ($this->getService()->delete($id)) {

                return $this->respond(Response::STATUS_CODE_200, 'Entity deleted');
            }

            return $this->respond(Response::STATUS_CODE_404, 'Entity not found');

        } catch (\Exception $ex) {

            return $this->unknownError($ex);
        }
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
        return $this->respond(Response::STATUS_CODE_500, 'An unknown error occurred: ' . $ex->getMessage());
    }

    /**
     * Get the service
     *
     * @param string $name
     * @return object
     */
    public function getService($name = null)
    {
        $serviceFactory = $this->getServiceLocator()->get('serviceFactory');

        if (empty($name) ) {

            if (!empty($this->serviceName)) {

                $name = $this->serviceName;

            } else {

                $name = $this->getControllerName();
            }
        }

        if (!$this->serviceExists($name)) {

            return $serviceFactory->getService('Generic')->setEntityName('\OlcsEntities\Entity\\' . $name);
        }

        return $serviceFactory->getService($name);
    }

    /**
     * Check if a service exists
     *
     * @param string $serviceName
     *
     * @return boolean
     */
    public function serviceExists($serviceName)
    {
        $className = '\Olcs\Db\Service\\' . $serviceName;

        return class_exists($className);
    }

    /**
     * Set the service name
     *
     * @param string $name
     */
    public function setServiceName($name)
    {
        $this->serviceName = $name;
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
     * Get data from query
     *
     * @return array
     */
    public function getDataFromQuery()
    {
        $routeParams = $this->plugin('params')->fromRoute();
        $queryParams = $this->plugin('params')->fromQuery();

        return array_merge($routeParams, $queryParams);
    }

    /**
     * Check if the method is allowed
     *
     * @param string $method
     * @throws RestResponseException
     */
    public function checkMethod($method)
    {
        if (strstr($method, '::')) {
            list($controller, $method) = explode('::', $method);
        }

        if (!in_array($method, $this->allowedMethods)) {
            throw new RestResponseException('Method not allowed', Response::STATUS_CODE_405);
        }

        return true;
    }

    /**
     * Set allowed methods
     *
     * @param array $methods
     */
    public function setAllowedMethods(array $methods = array())
    {
        $this->allowedMethods = $methods;
    }
}
