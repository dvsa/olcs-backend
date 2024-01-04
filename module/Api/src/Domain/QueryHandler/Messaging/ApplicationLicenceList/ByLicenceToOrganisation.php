<?php

declare(strict_types=1);

namespace Dvsa\Olcs\Api\Domain\QueryHandler\Messaging\ApplicationLicenceList;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Api\Domain\Repository\Licence as LicenceRepository;
use Dvsa\Olcs\Api\Domain\ToggleAwareTrait;
use Dvsa\Olcs\Api\Domain\ToggleRequiredInterface;
use Dvsa\Olcs\Api\Entity\System\FeatureToggle;
use Dvsa\Olcs\Transfer\Query\Messaging\ApplicationLicenceList\ByLicenceToOrganisation as GetApplicationListByLicenceToOrganisationQuery;
use Dvsa\Olcs\Transfer\Query\Messaging\ApplicationLicenceList\ByOrganisation as GetApplicationListByOrganisationQuery;
use Dvsa\Olcs\Transfer\Query\QueryInterface;

class ByLicenceToOrganisation extends AbstractQueryHandler implements ToggleRequiredInterface
{
    use ToggleAwareTrait;

    protected $toggleConfig = [FeatureToggle::MESSAGING];

    protected $extraRepos = ['Licence'];

    public function handleQuery(QueryInterface $query)
    {
        assert($query instanceof GetApplicationListByLicenceToOrganisationQuery);
        $licenceRepository = $this->getLicenceRepository();

        $licence = $licenceRepository->fetchById($query->getLicence());

        $organisation = $licence->getOrganisation();

        $organisationQuery = [
            'organisation' => $organisation->getId()
        ];

        return $this->getQueryHandler()->handleQuery(GetApplicationListByOrganisationQuery::create($organisationQuery));
    }

    private function getLicenceRepository(): LicenceRepository
    {
        $licenceRepository = $this->getRepo('Licence');
        assert($licenceRepository instanceof LicenceRepository);
        return $licenceRepository;
    }
}
