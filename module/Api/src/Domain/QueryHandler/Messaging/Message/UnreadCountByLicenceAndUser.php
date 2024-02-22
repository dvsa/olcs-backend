<?php

declare(strict_types=1);

namespace Dvsa\Olcs\Api\Domain\QueryHandler\Messaging\Message;

use Dvsa\Olcs\Api\Domain\QueryHandler\Messaging\Conversations\AbstractConversationQueryHandler;
use Dvsa\Olcs\Api\Domain\Repository;
use Dvsa\Olcs\Api\Domain\ToggleAwareTrait;
use Dvsa\Olcs\Api\Domain\ToggleRequiredInterface;
use Dvsa\Olcs\Api\Entity\System\FeatureToggle;
use Dvsa\Olcs\Transfer\Query\QueryInterface;

class UnreadCountByLicenceAndUser extends AbstractConversationQueryHandler implements ToggleRequiredInterface
{
    use ToggleAwareTrait;

    protected $toggleConfig = [FeatureToggle::MESSAGING];
    protected $extraRepos = [Repository\Message::class];

    /**
     * @param Dvsa\Olcs\Transfer\Query\Messaging\Messages\UnreadCountByOrganisationAndUser | QueryInterface $query
     */
    public function handleQuery(QueryInterface $query): array
    {
        $messageRepository = $this->getMessageRepository();
        $results = $messageRepository
            ->getUnreadConversationCountByLicenceIdAndUserId($query->getLicence()->getId(), $this->getUser()->getId());

        return ['count' => $results];
    }

    private function getMessageRepository(): Repository\Message
    {
        $messageRepository = $this->getRepo(Repository\Message::class);
        return $messageRepository;
    }
}
