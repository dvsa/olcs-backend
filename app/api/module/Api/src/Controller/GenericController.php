<?php

namespace Dvsa\Olcs\Api\Controller;

use Olcs\Logging\Log\Logger;
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
        } catch (Exception\RestResponseException $ex) {
            return $this->response()->error($ex->getCode(), $ex->getMessage());
        } catch (Exception\Exception $ex) {
            return $this->response()->error(400, $ex->getMessages());
        } catch (\Exception $ex) {
            Logger::logException($ex, \Zend\Log\Logger::ERR);
            return $this->response()->error(500, [$ex->getMessage()]);
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
            $countUnfiltered = isset($result['count-unfiltered']) ? $result['count-unfiltered'] : $count;
            unset($result['count'], $result['result'], $result['count-unfiltered']);

            return $this->response()->multipleResults($count, $results, $countUnfiltered, $result);

        } catch (Exception\NotFoundException $ex) {
            return $this->response()->notFound();
        } catch (Exception\NotReadyException $ex) {
            return $this->response()->notReady($ex->getRetryAfter());
        } catch (Exception\Exception $ex) {
            return $this->response()->error(400, $ex->getMessages());
        } catch (\Exception $ex) {
            Logger::logException($ex, \Zend\Log\Logger::ERR);
            return $this->response()->error(500, [$ex->getMessage()]);
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
        } catch (Exception\RestResponseException $ex) {
            return $this->response()->error($ex->getCode(), $ex->getMessage());
        } catch (Exception\Exception $ex) {
            return $this->response()->error(400, $ex->getMessages());
        } catch (\Exception $ex) {
            Logger::logException($ex, \Zend\Log\Logger::ERR);
            return $this->response()->error(500, [$ex->getMessage()]);
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
            Logger::logException($ex, \Zend\Log\Logger::ERR);
            return $this->response()->error(500, [$ex->getMessage()]);
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
        } catch (Exception\RestResponseException $ex) {
            return $this->response()->error($ex->getCode(), $ex->getMessages());
        } catch (Exception\Exception $ex) {
            return $this->response()->error(400, $ex->getMessages());
        } catch (\Exception $ex) {
            Logger::logException($ex, \Zend\Log\Logger::ERR);
            return $this->response()->error(500, [$ex->getMessage()]);
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
            Logger::logException($ex, \Zend\Log\Logger::ERR);
            return $this->response()->error(500, [$ex->getMessage()]);
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
            Logger::logException($ex, \Zend\Log\Logger::ERR);
            return $this->response()->error(500, [$ex->getMessage()]);
        }
    }

    /**
     * @return mixed
     */
    protected function handleQuery($dto)
    {
        return $this->getServiceLocator()->get('QueryHandlerManager')->handleQuery($dto);
    }

    /**
     * @param $dto
     * @return mixed
     */
    protected function handleCommand($dto)
    {
        return $this->getServiceLocator()->get('CommandHandlerManager')->handleCommand($dto);
    }
}
