<?php

/**
 * Business Details
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\QueryHandler\Operator;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Transfer\Query\QueryInterface;

/**
 * Business Details
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class BusinessDetails extends AbstractQueryHandler
{
    protected $repoServiceName = 'Organisation';

    public function handleQuery(QueryInterface $query)
    {
        $orgamisation = $this->getRepo()->fetchBusinessDetailsUsingId($query);
        return $this->result(
            $orgamisation,
            [
                'organisationPersons' => ['person'],
                'natureOfBusinesses',
                'contactDetails' => ['address']
            ]
        );
    }
}
