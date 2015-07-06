<?php

namespace Dvsa\Olcs\Api\Domain\QueryHandler\Licence;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Transfer\Query\QueryInterface;

/**
 * People
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class People extends AbstractQueryHandler
{
    protected $repoServiceName = 'Licence';

    protected $extraRepos = ['OrganisationPerson'];

    public function handleQuery(QueryInterface $query)
    {
        /* @var $licence \Dvsa\Olcs\Api\Entity\Licence\Licence */
        $licence = $this->getRepo()->fetchUsingId($query);

        $organisationPersons = $this->getRepo('OrganisationPerson')->fetchListForOrganisation(
            $licence->getOrganisation()->getId()
        );

        return $this->result(
            $licence,
            [
                'organisation' => ['type']
            ],
            [
                'hasInforceLicences' => $licence->getOrganisation()->hasInforceLicences(),
                'isExceptionalType' => $licence->getOrganisation()->isPartnership() ||
                    $licence->getOrganisation()->isSoleTrader(),
                'isSoleTrader' => $licence->getOrganisation()->isSoleTrader(),
                'people' => $this->resultList($organisationPersons, ['person']),
            ]
        );
    }
}
