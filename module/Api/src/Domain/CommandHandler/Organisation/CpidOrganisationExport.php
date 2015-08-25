<?php

/**
 * CpidOrganisationExport.php
 *
 * @author Josh Curtis <josh.curtis@valtech.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Organisation;

use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Entity\Queue\Queue;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Transfer\Command\CommandInterface;

/**
 * Class CpidOrganisationExport
 *
 * @author Josh Curtis <josh.curtis@valtech.co.uk>
 */
class CpidOrganisationExport extends AbstractCommandHandler
{
    protected $repoServiceName = 'Queue';

    public function handleCommand(CommandInterface $command)
    {
        $queueItem = new Queue($this->getRepo()->getRefdataReference(Queue::TYPE_CPID_EXPORT_CSV));
        $queueItem->setStatus($this->getRepo()->getRefdataReference(Queue::STATUS_QUEUED));
        $queueItem->setOptions(json_encode(['status' => $command->getCpid()]));

        $this->getRepo()->save($queueItem);

        $result = new Result();
        $result->addMessage('Cpid Export Queued.');

        return $result;
    }
}
