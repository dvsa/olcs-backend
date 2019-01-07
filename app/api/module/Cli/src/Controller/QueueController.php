<?php

/**
 * QueueController
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 * @note ported from Cli\Controller\QueueController
 */
namespace Dvsa\Olcs\Cli\Controller;

use Olcs\Logging\Log\Logger;
use Zend\Mvc\Controller\AbstractConsoleController;
use Zend\View\Model\ConsoleModel;

/**
 * QueueController
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 * @note ported from Cli\Controller\QueueController
 */
class QueueController extends AbstractConsoleController
{
    const DEFAULT_RUN_FOR = 60;

    protected $startTime;
    protected $endTime;
    protected $sleepFor = 1000000; // microseconds

    /**
     * Index Action
     *
     * @return void
     */
    public function indexAction()
    {
        $config = $this->getServiceLocator()->get('Config')['queue'];

        // Which message type to process, if null then we process any message type
        $includeTypes = $this->getIncludeTypes();
        $excludeTypes = $this->getExcludeTypes();
        $queueDuration = $this->getQueueDuration($config);

        $this->getConsole()->writeLine('Types = '. implode(',', $includeTypes));
        $this->getConsole()->writeLine('Exclude types = '. implode(',', $excludeTypes));
        $this->getConsole()->writeLine('Queue duration = '. $queueDuration);

        /** @var \Dvsa\Olcs\Cli\Service\Queue\QueueProcessor $service */
        $service = $this->getServiceLocator()->get('Queue');

        // Then we need to run for a given length of time
        if (empty($config['isLongRunningProcess'])) {
            $this->startTime = microtime(true);
            $this->endTime = $this->startTime + $queueDuration;
        }

        if (isset($config['sleepFor'])) {
            $this->sleepFor = $config['sleepFor'];
        }

        while ($this->shouldRunAgain()) {
            try {
                // process next item
                $response = $service->processNextItem($includeTypes, $excludeTypes);
            } catch (\Doctrine\ORM\ORMException $e) {
                // If ORMException such as "Entity Manager Closed" exit script as they will fail
                $content = 'ORM Error: '.$e->getMessage();

                Logger::log(
                    \Zend\Log\Logger::ERR,
                    'Failed to process next item in the queue',
                    ['errorLevel' => 1, 'content' => $content]
                );

                $this->getConsole()->writeLine($content);
                $model = new ConsoleModel();
                $model->setErrorLevel(1);
                return $model;
            } catch (\Exception $e) {
                $content = 'Error: '.$e->getMessage();

                Logger::log(
                    \Zend\Log\Logger::ERR,
                    'Failed to process next item in the queue',
                    ['errorLevel' => 1, 'content' => $content]
                );

                $this->getConsole()->writeLine($content);
                // continue with the next item
                continue;
            }

            if ($response === null) {
                $this->getConsole()->writeLine('No items queued, waiting for items');
                usleep($this->sleepFor);
            } else {
                $this->getConsole()->writeLine($response);
            }
        }

        $model = new ConsoleModel();
        $model->setErrorLevel(0);
        return $model;
    }

    /**
     * Get queue types to include
     *
     * @return array
     */
    private function getIncludeTypes()
    {
        return $this->getRequest()->getParam('type') ?
            explode(',', $this->getRequest()->getParam('type')) :
            [];
    }

    /**
     * Get queue types to exclude
     *
     * @return array
     */
    private function getExcludeTypes()
    {
        return $this->getRequest()->getParam('exclude') ?
            explode(',', $this->getRequest()->getParam('exclude')) :
            [];
    }

    /**
     * Get queue duration
     *
     * @param array $config Condig
     *
     * @return number Queue duration in seconds
     */
    private function getQueueDuration($config)
    {
        // get default from config, if not in config then default to self::gsDEFAULT_RUN_FOR
        $default = isset($config['runFor']) && is_numeric($config['runFor']) ?
            $config['runFor'] :
            self::DEFAULT_RUN_FOR;
        return $this->getRequest()->getParam('queue-duration', $default);
    }

    /**
     * Decide whether to run again based on config settings and time elapsed
     *
     * @return boolean
     */
    protected function shouldRunAgain()
    {
        if (isset($this->endTime)) {
            return microtime(true) < $this->endTime;
        }

        return true;
    }
}
