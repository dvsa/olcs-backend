<?php

/**
 * AbstractBasicRestServerController
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */

namespace Olcs\Db\Controller;

use Olcs\Db\Utility\RestServerInterface as OlcsRestServerInterface;
use Zend\Http\Response;
use Olcs\Db\Exceptions\RestResponseException;
use Olcs\Db\Traits\RestResponseTrait;
use Olcs\Db\Exceptions\NoVersionException;
use Doctrine\ORM\OptimisticLockException;

/**
 * AbstractBasicRestServerController
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
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
        'delete',
        'deleteList'
    );

    /**
     * Create an entity
     *
     * @param mixed $data Data
     *
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
            $multiple = (isset($data['_OPTIONS_']['multiple']) && $data['_OPTIONS_']['multiple']);

            if ($multiple) {
                $postData = $data;
                unset($postData['_OPTIONS_']);
            } else {
                $postData = array($data);
            }

            $id = array();

            foreach ($postData as $data) {
                $id[] = $this->getService()->create($data);
            }

            if (!$multiple) {
                $id = $id[0];
            }

            if ((is_numeric($id) && $id > 0) || is_array($id)) {
                return $this->respond(Response::STATUS_CODE_201, 'Entity Created', array('id' => $id));
            }

            throw new \Exception();
        } catch (\Exception $ex) {
            return $this->unknownError($ex);
        }
    }

    /**
     * Get an entity by its id
     *
     * @param int $id ID of record to retrieve
     *
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
     * @param int   $id   ID
     * @param mixed $data Data to update
     *
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
     * @param int   $id   ID
     * @param mixed $data data to patch
     *
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
     * @param int    $id     ID
     * @param mixed  $data   Data to update or patch
     * @param string $method Method for either update or patch
     *
     * @return Response
     */
    protected function updateOrPatch($id, $data, $method)
    {
        $data = $this->formatDataFromJson($data);

        if ($data instanceof Response) {
            return $data;
        }

        try {
            $success = true;
            $multiple = (isset($data['_OPTIONS_']['multiple']) && $data['_OPTIONS_']['multiple']);

            if ($multiple) {
                $postData = $data;
                unset($postData['_OPTIONS_']);

                foreach ($postData as $data) {
                    $id = $data['id'];
                    unset($data['id']);

                    $response = $this->getService()->$method($id, $data);

                    if (!$response) {
                        $success = false;
                    }
                }
            } else {
                $success = $this->getService()->$method($id, $data);
            }

            if ($success) {
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
     * @param int $id ID to remove
     *
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
     * Delete a list of records
     *
     * @param mixed $data Parameters for removing a record
     *
     * @return Response
     */
    public function deleteList($data)
    {
        $this->checkMethod(__METHOD__);

        $data = $this->getDataFromQuery();

        try {
            if ($this->getService()->deleteList($data)) {
                return $this->respond(Response::STATUS_CODE_200, 'Entity deleted');
            }
            return $this->respond(Response::STATUS_CODE_404, 'Entity not found');
        } catch (\Exception $ex) {
            return $this->unknownError($ex);
        }
    }

    /**
     * Get the service
     *
     * @param string $name Name of service to retrieve
     *
     * @return \Olcs\Db\Service\ServiceAbstract
     */
    public function getService($name = null)
    {
        $serviceFactory = $this->getServiceLocator()->get('serviceFactory');

        if (empty($name)) {
            if (!empty($this->serviceName)) {
                $name = $this->serviceName;
            } else {
                $name = $this->getControllerName();
            }
        }

        if (!$this->serviceExists($name)) {
            $entityName = $this->findEntityClass($name);
            $service = $serviceFactory->getService('Generic');
            $service->setEntityName($entityName);
            $service->setLanguage($this->getLanguageFromHeader());
            return $service;
        }

        $service = $serviceFactory->getService($name);
        $service->setLanguage($this->getLanguageFromHeader());

        return $service;
    }

    /**
     * Get language header
     *
     * @return mixed|void
     */
    protected function getLanguageFromHeader()
    {
        $header = $this->getEvent()->getRequest()->getHeaders('accept-language');
        if ($header) {
            return $header->getFieldValue();
        }
    }

    /**
     * Find entity class
     *
     * @param string $name Name of entity
     *
     * @return string|void
     */
    private function findEntityClass($name)
    {
        $namespaces = array(
            '\Dvsa\Olcs\Api\Entity\View\\',
            '\Dvsa\Olcs\Api\Entity\\'
        );

        $entityNamespaces = $this->getServiceLocator()->get('Config')['entity_namespaces'];

        foreach ($namespaces as $namespace) {
            if (isset($entityNamespaces[$name])) {
                $name = $entityNamespaces[$name] . '\\' . $name;
            }

            $potentialClassName = $namespace . $name;

            if (class_exists($potentialClassName)) {
                return $potentialClassName;
            }
        }
    }

    /**
     * Check if a service exists
     *
     * @param string $serviceName Check if service exists
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
     * @param string $name Name of service to set
     *
     * @return void
     */
    public function setServiceName($name)
    {
        $this->serviceName = $name;
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
     * @param string $method Method to check
     *
     * @return bool
     * @throws RestResponseException
     */
    public function checkMethod($method)
    {
        if (strstr($method, '::')) {
            $parts = explode('::', $method);
            $method = array_pop($parts);
        }

        if (!in_array($method, $this->allowedMethods)) {
            throw new RestResponseException('Method not allowed', Response::STATUS_CODE_405);
        }

        return true;
    }

    /**
     * Set allowed methods
     *
     * @param array $methods Methods to allow
     *
     * @return void
     */
    public function setAllowedMethods(array $methods = [])
    {
        $this->allowedMethods = $methods;
    }
}
