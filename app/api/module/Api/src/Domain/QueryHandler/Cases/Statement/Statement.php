<?php

namespace Dvsa\Olcs\Api\Domain\QueryHandler\Cases\Statement;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Transfer\Query\QueryInterface;

/**
 * Statement
 */
final class Statement extends AbstractQueryHandler
{
    protected $repoServiceName = 'Statement';

    public function handleQuery(QueryInterface $query)
    {
        return $this->result(
            $this->getRepo()->fetchUsingId($query),
            [
                'case',
                'requestorsContactDetails' => [
                    'address' => [
                        'countryCode'
                    ],
                    'contactType',
                    'person'
                ]
            ]
        );
    }
}
