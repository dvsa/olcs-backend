<?php

namespace Dvsa\Olcs\Api\Domain\QueryHandler\IrhpPermitRange;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Dvsa\Olcs\Api\Domain\ToggleAwareTrait;
use Dvsa\Olcs\Api\Domain\ToggleRequiredInterface;
use Dvsa\Olcs\Api\Entity\System\FeatureToggle;

/**
 * IRHP Range
 *
 * @author Scott Callaway <scott.callaway@capgemini.com>
 */
class GetList extends AbstractQueryHandler implements ToggleRequiredInterface
{
    use ToggleAwareTrait;

    protected $toggleConfig = [FeatureToggle::ADMIN_PERMITS];
    protected $repoServiceName = 'IrhpPermitRange';

    private $bundle = ['countrys', 'irhpPermitStock' => ['irhpPermitType' => ['name']], 'emissionsCategory', 'journey'];

    public function handleQuery(QueryInterface $query)
    {
        $irhpPermitRanges = $this->getRepo()->fetchByIrhpPermitStockId($query->getIrhpPermitStock());
        return [
            'result' => $this->resultList(
                $irhpPermitRanges,
                $this->bundle
            )
        ];
    }
}
