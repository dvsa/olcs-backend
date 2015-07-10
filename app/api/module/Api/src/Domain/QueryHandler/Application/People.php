<?php

namespace Dvsa\Olcs\Api\Domain\QueryHandler\Application;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Transfer\Query\QueryInterface;

/**
 * People
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class People extends AbstractQueryHandler
{
    protected $repoServiceName = 'Application';

    protected $extraRepos = ['ApplicationOrganisationPerson', 'OrganisationPerson', 'Person'];

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
            ]
        );
    }
}
