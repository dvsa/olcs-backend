<?php

namespace Dvsa\Olcs\Api\Domain\QueryHandler\IrhpPermitStock;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryByIdHandler;
use Dvsa\Olcs\Api\Domain\ToggleAwareTrait;
use Dvsa\Olcs\Api\Domain\ToggleRequiredInterface;
use Dvsa\Olcs\Api\Entity\System\FeatureToggle;

/**
 * Retrieve a permit stock by id
 *
 * @author Scott Callaway <scott.callaway@capgemini.com>
 */
final class ById extends AbstractQueryByIdHandler implements ToggleRequiredInterface
{
    use ToggleAwareTrait;

    protected $toggleConfig = [FeatureToggle::ADMIN_PERMITS];
    protected $repoServiceName = 'IrhpPermitStock';
    protected $bundle = ['irhpPermitType' => ['name'], 'country'];
}
