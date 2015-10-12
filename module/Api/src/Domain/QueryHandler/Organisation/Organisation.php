<?php

/**
 * Organisation
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\QueryHandler\Organisation;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Transfer\Query\QueryInterface;

/**
 * Organisation
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class Organisation extends AbstractQueryHandler
{
    protected $repoServiceName = 'Organisation';

    public function handleQuery(QueryInterface $query)
    {
        /* @var $organisation \Dvsa\Olcs\Api\Entity\Organisation\Organisation */
        $organisation = $this->getRepo()->fetchUsingId($query);

        return $this->result(
            $organisation,
            [
                'disqualifications'
            ],
            [
                'isDisqualified' => $organisation->getDisqualifications()->count() > 0,
            ]
        );
    }
}
