<?php

namespace Dvsa\Olcs\Queue\Service;

use Dvsa\Olcs\Queue\Service\Message\MessageBuilder;

trait QueueServiceTrait
{
    /**
     * @var Queue
     */
    protected $queueService;

    /**
     * @var array
     */
    protected $queueConfig;

    /**
     * @var MessageBuilder
     */
    protected $messageBuilderService;

    /**
     * @param mixed $messageBuilderService
     */
    public function setMessageBuilderService(MessageBuilder $messageBuilderService): void
    {
        if ($this->messageBuilderService === null) {
            $this->messageBuilderService = $messageBuilderService;
        }
    }

    public function setQueueService(Queue $queueService): void
    {
        if ($this->queueService === null) {
            $this->queueService = $queueService;
        }
    }

    public function setQueueConfig(array $config): void
    {
        if ($this->queueConfig === null) {
            $this->queueConfig = $config;
        }
    }

    protected function getQueueUrlKey($messageType): string
    {
        $path = explode('\\', (string) $messageType);
        $path = array_pop($path);

        return $path . '_URL';
    }
}
