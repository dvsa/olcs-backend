<?php

/**
 * Create Recipient
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Publication;

use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\Exception;
use Dvsa\Olcs\Api\Entity\Publication\Recipient;
use Dvsa\Olcs\Api\Entity\TrafficArea\TrafficArea;
use Dvsa\Olcs\Transfer\Command\Publication\CreateRecipient as Cmd;

/**
 * Create Recipient
 */
final class CreateRecipient extends AbstractCommandHandler implements TransactionedInterface
{
    const ERROR_INVALID_SUBSCRIPTION = 'PUB-REC-1';

    protected $repoServiceName = 'Recipient';

    public function handleCommand(CommandInterface $command)
    {
        // extra validation
        if ($command->getSendAppDecision() === 'N' && $command->getSendNoticesProcs() === 'N') {
            throw new Exception\ValidationException(
                [
                    self::ERROR_INVALID_SUBSCRIPTION
                        => 'Subscription details must be selected'
                ]
            );
        }

        // create and save a record
        $recipient = $this->createRecipientObject($command);
        $this->getRepo()->save($recipient);

        $result = new Result();
        $result->addId('recipient', $recipient->getId());
        $result->addMessage('Recipient created successfully');

        return $result;
    }

    /**
     * @param Cmd $command
     * @return Recipient
     */
    private function createRecipientObject(Cmd $command)
    {
        $recipient = new Recipient();

        $recipient->setIsObjector($command->getIsObjector());
        $recipient->setContactName($command->getContactName());
        $recipient->setEmailAddress($command->getEmailAddress());
        $recipient->setSendAppDecision($command->getSendAppDecision());
        $recipient->setSendNoticesProcs($command->getSendNoticesProcs());

        $trafficAreas = [];
        foreach ($command->getTrafficAreas() as $trafficAreaId) {
            $trafficAreas[] = $this->getRepo()->getReference(TrafficArea::class, $trafficAreaId);
        }
        $recipient->setTrafficAreas($trafficAreas);

        if ($command->getIsPolice() !== null) {
            $recipient->setIsPolice($command->getIsPolice());
        }

        return $recipient;
    }
}
