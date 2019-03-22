<?php

namespace Dvsa\Olcs\Cli\Service\Queue\Consumer;

use Dvsa\Olcs\Api\Domain\Repository\Fee;
use Dvsa\Olcs\Api\Entity\Queue\Queue as QueueEntity;
use Dvsa\Olcs\Transfer\Command\Fee\RefundFee;

class RefundInterimFees extends AbstractCommandConsumer
{
    protected $commandName = RefundFee::class;

    protected $feeRepo;

    public function __construct(Fee $feeRepo)
    {
        $this->feeRepo = $feeRepo;
    }

    public function getCommandData(QueueEntity $item)
    {
        return ['id' => $item->getEntityId()];
    }

    public function processMessage(QueueEntity $item)
    {
        /** @var \Dvsa\Olcs\Api\Entity\Fee\Fee $fee */
        $fee = $this->feeRepo->fetchById($item->getEntityId());

        if (!$fee->canRefund()) {
            return $this->failed($item, "Fee cannot be refunded");
        }

        return parent::processMessage($item);
    }
}