<?php

namespace Dvsa\Olcs\Api\Domain\QueryHandler\IrhpApplication;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryByIdHandler;
use Dvsa\Olcs\Api\Domain\ToggleAwareTrait;
use Dvsa\Olcs\Api\Domain\ToggleRequiredInterface;
use Dvsa\Olcs\Api\Entity\System\FeatureToggle;

/**
 * Retrieve IRHP application by id
 */
final class ById extends AbstractQueryByIdHandler implements ToggleRequiredInterface
{
    use ToggleAwareTrait;

    protected $toggleConfig = [FeatureToggle::BACKEND_ECMT];
    protected $repoServiceName = 'IrhpApplication';
    protected $bundle = [
        'licence',
        'irhpPermitType' => ['name'],
        'irhpPermitApplications' => ['irhpPermitWindow' => ['irhpPermitStock' => ['country']]],
    ];
}
