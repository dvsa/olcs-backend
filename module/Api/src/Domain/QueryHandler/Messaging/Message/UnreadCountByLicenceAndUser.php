<?php

declare(strict_types=1);

namespace Dvsa\Olcs\Api\Domain\QueryHandler\Messaging\Message;

use Dvsa\Olcs\Api\Domain\QueryHandler\Messaging\Conversations\AbstractConversationQueryHandler;
use Dvsa\Olcs\Api\Domain\Repository\Conversation as ConversationRepo;
use Dvsa\Olcs\Api\Domain\Repository\Message as MessageRepo;
use Dvsa\Olcs\Api\Domain\ToggleAwareTrait;
use Dvsa\Olcs\Api\Domain\ToggleRequiredInterface;
use Dvsa\Olcs\Api\Entity\System\FeatureToggle;
use Dvsa\Olcs\Transfer\Query\QueryInterface;

class UnreadCountByLicenceAndUser extends AbstractConversationQueryHandler implements ToggleRequiredInterface
{
    use ToggleAwareTrait;

    protected $toggleConfig = [FeatureToggle::MESSAGING];

    public function handleQuery(QueryInterface $query): int
    {
        $messageRepository = $this->getMessageRepository();
        $results = $messageRepository
            ->getUnreadMessagesByLicenceIdAndUserId($query->getLicence()->getId(), $this->getUser()->getId());

        return count($results);
    }

    private function getMessageRepository(): MessageRepo
    {
        $messageRepository = $this->getRepo('Message');
        // assert($messageRepository instanceof MessageRepo);
        return $messageRepository;
    }
}
