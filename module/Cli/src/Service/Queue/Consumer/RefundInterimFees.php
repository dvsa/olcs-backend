<?php

namespace Dvsa\Olcs\Cli\Service\Queue\Consumer;

use Dvsa\Olcs\Api\Entity\Queue\Queue as QueueEntity;
use Dvsa\Olcs\Transfer\Command\Fee\RefundFee;

class RefundInterimFees extends AbstractCommandConsumer
{
    protected $commandName = RefundFee::class;

    public function getCommandData(QueueEntity $item)
    {
        return ['id' => $item->getEntityId()];
    }
}