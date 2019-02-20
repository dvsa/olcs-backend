<?php

namespace Dvsa\Olcs\Api\Domain\QueryHandler\IrhpPermitWindow;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Dvsa\Olcs\Api\Domain\ToggleAwareTrait;
use Dvsa\Olcs\Api\Domain\ToggleRequiredInterface;
use Dvsa\Olcs\Api\Entity\System\FeatureToggle;

/**
 * IRHP Window
 *
 * @author Andy Newton
 */
class GetList extends AbstractQueryHandler implements ToggleRequiredInterface
{
    use ToggleAwareTrait;

    protected $toggleConfig = [FeatureToggle::ADMIN_PERMITS];
    protected $repoServiceName = 'IrhpPermitWindow';
    protected $bundle = ['irhpPermitStock' => ['irhpPermitType' => ['name']], 'emissionsCategory'];

    public function handleQuery(QueryInterface $query)
    {
        $irhpPermitWindows = $this->getRepo()->fetchByIrhpPermitStockId($query->getIrhpPermitStock());

        return [
            'result' => $this->resultList(
                $irhpPermitWindows,
                $this->bundle
            )
        ];
    }
}
