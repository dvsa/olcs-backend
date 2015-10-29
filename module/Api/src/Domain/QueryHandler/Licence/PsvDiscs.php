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

    public function handleQuery(QueryInterface $query)
    {
        /** @var LicenceEntity $licence */
        $licence = $this->getRepo()->fetchUsingId($query);

        if ($query->getIncludeCeased()) {
            return $this->result(
                $licence,
                ['psvDiscs'],
                ['remainingSpacesPsv' => $licence->getRemainingSpacesPsv()]
            );
        }

        $criteria = Criteria::create();
        $criteria->where(
            $criteria->expr()->isNull('ceasedDate')
        );

        return $this->result(
            $licence,
            [
                'psvDiscs' => ['criteria' => $criteria]
            ],
            [
                'remainingSpacesPsv' => $licence->getRemainingSpacesPsv(),
                'totalPsvDiscs' => $licence->getPsvDiscs()->count()
            ]
        );
    }
}
