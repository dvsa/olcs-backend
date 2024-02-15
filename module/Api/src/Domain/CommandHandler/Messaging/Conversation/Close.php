<?php

declare(strict_types=1);

namespace Dvsa\Olcs\Api\Domain\CommandHandler\Messaging\Conversation;

use Dvsa\Olcs\Api\Domain\Command\Email\CreateCorrespondenceRecord;
use Dvsa\Olcs\Api\Domain\Command\Messaging\Conversation\StoreSnapshot;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractUserCommandHandler;
use Dvsa\Olcs\Api\Domain\ToggleAwareTrait;
use Dvsa\Olcs\Api\Domain\ToggleRequiredInterface;
use Dvsa\Olcs\Api\Entity\Messaging\MessagingConversation;
use Dvsa\Olcs\Api\Entity\System\FeatureToggle;
use Dvsa\Olcs\Transfer\Command\CommandInterface;

/**
 * Close a conversation
 *
 * @author Wade Womersley <wade.womersley@dvsa.org.uk>
 */
final class Close extends AbstractUserCommandHandler implements ToggleRequiredInterface
{
    use ToggleAwareTrait;

    protected $repoServiceName = 'Conversation';
    protected $toggleConfig = [FeatureToggle::MESSAGING];

    /**
     * Close Command Handler Abstract
     */
    public function handleCommand(CommandInterface $command): Result
    {
        /** @var MessagingConversation $conversation */
        $conversation = $this->getRepo()->fetchUsingId($command);
        $conversation->setIsClosed(true);
        $this->getRepo()->save($conversation);

        $result = new Result();
        $result->addId('conversation', $conversation->getId());
        $result->addMessage('Conversation closed');

        $documentResult = $this->handleSideEffect(StoreSnapshot::create(['id' => $conversation->getId()]));

        $result->merge($documentResult);
        $result->merge(
            $this->handleSideEffect(
                CreateCorrespondenceRecord::create(
                    [
                        'licence'  => $conversation->getRelatedLicence(),
                        'document' => $documentResult->getId('document'),
                        'type'     => CreateCorrespondenceRecord::TYPE_STANDARD,
                    ],
                ),
            ),
        );

        return $result;
    }
}
