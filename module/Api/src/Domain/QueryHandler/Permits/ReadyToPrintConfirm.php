<?php

namespace Dvsa\Olcs\Api\Domain\QueryHandler\Permits;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractListQueryHandler;

/**
 * Get a list of permits ready to print for confirmation
 */
class ReadyToPrintConfirm extends AbstractListQueryHandler
{
    protected $repoServiceName = 'IrhpPermit';

    protected $bundle = [
        'irhpPermitApplication',
        'irhpPermitRange' => [
            'emissionsCategory',
            'journey',
            'irhpPermitStock' => [
                'irhpPermitType' => ['name'],
                'country',
                'permitCategory',
            ],
        ],
    ];
}
