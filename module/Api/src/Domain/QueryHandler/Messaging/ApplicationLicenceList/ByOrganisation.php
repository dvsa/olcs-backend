<?php

declare(strict_types=1);

namespace Dvsa\Olcs\Api\Domain\QueryHandler\Messaging\ApplicationLicenceList;

use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Api\Domain\Repository\Licence as LicenceRepo;
use Dvsa\Olcs\Api\Domain\Repository\Application as ApplicationRepo;
use Dvsa\Olcs\Api\Domain\ToggleAwareTrait;
use Dvsa\Olcs\Api\Domain\ToggleRequiredInterface;
use Dvsa\Olcs\Api\Entity\System\FeatureToggle;
use Dvsa\Olcs\Api\Entity\Application\Application as Entity;
use Dvsa\Olcs\Transfer\Query\QueryInterface;

class ByOrganisation extends AbstractQueryHandler implements ToggleRequiredInterface
{
    use ToggleAwareTrait;

    protected $toggleConfig = [FeatureToggle::MESSAGING];

    protected $extraRepos = ['Application','Licence'];

    public function handleQuery(QueryInterface $query)
    {
        $licenceRepository = $this->getRepo('Licence');
        $applicationRepository = $this->getRepo('Application');

        $licences = $licenceRepository->fetchByOrganisationId($query->getOrganisation());

        $applications = $applicationRepository->fetchByOrganisationIdAndStatuses($query->getOrganisation(),
            Entity::ALL_APPLICATION_STATUSES,
            AbstractQuery::HYDRATE_ARRAY
        );

        $results = array_fill_keys(['licences', 'applications'], array());

        // Sort by app / licence
        // Select uses label => id

        foreach($licences as $licence){
            if(empty($licence['licNo'])){
                continue;
            }
            $results['licences'][$licence['id']] = $licence['licNo'];
        }

        foreach($applications as $application){
            $results['applications'][$application['id']] = $application['id'];
        }

        return [
            'result' => $results
        ];
    }
}
