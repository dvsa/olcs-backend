<?php

/**
 * SQSController
 */

namespace Dvsa\Olcs\Cli\Controller;

use Dvsa\Olcs\Cli\Domain\Command\MessageQueue\Consumer\CompaniesHouse\CompanyProfile;
use Dvsa\Olcs\Cli\Domain\Command\MessageQueue\Consumer\CompaniesHouse\ProcessInsolvency;

/**
 * SQSController
 *
 */
class SQSController extends AbstractCliController
{
    const VALID_QUEUES = [
        'companyProfile',
        'processInsolvency'
    ];

    /**
     * Index Action
     *
     * @return \Zend\View\Model\ConsoleModel
     */
    public function indexAction()
    {
        try {
            $queue = $this->getQueue();
            $command = $this->$queue();
            return $this->handleExitStatus(
                $this->handleCommand(
                    [
                        $command
                    ]
                )
            );
        } catch (\Exception $exception) {
            $this->writeVerboseMessages($exception->getMessage(), \Zend\Log\Logger::ERR);
            return $this->handleExitStatus(400);
        }
    }

    /**
     * Get queue types to includeE
     *
     * @return array
     */
    private function getQueue()
    {
        $queue = $this->getRequest()->getParam('queue');
        if (!$this->isValidQueue($queue)) {
            throw new \InvalidArgumentException(
                $queue . ' is not a valid SQS queue. Options are ['
                . implode(', ', static::VALID_QUEUES) . "]"
            );
        }

        return $queue;
    }


    private function isValidQueue(string $queue)
    {
        return in_array($queue, static::VALID_QUEUES);
    }

    private function companyProfile()
    {
        return CompanyProfile::create([]);
    }

    private function processInsolvency()
    {
        return ProcessInsolvency::create([]);
    }
}
