<?php

declare(strict_types=1);

namespace Dvsa\Olcs\Api\Domain\CommandHandler\Messaging;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractUserCommandHandler;
use Dvsa\Olcs\Api\Entity\Messaging\MessagingConversation;
use Dvsa\Olcs\Transfer\Command\CommandInterface;

/**
 * Close a conversation
 *
 * @author Wade Womersley <wade.womersley@dvsa.org.uk>
 */
final class Close extends AbstractUserCommandHandler
{
    protected $repoServiceName = 'Conversation';

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

        return $result;
    }
}
