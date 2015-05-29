<?php

namespace Dvsa\Olcs\Api\Controller;

use Zend\Mvc\Controller\AbstractRestfulController;
use Dvsa\Olcs\Api\Domain\Exception;

/**
 * Generic Controller
 * @method \Dvsa\Olcs\Api\Mvc\Controller\Plugin\Response response()
 */
class GenericController extends AbstractRestfulController
{
    public function get($id)
    {
        try {
            $result = $this->handleQuery();
            return $this->response()->singleResult($result);
        } catch (Exception\NotFoundException $ex) {
            return $this->response()->notFound();
        } catch (Exception\Exception $ex) {
            return $this->response()->error(400, $ex->getMessages());
        } catch (\Exception $ex) {
            return $this->response()->error(500, [$ex->getMessage(), explode('#', $ex->getTraceAsString())]);
        }
    }

    public function getList()
    {
        try {
            $result = $this->handleQuery();
            return $this->response()->multipleResults($result['count'], $result['result']);
        } catch (Exception\NotFoundException $ex) {
            return $this->response()->notFound();
        } catch (Exception\Exception $ex) {
            return $this->response()->error(400, $ex->getMessages());
        } catch (\Exception $ex) {
            return $this->response()->error(500, [$ex->getMessage(), explode('#', $ex->getTraceAsString())]);
        }
    }

    public function update($id, $data)
    {
        $dto = $this->params('dto');

        try {
            $result = $this->handleCommand($dto);
            return $this->response()->successfulUpdate($result);
        } catch (Exception\NotFoundException $ex) {
            return $this->response()->notFound();
        } catch (Exception\Exception $ex) {
            return $this->response()->error(400, $ex->getMessages());
        } catch (\Exception $ex) {
            return $this->response()->error(500, [$ex->getMessage(), explode('#', $ex->getTraceAsString())]);
        }
    }

    public function create($data)
    {
        $dto = $this->params('dto');

        try {
            $result = $this->handleCommand($dto);
            return $this->response()->successfulCreate($result);
        } catch (Exception\Exception $ex) {
            return $this->response()->error(400, $ex->getMessages());
        } catch (\Exception $ex) {
            return $this->response()->error(500, [$ex->getMessage()]);
        }
    }

    public function delete($id)
    {
        $dto = $this->params('dto');

        try {
            $result = $this->handleCommand($dto);
            return $this->response()->successfulUpdate($result);
        } catch (Exception\NotFoundException $ex) {
            return $this->response()->notFound();
        } catch (Exception\Exception $ex) {
            return $this->response()->error(400, $ex->getMessages());
        } catch (\Exception $ex) {
            return $this->response()->error(500, [$ex->getMessage(), explode('#', $ex->getTraceAsString())]);
        }
    }

    /**
     * @return mixed
     */
    protected function handleQuery()
    {
        $result = $this->getServiceLocator()->get('QueryHandlerManager')->handleQuery($this->params('dto'));
        return $result;
    }

    /**
     * @param $dto
     * @return mixed
     */
    protected function handleCommand($dto)
    {
        $result = $this->getServiceLocator()->get('CommandHandlerManager')->handleCommand($dto);
        return $result;
    }
}
