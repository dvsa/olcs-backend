<?php

namespace Dvsa\Olcs\Api\Domain\Query\Queue;

use Dvsa\Olcs\Transfer\Query\AbstractQuery;

/**
 * Next Item
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class NextItem extends AbstractQuery
{
    protected $type;

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }
}
