<?php

namespace Dvsa\Olcs\Cli\Service\Queue\Consumer\PrintJob;

use Dvsa\Olcs\Api\Entity\Queue\Queue as QueueEntity;
use Dvsa\Olcs\Cli\Service\Queue\Consumer\AbstractCommandConsumer;

/**
 * Print Job
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class PrintJob extends AbstractCommandConsumer
{
    protected $maxAttempts = 3;

    /**
     * @var string the command to handle processing
     */
    protected $commandName = \Dvsa\Olcs\Api\Domain\Command\PrintScheduler\PrintJob::class;

    /**
     * {@inheritdoc}
     */
    public function getCommandData(QueueEntity $item)
    {
        $options = json_decode($item->getOptions(), true);

        return [
            'id' => $item->getId(),
            'title' => $options['jobName'],
            'documents' => isset($options['documents']) ? $options['documents'] : [$item->getEntityId()],
            'user' => isset($options['userId']) ? $options['userId'] : null,
            'copies' => (isset($options['copies']) ? $options['copies'] : null),
        ];
    }
}
