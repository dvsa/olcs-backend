<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\Permits;

use Dvsa\Olcs\Api\Domain\AuthAwareInterface;
use Dvsa\Olcs\Api\Domain\AuthAwareTrait;
use Dvsa\Olcs\Api\Domain\Command\Queue\Create as CreateQueue;
use Dvsa\Olcs\Api\Domain\Command\Permits\ProceedToStatus;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Domain\ConfigAwareInterface;
use Dvsa\Olcs\Api\Domain\ConfigAwareTrait;
use Dvsa\Olcs\Api\Domain\Exception\ValidationException;
use Dvsa\Olcs\Api\Domain\ToggleAwareTrait;
use Dvsa\Olcs\Api\Domain\ToggleRequiredInterface;
use Dvsa\Olcs\Api\Entity\System\FeatureToggle;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermit as IrhpPermitEntity;
use Dvsa\Olcs\Api\Entity\Queue\Queue;
use Dvsa\Olcs\Transfer\Command\CommandInterface;

/**
 * PrintPermits
 */
final class PrintPermits extends AbstractCommandHandler implements
    AuthAwareInterface,
    ConfigAwareInterface,
    TransactionedInterface,
    ToggleRequiredInterface
{
    use AuthAwareTrait, ConfigAwareTrait, ToggleAwareTrait;

    protected $toggleConfig = [FeatureToggle::BACKEND_ECMT];

    protected $repoServiceName = 'Queue';

    const MAX_BATCH_SIZE = 100;
    const ERR_MAX_BATCH_SIZE_REACHED = 'ERR_PERMIT_PRINTING_MAX_BATCH_SIZE_REACHED';
    const ERR_ALREADY_IN_PROGRESS = 'ERR_PERMIT_PRINTING_ALREADY_IN_PROGRESS';

    /**
     * @param CommandInterface $command
     * @return \Dvsa\Olcs\Api\Domain\Command\Result
     */
    public function handleCommand(CommandInterface $command)
    {
        $ids = $command->getIds();

        $config = $this->getConfig();
        $maxBatchSize = isset($config['permit_printing']['max_batch_size'])
            && is_numeric($config['permit_printing']['max_batch_size'])
            ? $config['permit_printing']['max_batch_size']
            : self::MAX_BATCH_SIZE;

        // check the number of selected permits
        if (sizeof($ids) > $maxBatchSize) {
            throw new ValidationException([self::ERR_MAX_BATCH_SIZE_REACHED]);
        }

        // check if the message is already in the queue
        if ($this->getRepo('Queue')->isItemInQueue(
            [Queue::TYPE_PERMIT_GENERATE, Queue::TYPE_PERMIT_PRINT],
            [Queue::STATUS_QUEUED, Queue::STATUS_PROCESSING]
        )) {
            throw new ValidationException([self::ERR_ALREADY_IN_PROGRESS]);
        }

        // update status of permits
        $this->result->merge(
            $this->handleSideEffect(
                ProceedToStatus::create(
                    [
                        'ids' => $ids,
                        'status' => IrhpPermitEntity::STATUS_AWAITING_PRINTING,
                    ]
                )
            )
        );

        // queue the request
        $data = [
            'ids' => $ids,
            'user' => $this->getCurrentUser()->getId()
        ];
        $params = [
            'type' => Queue::TYPE_PERMIT_GENERATE,
            'status' => Queue::STATUS_QUEUED,
            'options' => json_encode($data)
        ];
        $this->result->merge($this->handleSideEffect(CreateQueue::create($params)));

        $this->result->addMessage('Permits submitted for printing');

        return $this->result;
    }
}
