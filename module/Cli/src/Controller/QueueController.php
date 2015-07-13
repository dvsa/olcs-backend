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

    public function indexAction()
    {
        // Which message type to process, if null then we process any message type
        $type = $this->getRequest()->getParam('type');

        $config = $this->getServiceLocator()->get('Config')['queue'];

        $service = $this->getServiceLocator()->get('Queue');

        // Then we need to run for a given length of time
        if (empty($config['isLongRunningProcess'])) {
            $this->startTime = time();
            $this->endTime = $this->startTime + $config['runFor'];
        }

        while ($this->shouldRunAgain()) {

            $response = $service->processNextItem($type);

            if ($response === null) {
                $this->getConsole()->writeLine('No items queued, waiting for items');
                sleep(1);
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
            return time() < $this->endTime;
        }

        return true;
    }
}
