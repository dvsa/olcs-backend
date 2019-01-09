<?php

namespace Dvsa\Olcs\Api\Domain\QueryHandler\Permits;

use Dvsa\Olcs\Api\Domain\Repository\IrhpPermitStock;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitType;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitWindow;
use Dvsa\Olcs\Transfer\Query\Permits\LastOpenWindow as LastOpenWindowQuery;
use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Api\Domain\ToggleAwareTrait;
use Dvsa\Olcs\Api\Domain\ToggleRequiredInterface;
use Dvsa\Olcs\Api\Entity\System\FeatureToggle;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use DateTime;

/**
 * Last open window
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class LastOpenWindow extends AbstractQueryHandler implements ToggleRequiredInterface
{
    use ToggleAwareTrait;

    protected $toggleConfig = [FeatureToggle::BACKEND_ECMT];
    protected $repoServiceName = 'IrhpPermitWindow';
    protected $extraRepos = ['IrhpPermitType', 'IrhpPermitStock'];

    /**
     * Handle query
     *
     * @param QueryInterface|LastOpenWindowQuery $query query
     *
     * @return array
     */
    public function handleQuery(QueryInterface $query)
    {
        $date = DateTime::createFromFormat('Y-m-d H:i:s', $query->getCurrentDateTime());

        /** @var IrhpPermitType $irhpPermitType */
        $irhpPermitType = $this->getRepo('IrhpPermitType')->fetchById($query->getPermitType());

        /** @var IrhpPermitStock $irhpPermitStockRepo */
        $irhpPermitStockRepo = $this->getRepo('IrhpPermitStock');

        $stocks = $irhpPermitStockRepo->fetchByIrhpPermitType($irhpPermitType->getId());

        $lastOpenWindow = [];

        foreach ($stocks as $stock) {
            /** @var IrhpPermitWindow $window */
            $window = $this->getRepo()->fetchLastOpenWindow($stock->getId(), $date)[0];

            if (empty($lastOpenWindow) || strtotime($window['endDate']) > strtotime($lastOpenWindow['endDate'])) {
                $lastOpenWindow = $window;
            }
        }

        return ['lastOpenWindow' => $lastOpenWindow];
    }
}
