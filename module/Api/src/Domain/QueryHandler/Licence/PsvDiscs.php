<?php

/**
 * Psv Discs
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\QueryHandler\Licence;

use Doctrine\Common\Collections\Criteria;
use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Dvsa\Olcs\Api\Entity\Licence\Licence as LicenceEntity;

/**
 * Psv Discs
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class PsvDiscs extends AbstractQueryHandler
{
    protected $repoServiceName = 'Licence';

    protected $extraRepos = ['PsvDisc'];

    public function handleQuery(QueryInterface $query)
    {
        /** @var LicenceEntity $licence */
        $licence = $this->getRepo()->fetchUsingId($query);

        return $this->result(
            $licence,
            [],
            [
                'psvDiscs' => $this->getRepo('PsvDisc')->fetchList($query),
                'remainingSpacesPsv' => $licence->getRemainingSpacesPsv(),
                'totalPsvDiscs' => $this->getRepo('PsvDisc')->fetchCount($query),
            ]
        );
    }
}
