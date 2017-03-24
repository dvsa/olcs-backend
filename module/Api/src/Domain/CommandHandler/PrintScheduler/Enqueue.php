<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\PrintScheduler;

use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Entity\Queue\Queue;

/**
 * Queue a print job
 *
 */
final class Enqueue extends AbstractCommandHandler implements \Dvsa\Olcs\Api\Domain\AuthAwareInterface
{
    use \Dvsa\Olcs\Api\Domain\AuthAwareTrait;

    protected $repoServiceName = 'Document';

    protected $extraRepos = ['User'];

    public function handleCommand(CommandInterface $command)
    {
        /* @var $command \Dvsa\Olcs\Api\Domain\Command\PrintScheduler\Enqueue */

        // Document id parameter must be present and must be a number
        if (!is_numeric($command->getDocumentId())) {
            throw new \Dvsa\Olcs\Api\Domain\Exception\ValidationException(
                ['Print, document ID parameter must be an integer']
            );
        }

        $user = $command->getUser() ? $this->getRepo('User')->fetchById($command->getUser()) : $this->getCurrentUser();
        // if the user is in a team check the user has a team printer assigned
        if ($user->getTeam() && $user->getTeam()->getTeamPrinters()->isEmpty()) {
            throw new \Dvsa\Olcs\Api\Domain\Exception\BadRequestException(
                'Failed to generate document as there are no printer settings for the current user'
            );
        }

        $dtoData = [
            'type' => Queue::TYPE_PRINT,
            'entityId' => $command->getDocumentId(),
            'status' => Queue::STATUS_QUEUED,
            'options' => json_encode(
                array_filter(
                    [
                        'userId' => $user->getId(),
                        'jobName' => $command->getJobName(),
                        'copies' => $command->getCopies(),
                    ]
                )
            ),
        ];

        if ($command->getIsDiscPrinting()) {
            // if disc printing then use bespoke queue print type
            $dtoData['type'] = Queue::TYPE_DISC_PRINTING_PRINT;
        }

        $this->handleSideEffect(\Dvsa\Olcs\Api\Domain\Command\Queue\Create::create($dtoData));

        $this->result->addMessage(
            "Document id '{$command->getDocumentId()}', '{$command->getJobName()}' queued for print"
        );
        return $this->result;
    }
}
