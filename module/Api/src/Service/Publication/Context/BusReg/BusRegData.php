<?php

namespace Dvsa\Olcs\Api\Service\Publication\Context\BusReg;

use Dvsa\Olcs\Api\Domain\Exception\NotFoundException;
use Dvsa\Olcs\Api\Domain\Query\Bookmark\BusRegBundle;
use Dvsa\Olcs\Api\Service\Publication\Context\AbstractContext;

class BusRegData extends AbstractContext
{
    private static $bundle = [
        'status',
        'revertStatus',
        'withdrawnReason',
        'parent',
        'busNoticePeriod',
        'busServiceTypes',
        'otherServices',
        'trafficAreas',
        'variationReasons',
        'localAuthoritys'
    ];

    public function provide(\ArrayObject $context)
    {
        if (!isset($context['busReg'])) {
            throw new NotFoundException('No bus reg id provided');
        }

        $query = BusRegBundle::create(['id' => $context['busReg'], 'bundle' => self::$bundle]);

        $context['busRegData'] = $this->handleQuery($query);
    }
}