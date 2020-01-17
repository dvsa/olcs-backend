<?php

namespace Dvsa\Olcs\Cli\Domain\CommandHandler\MessageQueue\Consumer;

use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Cli\Domain\CommandHandler\MessageQueue\Consumer\CompaniesHouse\CompanyProfile;
use Dvsa\Olcs\Queue\Exception\NoMessagesException;
use Dvsa\Olcs\Queue\Service\QueueInterface;
use Dvsa\Olcs\Queue\Service\QueueServiceTrait;

abstract class AbstractConsumer extends AbstractCommandHandler implements QueueInterface
{
    use QueueServiceTrait;

    const NOTHING_TO_PROCESS_MESSAGE = 'No messages to process';

    protected function fetchMessages(int $number): ?array
    {
        return $this->queueService->fetchMessages(
            $this->getQueueUrl(static::class),
            $number
        );
    }

    protected function getQueueUrl(string $class): string
    {
        return $this->queueConfig[$this->getQueueUrlKey($class)];
    }

    public function deleteMessage(array $message): void
    {
        $this->queueService->deleteMessage($this->getQueueUrl(static::class), $message['ReceiptHandle']);
    }

    public function noMessages(): void
    {
        $this->result->setFlag('no_messages', true);
        $this->result->addMessage(AbstractConsumer::NOTHING_TO_PROCESS_MESSAGE);
    }
}
