<?php

namespace Dvsa\Olcs\Api\Domain\QueryHandler\Permits;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryByIdHandler;
use Dvsa\Olcs\Api\Domain\ToggleAwareTrait;
use Dvsa\Olcs\Api\Domain\ToggleRequiredInterface;
use Dvsa\Olcs\Api\Entity\System\FeatureToggle;

/**
 * Retrieve a permit application by id
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
final class ById extends AbstractQueryByIdHandler implements ToggleRequiredInterface
{
    use ToggleAwareTrait;

    protected $toggleConfig = [FeatureToggle::BACKEND_PERMITS];
    protected $repoServiceName = 'EcmtPermitApplication';
    protected $bundle = ['licence'=>['trafficArea', 'licenceType', 'organisation'],
                         'sectors' => ['sectors'],
                         'countrys' => ['country'],
                         'irhpPermitApplications' => ['irhpPermitWindow' => ['irhpPermitStock', 'emissionsCategory']],
                         'fees' => ['feeStatus', 'feeType' => ['feeType']]
                        ];
}
