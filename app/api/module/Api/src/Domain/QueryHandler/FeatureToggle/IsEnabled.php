<?php

namespace Dvsa\Olcs\Api\Domain\QueryHandler\FeatureToggle;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Api\Domain\ToggleAwareInterface;
use Dvsa\Olcs\Api\Domain\ToggleAwareTrait;
use Dvsa\Olcs\Transfer\Query\FeatureToggle\IsEnabled as IsEnabledQry;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Olcs\Logging\Log\Logger;

/**
 * Retrieve a feature toggle by id
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
class IsEnabled extends AbstractQueryHandler implements ToggleAwareInterface
{
    use ToggleAwareTrait;

    protected $repoServiceName = 'FeatureToggle';

    public function handleQuery(QueryInterface $query)
    {
        $allFeaturesEnabled = true;

        /** @var IsEnabledQry $query */
        $togglesToCheck = $query->getIds();

        foreach ($togglesToCheck as $toggle) {
            if (!$this->toggleService->isEnabled($toggle)) {
                $allFeaturesEnabled = false;
                break;
            }
        }

        return ['isEnabled' => $allFeaturesEnabled];
    }
}
