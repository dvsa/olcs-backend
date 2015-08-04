<?php

namespace Dvsa\Olcs\Api\Domain\Query\Bookmark;

use Dvsa\Olcs\Transfer\Query\AbstractQuery;

/**
 * FstandingCapitalReserves
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class FstandingCapitalReserves extends AbstractQuery
{
    /**
     * @var int $organisation
     */
    protected $organisation;

    /**
     * Gets the value of organisation.
     *
     * @return int
     */
    public function getOrganisation()
    {
        return $this->organisation;
    }
}
