<?php

/**
 * QueueController
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 * @note ported from Cli\Controller\QueueController
 */
namespace Dvsa\Olcs\Cli\Controller;

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
        // Which message type to process, if null then we process any message type
        $includeTypes = $this->getIncludeTypes();
        $excludeTypes = $this->getExcludeTypes();

        $this->getConsole()->writeLine('Types = '. implode(',', $includeTypes));
        $this->getConsole()->writeLine('Exclude types = '. implode(',', $excludeTypes));

        $config = $this->getServiceLocator()->get('Config')['queue'];

        /** @var \Dvsa\Olcs\Cli\Service\Queue\QueueProcessor $service */
        $service = $this->getServiceLocator()->get('Queue');

        // Then we need to run for a given length of time
        if (empty($config['isLongRunningProcess'])) {
            $this->startTime = microtime(true);
            $this->endTime = $this->startTime + $config['runFor'];
        }

        if (isset($config['sleepFor'])) {
            $this->sleepFor = $config['sleepFor'];
        }

        while ($this->shouldRunAgain()) {
            try {
                // process next item
                $response = $service->processNextItem($includeTypes, $excludeTypes);
            } catch (\Exception $e) {
                $this->getConsole()->writeLine('Error: '.$e->getMessage());
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
     * Get queue tyoe to include
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
     * Get queue tyoe to exclude
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
