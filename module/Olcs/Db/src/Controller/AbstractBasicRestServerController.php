<?php
namespace Olcs\Db\Controller;

use Olcs\Db\Utility\RestServerInterface as OlcsRestServerInterface;
use Zend\Http\Response;
use Olcs\Db\Exceptions\EntityTypeNotFoundException;
use Olcs\Db\Traits\RestResponseTrait;

abstract class AbstractBasicRestServerController extends AbstractController implements OlcsRestServerInterface
{
    use RestResponseTrait;

    /**
     * Create an entity
     *
     * @param mixed $data
     * @return Response
     */
    public function create($data)
    {
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

        } catch (EntityTypeNotFoundException $ex) {

            return $this->respond(Response::STATUS_CODE_400, $ex->getMessage() . ' Entity not found');

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
        try {
            $result = $this->getService()->get($id);

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
        $routeParams = $this->plugin('params')->fromRoute();
        $queryParams = $this->plugin('params')->fromQuery();

        $data = array_merge($routeParams, $queryParams);

        try {
            $result = $this->getService()->getList($data);

            if (empty($result)) {

                return $this->respond(Response::STATUS_CODE_200, 'No results found', $result);
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
        $data = $this->formatDataFromJson($data);

        if ($data instanceof Response) {

            return $data;
        }

        try {
            if ($this->getService()->update($id, $data)) {

                return $this->respond(Response::STATUS_CODE_200, 'Entity updated');
            }

            return $this->respond(Response::STATUS_CODE_404, 'Entity not found');

        } catch (\Exception $ex) {

            return $this->unknownError($ex);
        }
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
        $data = $this->formatDataFromJson($data);

        if ($data instanceof Response) {

            return $data;
        }

        try {
            if ($this->getService()->patch($id, $data)) {

                return $this->respond(Response::STATUS_CODE_200, 'Entity patched');
            }

            return $this->respond(Response::STATUS_CODE_404, 'Entity not found');

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
     *  We should try and catch all known exceptions and provide a reasonable response, if we get here the
     *   we have no idea what went wrong
     *
     * @param \Exception $ex
     * @return Response
     */
    private function unknownError($ex)
    {
        return $this->respond(Response::STATUS_CODE_500, 'An unknown error occurred: ' . $ex->getMessage());
    }

    public function getService()
    {
        return $this->getServiceLocator()->get($this->getControllerName());
    }

    /**
     * Format data from json
     *
     * @param mixed $data
     */
    private function formatDataFromJson($data)
    {
        $data = isset($data['data']) ? $data['data'] : $data;

        if (!is_string($data)) {

            return $this->respond(Response::STATUS_CODE_400, 'Expected JSON request data');
        }

        $data = json_decode($data, true);

        if (json_last_error() !== JSON_ERROR_NONE) {

            return $this->respond(Response::STATUS_CODE_400, 'JSON request data is invalid');
        }

        return $data;
    }
}
