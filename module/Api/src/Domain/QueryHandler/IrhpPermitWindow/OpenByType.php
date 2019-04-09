<?php

namespace Dvsa\Olcs\Api\Domain\QueryHandler\IrhpPermitWindow;

use DateTime;
use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Dvsa\Olcs\Api\Domain\ToggleAwareTrait;
use Dvsa\Olcs\Api\Domain\ToggleRequiredInterface;
use Dvsa\Olcs\Api\Entity\System\FeatureToggle;

/**
 * Open Windows by Permit Type
 *
 * @author Andy Newton
 */
class OpenByType extends AbstractQueryHandler implements ToggleRequiredInterface
{
    use ToggleAwareTrait;

    protected $toggleConfig = [FeatureToggle::ADMIN_PERMITS];
    protected $repoServiceName = 'IrhpPermitWindow';
    protected $bundle = ['irhpPermitStock' => ['irhpPermitType' => ['name'], 'country']];

    public function handleQuery(QueryInterface $query)
    {
        // fetch the list of all open windows for selected countries
        $openWindows = $this->getRepo('IrhpPermitWindow')->fetchOpenWindowsByType(
            $query->getIrhpPermitType(),
            new DateTime($query->getCurrentDateTime())
        );

        return [
            'result' => $this->resultList(
                $openWindows,
                $this->bundle
            )
        ];
    }
}
