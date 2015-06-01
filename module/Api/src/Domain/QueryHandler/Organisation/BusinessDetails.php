<?php

/**
 * Business Details
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\QueryHandler\Organisation;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Dvsa\Olcs\Api\Entity\Organisation\Organisation as OrgEntity;

/**
 * Business Details
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class BusinessDetails extends AbstractQueryHandler
{
    protected $repoServiceName = 'Organisation';

    public function handleQuery(QueryInterface $query)
    {
        /** @var OrgEntity $organisation */
        $organisation = $this->getRepo()->fetchBusinessTypeUsingId($query);

        $orgData = $organisation->jsonSerialize();

        //$orgData['tradingNames'] = [];
        //$orgData['contactDetails'] = [];

        return $orgData;
    }
}
