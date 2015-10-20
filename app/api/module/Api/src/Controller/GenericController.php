<?php

namespace Dvsa\Olcs\Api\Controller;

use Zend\Log\Logger;
use Zend\Mvc\Controller\AbstractRestfulController;
use Dvsa\Olcs\Api\Domain\Exception;
use Dvsa\Olcs\Api\Domain\QueryHandler\Result;

/**
 * Generic Controller
 * @method \Dvsa\Olcs\Api\Mvc\Controller\Plugin\Response response()
 */
class GenericController extends AbstractRestfulController
{
    /**
     * @inheritdoc
     */
    public function get($id)
    {
        $dto = $this->params('dto');

        try {
            $result = $this->handleQuery($dto);
            return $this->response()->singleResult($result);
        } catch (Exception\NotFoundException $ex) {
            return $this->response()->notFound();
        } catch (Exception\NotReadyException $ex) {
            return $this->response()->notReady($ex->getRetryAfter());
        } catch (Exception\Exception $ex) {
            return $this->response()->error(400, $ex->getMessages());
        } catch (\Exception $ex) {
            return $this->response()->error(500, [$ex->getMessage(), explode('#', $ex->getTraceAsString())]);
        }
    }

    public function getList()
    {
        $dto = $this->params('dto');

        try {
            $result = $this->handleQuery($dto);

            if ($result instanceof Result || !isset($result['result'])) {
                // we sometimes still get a single result if we're not retrieving by id
                return $this->response()->singleResult($result);
            }

            $count = $result['count'];
            $results = $result['result'];
            unset($result['count'], $result['result']);

            return $this->response()->multipleResults($count, $results, $result);

        } catch (Exception\NotFoundException $ex) {
            return $this->response()->notFound();
        } catch (Exception\NotReadyException $ex) {
            return $this->response()->notReady($ex->getRetryAfter());
        } catch (Exception\Exception $ex) {
            return $this->response()->error(400, $ex->getMessages());
        } catch (\Exception $ex) {
            return $this->response()->error(500, [$ex->getMessage(), explode('#', $ex->getTraceAsString())]);
        }
    }

    /**
     * @inheritdoc
     */
    public function update($id, $data)
    {
        $dto = $this->params('dto');

        try {
            $result = $this->handleCommand($dto);
            return $this->response()->successfulUpdate($result);
        } catch (Exception\VersionConflictException $ex) {
            return $this->response()->error(409, $ex->getMessages());
        } catch (Exception\NotFoundException $ex) {
            return $this->response()->notFound();
        } catch (Exception\Exception $ex) {
            return $this->response()->error(400, $ex->getMessages());
        } catch (\Exception $ex) {
            return $this->response()->error(500, [$ex->getMessage(), explode('#', $ex->getTraceAsString())]);
        }
    }

    /**
     * @inheritdoc
     */
    public function replaceList($data)
    {
        $dto = $this->params('dto');

        try {
            $result = $this->handleCommand($dto);
            return $this->response()->successfulUpdate($result);
        } catch (Exception\VersionConflictException  $ex) {
            return $this->response()->error(409, $ex->getMessages());
        } catch (Exception\NotFoundException $ex) {
            return $this->response()->notFound();
        } catch (Exception\Exception $ex) {
            return $this->response()->error(400, $ex->getMessages());
        } catch (\Exception $ex) {
            return $this->response()->error(500, [$ex->getMessage(), explode('#', $ex->getTraceAsString())]);
        }
    }

    /**
     * @inheritdoc
     */
    public function create($data)
    {
        $dto = $this->params('dto');

        try {
            $result = $this->handleCommand($dto);
            return $this->response()->successfulCreate($result);
        } catch (Exception\Exception $ex) {
            return $this->response()->error(400, $ex->getMessages());
        } catch (\Exception $ex) {
            return $this->response()->error(500, [$ex->getMessage(), explode('#', $ex->getTraceAsString())]);
        }
    }

    /**
     * @inheritdoc
     */
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

    public function deleteList()
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
    protected function handleQuery($dto)
    {
        $result = $this->getServiceLocator()->get('QueryHandlerManager')->handleQuery($dto);
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
