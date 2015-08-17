<?php

namespace Dvsa\Olcs\Api\Domain\QueryHandler\Organisation;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Transfer\Query\QueryInterface;

/**
 * People
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class People extends AbstractQueryHandler
{
    protected $repoServiceName = 'Organisation';

    public function handleQuery(QueryInterface $query)
    {
        /* @var $organisation \Dvsa\Olcs\Api\Entity\Organisation\Organisation */
        $organisation =  $this->getRepo()->fetchUsingId($query);

        return $this->result(
            $organisation,
            [
                'organisationPersons' => [
                    'person' => ['title']
                ]
            ],
            [
                'isSoleTrader' => $organisation->isSoleTrader()
            ]
        );
    }
}
