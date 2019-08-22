<?php

namespace Dvsa\Olcs\Api\Service\Permits\ShortTermEcmt;

use Doctrine\DBAL\Connection;
use Dvsa\Olcs\Api\Domain\Repository\IrhpPermitRange as IrhpPermitRangeRepository;
use Dvsa\Olcs\Api\Domain\Repository\IrhpPermitApplication as IrhpPermitApplicationRepository;
use Dvsa\Olcs\Api\Domain\Repository\IrhpPermit as IrhpPermitRepository;
use RuntimeException;

class EmissionsCategoryAvailabilityCounter
{
    const ERR_BAD_ISOLATION_LEVEL = 'Transaction isolation level must be set to REPEATABLE_READ';

    /** @var Connection */
    private $connection;

    /** @var IrhpPermitRangeRepository */
    private $irhpPermitRangeRepo;

    /** @var IrhpPermitApplicationRepository */
    private $irhpPermitApplicationRepo;

    /** @var IrhpPermitRepository */
    private $irhpPermitRepo;

    /**
     * Create service instance
     *
     * @param Connection $connection
     * @param IrhpPermitRangeRepository $irhpPermitRangeRepo
     * @param IrhpPermitApplicationRepository $irhpPermitApplicationRepo
     * @param IrhpPermitRepository $irhpPermitRepo
     *
     * @return EmissionsCategoryAvailabilityCounter
     */
    public function __construct(
        Connection $connection,
        IrhpPermitRangeRepository $irhpPermitRangeRepo,
        IrhpPermitApplicationRepository $irhpPermitApplicationRepo,
        IrhpPermitRepository $irhpPermitRepo
    ) {
        $this->connection = $connection;
        $this->irhpPermitRangeRepo = $irhpPermitRangeRepo;
        $this->irhpPermitApplicationRepo = $irhpPermitApplicationRepo;
        $this->irhpPermitRepo = $irhpPermitRepo;
    }

    /**
     * Get the count of permits available to apply for within the scope of a specific short term stock and emissions
     * category, using a transaction to ensure read consistency
     *
     * @param int $irhpPermitStockId
     * @param int $emissionsCategoryId
     *
     * @return int
     *
     * @throws RuntimeException
     */
    public function getCount($irhpPermitStockId, $emissionsCategoryId)
    {
        if ($this->connection->getTransactionIsolation() != Connection::TRANSACTION_REPEATABLE_READ) {
            throw new RuntimeException(self::ERR_BAD_ISOLATION_LEVEL);
        }

        $this->connection->beginTransaction();
        $count = $this->getCountUsingQueries($irhpPermitStockId, $emissionsCategoryId);
        $this->connection->commit();

        return $count;
    }

    /**
     * Run queries to get the count of permits available to apply for
     *
     * @param int $irhpPermitStockId
     * @param int $emissionsCategoryId
     *
     * @return int
     */
    private function getCountUsingQueries($irhpPermitStockId, $emissionsCategoryId)
    {
        $combinedRangeSize = $this->irhpPermitRangeRepo->getCombinedRangeSize(
            $irhpPermitStockId,
            $emissionsCategoryId
        );

        if (is_null($combinedRangeSize)) {
            return 0;
        }

        $permitsGranted = $this->irhpPermitApplicationRepo->getRequiredPermitCountWhereApplicationAwaitingPayment(
            $irhpPermitStockId,
            $emissionsCategoryId
        );

        $permitsAllocated = $this->irhpPermitRepo->getPermitCount($irhpPermitStockId, $emissionsCategoryId);

        return ($combinedRangeSize - ($permitsGranted + $permitsAllocated));
    }
}
