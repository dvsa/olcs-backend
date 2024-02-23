<?php

declare(strict_types=1);

namespace Dvsa\Olcs\Api\Domain\QueryHandler\Messaging;

use Dvsa\Olcs\Api\Domain\AuthAwareTrait;
use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Api\Domain\Repository\Conversation as ConversationRepo;
use Dvsa\Olcs\Api\Domain\Repository\Document;
use Dvsa\Olcs\Api\Domain\ToggleAwareTrait;
use Dvsa\Olcs\Api\Domain\ToggleRequiredInterface;
use Dvsa\Olcs\Api\Entity\System\FeatureToggle;
use Dvsa\Olcs\Transfer\Query\Messaging\Documents as DocumentsQuery;
use Dvsa\Olcs\Transfer\Query\QueryInterface;

class Documents extends AbstractQueryHandler implements ToggleRequiredInterface
{
    use ToggleAwareTrait;
    use AuthAwareTrait;

    protected $toggleConfig = [FeatureToggle::MESSAGING];
    protected $extraRepos = [ConversationRepo::class, Document::class];

    /** @param DocumentsQuery|QueryInterface $query */
    public function handleQuery(QueryInterface $query): array
    {
        $documentsRepo = $this->getRepo(Document::class);
        $documents = $documentsRepo->fetchListForConversation((int)$query->getConversation(), $this->getUser()->getId());

        return $this->resultList($documents);
    }
}
