<?php

/**
 * Unlicensed Vehicles
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace Dvsa\Olcs\Api\Domain\QueryHandler\Operator;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\AuthAwareInterface;
use Dvsa\Olcs\Api\Domain\AuthAwareTrait;
use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Api\Entity\User\Permission;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Dvsa\Olcs\Api\Entity\Organisation\Organisation as OrganisationEntity;

/**
 * Unlicensed Vehicles
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class UnlicensedVehicles extends AbstractQueryHandler implements AuthAwareInterface
{
    use AuthAwareTrait;

    protected $repoServiceName = 'Organisation';

    protected $extraRepos = ['LicenceVehicle'];

    public function handleQuery(QueryInterface $query)
    {
        /** @var OrganisationEntity $organisation */
        $organisation = $this->getRepo()->fetchUsingId($query);

        $licence = $organisation->getLicences()->first();

        $lvQuery = $this->getRepo('LicenceVehicle')->createPaginatedVehiclesDataForUnlicensedOperatorQuery(
            $query,
            $licence->getId()
        );

        return $this->result(
            $organisation,
            [],
            [
                'licenceVehicles' => [
                    'results' => $this->resultList(
                        $this->getRepo('LicenceVehicle')->fetchPaginatedList($lvQuery, Query::HYDRATE_OBJECT),
                        [
                            'vehicle',
                        ]
                    ),
                    'count' => $this->getRepo('LicenceVehicle')->fetchPaginatedCount($lvQuery)
                ],
            ]
        );
    }
}
