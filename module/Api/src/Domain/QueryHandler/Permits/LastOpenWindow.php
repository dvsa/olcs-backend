<?php

namespace Dvsa\Olcs\Api\Domain\QueryHandler\Permits;

use Dvsa\Olcs\Api\Domain\Exception\NotFoundException;
use Dvsa\Olcs\Api\Domain\Repository\IrhpPermitStock as IrhpPermitStockRepo;
use Dvsa\Olcs\Api\Domain\Repository\IrhpPermitWindow as IrhpPermitWindowRepo;
use Dvsa\Olcs\Api\Domain\Repository\IrhpPermitType as IrhpPermitTypeRepo;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitStock as IrhpPermitStockEntity;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitType as IrhpPermitTypeEntity;
use Dvsa\Olcs\Transfer\Query\Permits\LastOpenWindow as LastOpenWindowQuery;
use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Api\Domain\ToggleAwareTrait;
use Dvsa\Olcs\Api\Domain\ToggleRequiredInterface;
use Dvsa\Olcs\Api\Entity\System\FeatureToggle;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use DateTime;

/**
 * Last open window
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
     * @throws NotFoundException
     * @throws \Dvsa\Olcs\Api\Domain\Exception\RuntimeException
     */
    public function handleQuery(QueryInterface $query)
    {
        $date = DateTime::createFromFormat('Y-m-d H:i:s', $query->getCurrentDateTime());

        /** @var IrhpPermitWindowRepo $irhpPermitWindowRepo */
        $irhpPermitWindowRepo = $this->getRepo();

        /** @var IrhpPermitTypeRepo $irhpPermitTypeRepo */
        $irhpPermitTypeRepo = $this->getRepo('IrhpPermitType');

        /** @var IrhpPermitStockRepo $irhpPermitStockRepo */
        $irhpPermitStockRepo = $this->getRepo('IrhpPermitStock');

        /** @var IrhpPermitTypeEntity $irhpPermitType */
        $irhpPermitType = $irhpPermitTypeRepo->fetchById($query->getPermitType());

        $stocks = $irhpPermitStockRepo->fetchByIrhpPermitType($irhpPermitType->getId());

        $lastOpenWindow = [];

        /** @var IrhpPermitStockEntity $stock */
        foreach ($stocks as $stock) {
            $window = $irhpPermitWindowRepo->fetchLastOpenWindow($stock->getId(), $date)[0];

            if (isset($lastOpenWindow['endDate'])) {
                if (strtotime($window['endDate']) > strtotime($lastOpenWindow['endDate'])) {
                    $lastOpenWindow = $window;
                }
            } else {
                $lastOpenWindow = $window;
            }
        }

        return ['lastOpenWindow' => $lastOpenWindow];
    }
}
