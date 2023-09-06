<?php

/**
 * SQSController
 */

namespace Dvsa\Olcs\Cli\Controller;

use Doctrine\ORM\ORMException;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandlerManager;
use Dvsa\Olcs\Cli\Domain\Command\MessageQueue\Consumer\CompaniesHouse\CompanyProfile;
use Dvsa\Olcs\Cli\Domain\Command\MessageQueue\Consumer\CompaniesHouse\ProcessInsolvency;
use Dvsa\Olcs\Cli\Domain\Command\MessageQueue\Consumer\CompaniesHouse\ProcessInsolvencyDlq;
use Dvsa\Olcs\Cli\Domain\Command\MessageQueue\Consumer\CompaniesHouse\CompanyProfileDlq;
use Dvsa\Olcs\Cli\Domain\Command\MessageQueue\Consumer\TransXChange\TransXChangeConsumer;
use Olcs\Logging\Log\Logger;
use Laminas\Mvc\Console\View\ViewModel as ConsoleModel;

/**
 * SQSController
 *
 */
class SQSController extends AbstractQueueController
{
    private const VALID_QUEUE_TYPES = [
        'companyProfile',
        'processInsolvency',
        'processInsolvencyDlq',
        'companyProfileDlq',
        'transXChangeConsumer',
    ];

    private CommandHandlerManager $commandHandlerManager;

    private array $config;

    /**
     * @param array $config
     * @param CommandHandlerManager $commandHandlerManager
     */
    public function __construct(
        array $config,
        CommandHandlerManager $commandHandlerManager
    ) {
        $this->config = $config;
        $this->commandHandlerManager = $commandHandlerManager;
    }

    /**
     * Index Action
     *
     * @return \Laminas\Mvc\Console\View\ViewModel
     */
    public function indexAction()
    {
        $config = $this->config['queue'];
        $queueDuration = $this->getQueueDuration($config);
        $queueType = $this->getQueueType();
        $this->startTime = microtime(true);
        $this->endTime = $this->startTime + $queueDuration;

        $this->getConsole()->writeLine('Queue type = ' . $queueType);
        $this->getConsole()->writeLine('Queue duration = ' . $queueDuration);

        if (isset($config['sleepFor'])) {
            $this->sleepFor = $config['sleepFor'];
        }

        while ($this->shouldRunAgain()) {
            try {
                $command = $this->$queueType();
                $response = $this->handleSingleCommand($command);
            } catch (ORMException $exception) {
                return $this->handleORMException($exception);
            } catch (\Exception $exception) {
                $content = 'Error: ' . $exception->getMessage();
                $this->getConsole()->writeLine($content);
                Logger::log(
                    \Laminas\Log\Logger::ERR,
                    'Failed to process next item in the queue',
                    ['errorLevel' => 1, 'content' => $content]
                );
                continue;
            }

            if ($response->getFlag('no_messages')) {
                $this->getConsole()->writeLine('No messages queued, waiting for messages');
                usleep($this->sleepFor);
            } else {
                $message = "Processed message: " . implode(" . ", array_values($response->getMessages()));
                $this->getConsole()->writeLine($message);
            }
        }

        $model = new ConsoleModel();
        $model->setErrorLevel(0);
        return $model;
    }

    /**
     * Get queue types to includeE
     *
     * @return array
     */
    private function getQueueType()
    {
        $queue = $this->getRequest()->getParam('queue');
        if (!$this->isValidQueueType($queue)) {
            throw new \InvalidArgumentException(
                $queue . ' is not a valid SQS queue. Options are ['
                . implode(', ', static::VALID_QUEUE_TYPES) . "]"
            );
        }

        return $queue;
    }

    private function isValidQueueType(string $queue)
    {
        return in_array($queue, static::VALID_QUEUE_TYPES);
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
     */
    private function companyProfile(): CompanyProfile
    {
        return CompanyProfile::create([]);
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
     */
    private function processInsolvency(): ProcessInsolvency
    {
        return ProcessInsolvency::create([]);
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
     */
    private function processInsolvencyDlq(): ProcessInsolvencyDlq
    {
        return ProcessInsolvencyDlq::create([]);
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
     */
    private function companyProfileDlq(): CompanyProfileDlq
    {
        return CompanyProfileDlq::create([]);
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
     */
    private function transXChangeConsumer(): TransXChangeConsumer
    {
        return TransXChangeConsumer::create([]);
    }

    protected function handleSingleCommand($dto): Result
    {
        return $this->commandHandlerManager->handleCommand($dto);
    }
}
