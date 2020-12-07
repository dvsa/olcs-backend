<?php

namespace Dvsa\Olcs\Cli\Controller;

use Dvsa\Olcs\Api\Domain\Exception;
use Dvsa\Olcs\Api\Domain\Query;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Olcs\Logging\Log\Logger;
use Laminas\Mvc\Controller\AbstractConsoleController;
use Dvsa\Olcs\Cli\Domain\Query as CliQuery;
use Laminas\View\Model\ConsoleModel;

class UtilController extends AbstractConsoleController
{
    public function getDbValueAction()
    {
        $params = [
            'propertyName' => $this->params('property-name'),
            'entityName' => $this->params('entity-name'),
            'filterProperty' => $this->params('filter-property'),
            'filterValue' => $this->params('filter-value'),
        ];

        $dto = CliQuery\Util\GetDbValue::create($params);

        try {
            $result = $this->handleQuery($dto, true);
            $entity = $result->getObject();
            $getter = 'get' . ucwords($params['propertyName']);
            $output = $entity->$getter();
            if (is_object($output)) {
                $output = $output->getId();
            }
            $output = ["value"=>$output];
            $exitCode = 0;
        } catch (\Exception $e) {
            $exitCode = 1;
            $output = ["error" =>$e->getMessage()];
        }
        $output = json_encode($output);
        $this->writeMessages([PHP_EOL.'*** OUTPUT ***']);
        $output .= PHP_EOL. '*** END OF OUTPUT ***'.PHP_EOL;
        return $this->handleExitStatus($exitCode, (string) $output);
    }

    /**
     * Using this method ensures the calling CLI environment gets an appropriate
     * exit code from the process.
     *
     * @param int $result exit code, should be non-zero if there was an error
     *
     * @return \Laminas\View\Model\ConsoleModel
     */
    private function handleExitStatus($exitCode, $resultText = '')
    {
        $model = new ConsoleModel();
        $model->setErrorLevel($exitCode);
        $model->setResult($resultText);
        return $model;
    }

    /**
     * Handle DTO query
     *
     * @param QueryInterface $dto dto
     *
     * @return mixed $result|false
     */
    protected function handleQuery(QueryInterface $dto, $propagateException = false)
    {
        $result = false;
        
        try {
            $this->writeVerboseMessages("Handle query " . get_class($dto));
            $result = $this->getServiceLocator()->get('QueryHandlerManager')->handleQuery($dto);
        } catch (Exception\NotFoundException $e) {
            $this->writeVerboseMessages(['NotFoundException', $e->getMessage()], \Laminas\Log\Logger::WARN);
            if ($propagateException) {
                throw $e;
            }
        } catch (Exception\Exception $e) {
            $this->writeVerboseMessages($e->getMessages(), \Laminas\Log\Logger::ERR);
            if ($propagateException) {
                throw $e;
            }
        } catch (\Exception $e) {
            $this->writeVerboseMessages([$e->getMessage()], \Laminas\Log\Logger::ERR);
            if ($propagateException) {
                throw $e;
            }
        }

        return $result;
    }

    /**
     * Write verbose messages, ie only if verbose flag is set
     *
     * @param array|string $messages    Message to write
     * @param int          $logPriority One of \Laminas\Log\Logger::*
     *
     * @return void
     */
    protected function writeVerboseMessages($messages, $logPriority = \Laminas\Log\Logger::DEBUG)
    {
        if (!is_array($messages)) {
            $messages = [$messages];
        }
        if ($this->isVerbose()) {
            $this->writeMessages($messages);
        }
        Logger::log(
            $logPriority,
            json_encode($messages)
        );
    }

    /**
     * Is verbose
     *
     * @return boolean
     */
    private function isVerbose()
    {
        return $this->params('verbose') || $this->params('v');
    }

    /**
     * Write messages to the console
     *
     * @param array $messages Message to write
     *
     * @return void
     */
    protected function writeMessages($messages)
    {
        foreach ($messages as $message) {
            $this->getConsole()->writeLine((new \DateTime())->format(\DateTime::W3C) .' '. $message);
        }
    }
}
