<?php

namespace Dvsa\Olcs\Api\Domain\QueryHandler\Cases\Impounding;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Dvsa\Olcs\Api\Domain\Repository\Impounding as ImpoundingRepo;

/**
 * Impounding
 */
final class Impounding extends AbstractQueryHandler
{
    protected $repoServiceName = 'Impounding';

    public function handleQuery(QueryInterface $query)
    {
        return $this->result(
            $this->getRepo()->fetchUsingId($query),
            [
                'presidingTc',
                'impoundingLegislationTypes',
                'piVenue'
            ]
        );
    }
}
