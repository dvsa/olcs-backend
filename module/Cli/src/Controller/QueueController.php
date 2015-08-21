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
use Dvsa\Olcs\Api\Domain\Exception;

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

            $response = $service->processNextItem($type);

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
