<?php

/**
 * Continuation Checklist Queue Consumer
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace Dvsa\Olcs\Cli\Service\Queue\Consumer;

use Dvsa\Olcs\Api\Domain\Command\ContinuationDetail\Process as Cmd;
use Dvsa\Olcs\Api\Domain\Exception\Exception as DomainException;
use Dvsa\Olcs\Api\Entity\Queue\Queue as QueueEntity;

/**
 * Continuation Checklist Queue Consumer
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class ContinuationChecklist extends AbstractConsumer
{
    protected $commandName = Cmd::class;

    /**
     * @param QueueEntity $item
     * @return array
     */
    public function getCommandData(QueueEntity $item)
    {
        return ['id' => $item->getEntityId()];
    }
}
