<?php

namespace Dvsa\Olcs\Api\Domain\QueryHandler\IrhpPermitType;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryByIdHandler;
use Dvsa\Olcs\Api\Domain\ToggleAwareTrait;
use Dvsa\Olcs\Api\Domain\ToggleRequiredInterface;
use Dvsa\Olcs\Api\Entity\System\FeatureToggle;

/**
 * Retrieve a permit type by id
 */
final class ById extends AbstractQueryByIdHandler implements ToggleRequiredInterface
{
    use ToggleAwareTrait;

    protected $toggleConfig = [FeatureToggle::ADMIN_PERMITS];
    protected $repoServiceName = 'IrhpPermitType';
}
