<?php

declare(strict_types=1);

namespace Dvsa\Olcs\Api\Domain\QueryHandler\Messaging\Conversations;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Api\Domain\Repository\Application as ApplicationRepo;
use Dvsa\Olcs\Api\Domain\ToggleAwareTrait;
use Dvsa\Olcs\Api\Domain\ToggleRequiredInterface;
use Dvsa\Olcs\Api\Entity\System\FeatureToggle;
use Dvsa\Olcs\Transfer\Query\Messaging\Conversations\ByApplicationToLicence as GetConversationsByApplicationToLicenceQuery;
use Dvsa\Olcs\Transfer\Query\Messaging\Conversations\ByLicence as GetConversationsByLicenceQuery;
use Dvsa\Olcs\Transfer\Query\QueryInterface;

class ByApplicationToLicence extends AbstractQueryHandler implements ToggleRequiredInterface
{
    use ToggleAwareTrait;

    protected $toggleConfig = [FeatureToggle::MESSAGING];
    protected $extraRepos = ['Application'];

    public function handleQuery(QueryInterface $query)
    {
        assert($query instanceof GetConversationsByApplicationToLicenceQuery);
        $applicationRepository = $this->getApplicationRepository();

        $application = $applicationRepository->fetchById($query->getApplication());

        $licenceQuery = [
            'page' => $query->getPage(),
            'limit' => $query->getLimit(),
            'licence' => $application->getLicence()->getId(),
        ];

        return $this->getQueryHandler()->handleQuery(GetConversationsByLicenceQuery::create($licenceQuery));
    }

    private function getApplicationRepository(): ApplicationRepo
    {
        $applicationRepository = $this->getRepo('Application');
        assert($applicationRepository instanceof ApplicationRepo);
        return $applicationRepository;
    }
}
