<?php
namespace Olcs\Db\Controller;

use Zend\View\Model\JsonModel;
use Olcs\Db\Utility\RestServerInterface as OlcsRestServerInterface;
use Zend\Http\Response;
use Olcs\Db\Exceptions\EntityTypeNotFoundException;
use Olcs\Db\Entity\AbstractEntity;

abstract class AbstractBasicRestServerController extends AbstractController implements OlcsRestServerInterface
{
    /**
     * Create an entity
     *
     * @param mixed $data
     * @return Response
     */
    public function create($data)
    {
        // Find the data
        $data = isset($data['data']) ? $data['data'] : $data;

        // We are expecting a JSON string
        if (!is_string($data)) {

            return $this->respond(Response::STATUS_CODE_400, 'Expected JSON request data');
        }

        // Attempt to decode the json
        $data = json_decode($data, true);

        // If we can't decode the json
        if (json_last_error() !== JSON_ERROR_NONE) {

            return $this->respond(Response::STATUS_CODE_400, 'JSON request data is invalid');
        }

        // Try to create the entity
        try {

            $id = $this->getService()->create($data);

            if (is_numeric($id) && $id > 0) {

                return $this->respond(Response::STATUS_CODE_201, 'Entity Created', array('id' => $id));
            }

            throw new \Exception();

        } catch (EntityTypeNotFoundException $ex) {

            return $this->respond(Response::STATUS_CODE_400, $ex->getMessage() . ' Entity not found');

        } catch (\Exception $ex) {
            /**
             *  We should try and catch all known exceptions and provide a reasonable response, if we get here the
             *   we have no idea what went wrong
             */
            return $this->respond(Response::STATUS_CODE_500, 'An unknown error occurred');
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
        // Try to get the entity
        try {

            $result = $this->getService()->get($id);

            // If we haven't found an entity
            if (empty($result) || !($result instanceof AbstractEntity)) {

                return $this->respond(Response::STATUS_CODE_404, 'Entity not found');
            }

        } catch (\Exception $ex) {
            /**
             *  We should try and catch all known exceptions and provide a reasonable response, if we get here the
             *   we have no idea what went wrong
             */
            return $this->respond(Response::STATUS_CODE_500, 'An unknown error occurred');
        }
    }

    public function getList()
    {
        $routeParams = $this->plugin('params')->fromRoute();
        $queryParams = $this->plugin('params')->fromQuery();

        $data = array_merge($routeParams, $queryParams);

        $result = $this->getService()->getList($data);

        return new JsonModel(array('data' => $result));
    }

    public function update($id, $data)
    {
        $result = $this->getService()->update($data);

        return new JsonModel(array('result' => $result));
    }

    public function patch($id, $data)
    {
        $result = $this->getService()->patch($data);

        return new JsonModel(array('result' => $result));
    }

    public function delete($id)
    {
        $result = $this->getService()->delete($id);

        return new JsonModel(array('result' => $result));
    }

    public function getService()
    {
        return $this->getServiceLocator()->get($this->getControllerName());
    }

    /**
     * Creates a response object and set's up the response body
     *
     * @param int $errorCode
     * @param string $summary
     * @param array $data
     * @return Response
     */
    private function respond($errorCode, $summary = null, $data = array())
    {
        $response = new Response();

        $response->setStatusCode($errorCode);

        $response->setContent(
            json_encode(
                array(
                    'Response' => array(
                        'Code' => $errorCode,
                        'Message' => $response->getReasonPhrase(),
                        'Summary' => $summary,
                        'Data' => $data
                    )
                )
            )
        );

        return $response;
    }
}
