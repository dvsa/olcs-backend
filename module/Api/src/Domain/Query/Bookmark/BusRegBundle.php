<?php

namespace Dvsa\Olcs\Api\Domain\Query\Bookmark;

use Dvsa\Olcs\Transfer\FieldType\Traits\Identity;
use Dvsa\Olcs\Transfer\Query\AbstractQuery;

/**
 * BusReg Bundle
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class BusRegBundle extends AbstractQuery
{
    use Identity;

    protected $bundle = [];

    /**
     * @return mixed
     */
    public function getBundle()
    {
        return $this->bundle;
    }
}
