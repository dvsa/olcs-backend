<?php

namespace Dvsa\Olcs\Api\Domain\QueryHandler\Application;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Dvsa\Olcs\Api\Entity\Licence\Licence as LicenceEntity;

/**
 * People
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class People extends AbstractQueryHandler
{
    protected $repoServiceName = 'Application';

    protected $extraRepos = ['ApplicationOrganisationPerson', 'OrganisationPerson', 'Person', 'Licence'];

    /**
     * Handle query
     *
     * @param QueryInterface $query query
     *
     * @return \Dvsa\Olcs\Api\Domain\QueryHandler\Result
     * @throws \Dvsa\Olcs\Api\Domain\Exception\RuntimeException
     */
    public function handleQuery(QueryInterface $query)
    {
        /* @var $application \Dvsa\Olcs\Api\Entity\Application\Application */
        $application = $this->getRepo()->fetchUsingId($query);
        $licence = $application->getLicence();

        $orgPersons = $this->getRepo('OrganisationPerson')->fetchListForOrganisation(
            $licence->getOrganisation()->getId()
        );

        $appOrgPersons = $this->getRepo('ApplicationOrganisationPerson')->fetchListForApplication(
            $query->getId()
        );

        $licences = $this->getRepo('Licence')->fetchByOrganisationIdAndStatuses(
            $licence->getOrganisation()->getId(),
            [
                LicenceEntity::LICENCE_STATUS_VALID,
                LicenceEntity::LICENCE_STATUS_SURRENDERED,
                LicenceEntity::LICENCE_STATUS_CURTAILED,
            ]
        );

        return $this->result(
            $application,
            [
                'licence' => ['organisation' => ['type']]
            ],
            [
                'useDeltas' => $application->useDeltasInPeopleSection(),
                'hasInforceLicences' => $licence->getOrganisation()->hasInforceLicences(),
                'isExceptionalType' => $licence->getOrganisation()->isSoleTrader() ||
                    $licence->getOrganisation()->isPartnership(),
                'isSoleTrader' => $licence->getOrganisation()->isSoleTrader(),
                'people' => $this->resultList($orgPersons, ['person']),
                'application-people' => $this->resultList($appOrgPersons, ['person', 'originalPerson']),
                'hasMoreThanOneValidCurtailedOrSuspendedLicences' =>
                    is_array($licences) && count($licences) > 1,
            ]
        );
    }
}
