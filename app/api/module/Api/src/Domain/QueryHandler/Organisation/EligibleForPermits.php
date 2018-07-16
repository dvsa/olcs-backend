<?php

namespace Dvsa\Olcs\Api\Domain\QueryHandler\Organisation;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Api\Entity\Organisation\Organisation as OrganisationEntity;
use Dvsa\Olcs\Transfer\Query\Organisation\EligibleForPermits as EligibleForPermitsQry;
use Dvsa\Olcs\Transfer\Query\Organisation\EligibleForPermitsById as EligibleForPermitsByIdQry;
use Dvsa\Olcs\Transfer\Query\QueryInterface;

/**
 * Retrieve whether the organisation is eligible for permits
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
final class EligibleForPermits extends AbstractQueryHandler
{
    protected $repoServiceName = 'Organisation';

    public function handleQuery(QueryInterface $query)
    {
        $eligibleForPermits = false;

        /**
         * If no id is provided then we're looking at the organisation for the current user
         *
         * @var EligibleForPermitsQry $query
         */
        if ($query instanceof EligibleForPermitsByIdQry) {
            $organisation = $this->getRepo()->fetchUsingId($query);
        } else {
            $organisation = $this->getCurrentOrganisation();
        }

        if ($organisation instanceof OrganisationEntity) {
            $eligibleForPermits = $organisation->isEligibleForPermits();
        }

        return ['eligibleForPermits' => $eligibleForPermits];
    }
}
