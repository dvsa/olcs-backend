<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\Application;

use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Domain\Util\DateTime\DateTime;
use Dvsa\Olcs\Api\Entity\Fee\Fee;
use Dvsa\Olcs\Api\Entity\Fee\FeeType;
use Dvsa\Olcs\Api\Domain\Command\Queue\Create;
use Dvsa\Olcs\Api\Entity\Queue\Queue;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Entity\Application\Application as ApplicationEntity;
use Dvsa\Olcs\Transfer\Command\Application\RefuseInterim as Cmd;
use Dvsa\Olcs\Api\Domain\Command\Result;

/**
 * Refuse Interim
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
final class RefuseInterim extends AbstractCommandHandler implements TransactionedInterface
{
    protected $repoServiceName = 'Application';

    /**
     * Handle command
     *
     * @param Cmd $command command
     *
     * @return Result
     */
    public function handleCommand(CommandInterface $command)
    {
        /** @var ApplicationEntity $application */
        $application = $this->getRepo()->fetchUsingId($command);
        
        $status = $this->getRepo()->getRefdataReference(ApplicationEntity::INTERIM_STATUS_REFUSED);
        $application->setInterimStatus($status);
        $application->setInterimEnd(new DateTime());

        $this->result->addMessage('Interim updated');

        $this->getRepo()->save($application);

        $this->maybeRefundInterimFee($application);

        return $this->result;
    }

    private function maybeRefundInterimFee($application)
    {
        /** @var Fee $fee */
        foreach ($application->getFees() as $fee) {
            if ($fee->canRefund() && $fee->getFeeType()->getFeeType()->getId() === FeeType::FEE_TYPE_GRANTINT) {
                $createCommand = Create::create(
                    [
                        'entityId' => $fee->getId(),
                        'type' => Queue::TYPE_REFUND_INTERIM_FEES,
                        'status' => Queue::STATUS_QUEUED,
                    ]
                );
                $this->result->merge($this->handleSideEffect($createCommand));
            }
        }
    }
}
