<?php

declare(strict_types=1);

namespace Dvsa\Olcs\Api\Domain\QueryHandler\Messaging\ApplicationLicenceList;

use Doctrine\ORM\AbstractQuery;
use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Api\Domain\Repository;
use Dvsa\Olcs\Api\Domain\ToggleAwareTrait;
use Dvsa\Olcs\Api\Domain\ToggleRequiredInterface;
use Dvsa\Olcs\Api\Entity\System\FeatureToggle;
use Dvsa\Olcs\Api\Entity\Application\Application as Entity;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Dvsa\Olcs\Transfer\Query\Messaging\ApplicationLicenceList\ByOrganisation as ByOrganisationQuery;

class ByOrganisation extends AbstractQueryHandler implements ToggleRequiredInterface
{
    use ToggleAwareTrait;

    protected $toggleConfig = [FeatureToggle::MESSAGING];

    protected $extraRepos = [Repository\Application::class, Repository\Licence::class];

    /**
     * @param ByOrganisationQuery $query
     */
    public function handleQuery(QueryInterface $query): array
    {
        $licenceRepository = $this->getLicenceRepository();
        $applicationRepository = $this->getApplicationRepository();

        $licences = $licenceRepository->fetchByOrganisationId($query->getOrganisation());

        $applications = $applicationRepository->fetchByOrganisationIdAndStatuses(
            $query->getOrganisation(),
            Entity::ALL_APPLICATION_STATUSES,
            AbstractQuery::HYDRATE_ARRAY
        );

        $results = array_fill_keys(['licences', 'applications'], []);

        // Sort by app / licence
        // Select uses label => id

        foreach ($licences as $licence) {
            if (empty($licence['licNo'])) {
                continue;
            }
            $results['licences'][$licence['id']] = $licence['licNo'];
        }

        foreach ($applications as $application) {
            $results['applications'][$application['id']] = $application['id'];
        }

        return [
            'result' => $results
        ];
    }

    public function getLicenceRepository(): Repository\Licence
    {
        return $this->getRepo(Repository\Licence::class);
    }

    public function getApplicationRepository(): Repository\Application
    {
        return $this->getRepo(Repository\Application::class);
    }
}
