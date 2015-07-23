<?php

namespace Dvsa\Olcs\Api\Domain\Query\Bookmark;

use Dvsa\Olcs\Transfer\Query\AbstractQuery;
use Dvsa\Olcs\Api\Entity\Organisation\Organisation;

/**
 * FstandingCapitalReserves
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class FstandingCapitalReserves extends AbstractQuery
{
    /**
     * @var Organisation $organisation
     */
    protected $organisation;

    /**
     * Gets the value of organisation.
     *
     * @return Organisation
     */
    public function getOrganisation()
    {
        return $this->organisation;
    }
}
