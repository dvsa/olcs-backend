<?php

namespace Dvsa\Olcs\Api\Domain\QueryHandler\Permits;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\Repository\IrhpPermitStock;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitType;
use Dvsa\Olcs\Transfer\Query\Permits\OpenWindows as OpenWindowsQuery;
use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Api\Domain\ToggleAwareTrait;
use Dvsa\Olcs\Api\Domain\ToggleRequiredInterface;
use Dvsa\Olcs\Api\Entity\System\FeatureToggle;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use DateTime;

/**
 * Open windows
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class OpenWindows extends AbstractQueryHandler implements ToggleRequiredInterface
{
    use ToggleAwareTrait;

    protected $toggleConfig = [FeatureToggle::BACKEND_ECMT];
    protected $repoServiceName = 'IrhpPermitWindow';
    protected $extraRepos = ['IrhpPermitType', 'IrhpPermitStock'];

    /**
     * Handle query
     *
     * @param QueryInterface|OpenWindowsQuery $query query
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

        foreach ($stocks as $stock) {
            $openWindows = $this->getRepo()->fetchOpenWindows($stock->getId(), $date);

            if (!empty($openWindows[0])) {
                return ['windows' => $openWindows];
            }
        }

        return ['windows' => []];
    }
}
