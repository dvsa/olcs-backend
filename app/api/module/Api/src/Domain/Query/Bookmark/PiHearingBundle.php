<?php

namespace Dvsa\Olcs\Api\Domain\Query\Bookmark;

use Dvsa\Olcs\Transfer\FieldType\Traits\Identity;
use Dvsa\Olcs\Transfer\Query\AbstractQuery;

/**
 * PiHearing Bundle
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class PiHearingBundle extends AbstractQuery
{
    use Identity;

    protected $bundle = [
        'pi' => [
            'case' => [
                'licence'
            ]
        ]
    ];

    /**
     * @return mixed
     */
    public function getBundle()
    {
        return $this->bundle;
    }
}
