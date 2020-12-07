<?php

/**
 * QueueController
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 * @note ported from Cli\Controller\QueueController
 */
namespace Dvsa\Olcs\Cli\Controller;

use Doctrine\ORM\ORMException;
use Olcs\Logging\Log\Logger;
use Laminas\View\Model\ConsoleModel;

/**
 * QueueController
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 * @note ported from Cli\Controller\QueueController
 */
class QueueController extends AbstractQueueController
{
    /**
     * Index Action
     *
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
            } catch (ORMException $e) {
                return $this->handleORMException($e);
            } catch (\Exception $e) {
                $content = 'Error: '.$e->getMessage();

                Logger::log(
                    \Laminas\Log\Logger::ERR,
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
}
