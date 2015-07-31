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

    /**
     * @todo probably don't need this unless we override processMessage()
     */
    protected function skip(QueueEntity $item)
    {
        return $this->success($item, 'Continuation detail no longer pending');
    }

    /**
     * Mark the message as failed and continuation detail record as errored
     *
     * @param QueueEntity $item
     * @param string $reason
     * @return string
     * @todo port additional logic from olcs-internal consumer on failure
     * @see Cli\Service\Queue\Consumer\ContinuationChecklist
     */
    protected function failed(QueueEntity $item, $reason = null)
    {
        // $this->getServiceLocator()->get('Entity\ContinuationDetail')->checklistFailed($item['entityId']);
        return parent::failed($item, $reason);
    }
}
