<?php

namespace Dvsa\Olcs\Api\Domain\QueryHandler\Cases\Hearing;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Transfer\Query\QueryInterface;

/**
 * PiHearing
 */
final class PiHearing extends AbstractQueryHandler
{
    protected $repoServiceName = 'PiHearing';

    public function handleQuery(QueryInterface $query)
    {
        $hearing = $this->getRepo()->fetchUsingId($query);

        return $this->result(
            $hearing,
            [
                'pi' => [
                    'case' => [
                        'licence'
                    ]
                ]
            ]
        );
    }
}
