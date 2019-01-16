<?php

namespace Dvsa\Olcs\Api\Domain\QueryHandler\ContactDetail;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractListQueryHandler;
use Dvsa\Olcs\Api\Domain\ToggleAwareTrait;
use Dvsa\Olcs\Api\Domain\ToggleRequiredInterface;
use Dvsa\Olcs\Api\Entity\System\FeatureToggle;

/**
 * Get a list of Countries, optionally filtered by flags
 *
 * @author Andy Newton <andy@vitri.ltd>
 */
class CountrySelectList extends AbstractListQueryHandler implements ToggleRequiredInterface
{
    use ToggleAwareTrait;

    protected $toggleConfig = [FeatureToggle::BACKEND_ECMT];

    protected $repoServiceName = 'Country';
}
