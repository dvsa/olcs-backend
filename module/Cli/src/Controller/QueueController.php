<?php

/**
 * QueueController
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 * @note ported from Cli\Controller\QueueController
 */
namespace Dvsa\Olcs\Cli\Controller;

use Zend\Mvc\Controller\AbstractConsoleController;

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
        $type = $this->getRequest()->getParam('type');

        $config = $this->getServiceLocator()->get('Config')['queue'];

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
                $response = $service->processNextItem($type);
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
