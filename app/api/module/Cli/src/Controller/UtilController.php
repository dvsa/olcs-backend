<?php

namespace Dvsa\Olcs\Cli\Controller;

use Dvsa\Olcs\Api\Domain\Command;
use Dvsa\Olcs\Api\Domain\Exception;
use Dvsa\Olcs\Api\Domain\Query;
use Dvsa\Olcs\Api\Domain\Util\DateTime\DateTime;
use Dvsa\Olcs\Cli\Domain\Command as CliCommand;
use Dvsa\Olcs\Transfer\Command as TransferCommand;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Olcs\Logging\Log\Logger;
use Zend\Http\Response;
use Zend\Mvc\Controller\AbstractConsoleController;
use Dvsa\Olcs\Cli\Domain\Query as CliQuery;
use Zend\View\Model\ConsoleModel;

class UtilController extends AbstractConsoleController
{
    public function getDbValueAction()
    {
        $params = [
            'columnName' =>  $this->params('column-name'),
            'tableName' => $this->params('table-name'),
            'filterName' => $this->params('filter-name'),
            'filterValue' => $this->params('filter-value'),
        ];

        $dto = CliQuery\Util\getDbValue::create($params);

        $result = $this->handleQuery($dto);

        return $this->handleExitStatus($result);

    }

    /**
     * Using this method ensures the calling CLI environment gets an appropriate
     * exit code from the process.
     *
     * @param int $result exit code, should be non-zero if there was an error
     *
     * @return \Zend\View\Model\ConsoleModel
     */
    private function handleExitStatus($result)
    {
        $model = new ConsoleModel();
        $model->setErrorLevel($result);
        return $model;
    }

    /**
     * Handle DTO query
     *
     * @param QueryInterface $dto dto
     *
     * @return mixed $result|false
     */
    protected function handleQuery(QueryInterface $dto)
    {
        try {
            $this->writeVerboseMessages("Handle query ". get_class($dto));
            return $this->getServiceLocator()->get('QueryHandlerManager')->handleQuery($dto);
        } catch (Exception\NotFoundException $e) {
            $this->writeVerboseMessages(['NotFoundException', $e->getMessage()], \Zend\Log\Logger::WARN);
        } catch (Exception\Exception $e) {
            $this->writeVerboseMessages($e->getMessages(), \Zend\Log\Logger::ERR);
        } catch (\Exception $e) {
            $this->writeVerboseMessages([$e->getMessage()], \Zend\Log\Logger::ERR);
        }

        return false;
    }

}