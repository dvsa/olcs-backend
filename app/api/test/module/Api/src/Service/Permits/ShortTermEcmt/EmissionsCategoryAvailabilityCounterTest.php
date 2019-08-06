<?php

namespace Dvsa\OlcsTest\Api\Service\Permits\ShortTermEcmt;

use Doctrine\DBAL\Connection;
use Dvsa\Olcs\Api\Domain\Repository\IrhpPermitRange as IrhpPermitRangeRepository;
use Dvsa\Olcs\Api\Domain\Repository\IrhpPermitApplication as IrhpPermitApplicationRepository;
use Dvsa\Olcs\Api\Domain\Repository\IrhpPermit as IrhpPermitRepository;
use Dvsa\Olcs\Api\Entity\System\RefData;
use Dvsa\Olcs\Api\Service\Permits\ShortTermEcmt\EmissionsCategoryAvailabilityCounter;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use RuntimeException;

/**
 * EmissionsCategoryAvailabilityCounterTest
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class EmissionsCategoryAvailabilityCounterTest extends MockeryTestCase
{
    private $connection;

    private $irhpPermitRangeRepo;

    private $irhpPermitApplicationRepo;

    private $irhpPermitRepo;

    private $emissionsCategoryAvailabilityCounter;

    public function setUp()
    {
        $this->connection = m::mock(Connection::class);

        $this->irhpPermitRangeRepo = m::mock(IrhpPermitRangeRepository::class);

        $this->irhpPermitApplicationRepo = m::mock(IrhpPermitApplicationRepository::class);

        $this->irhpPermitRepo = m::mock(IrhpPermitRepository::class);

        $this->emissionsCategoryAvailabilityCounter = new EmissionsCategoryAvailabilityCounter(
            $this->connection,
            $this->irhpPermitRangeRepo,
            $this->irhpPermitApplicationRepo,
            $this->irhpPermitRepo
        );
    }

    public function testGetCount()
    {
        $irhpPermitStockId = 22;
        $emissionsCategoryId = RefData::EMISSIONS_CATEGORY_EURO5_REF;

        $this->connection->shouldReceive('getTransactionIsolation')
            ->withNoArgs()
            ->andReturn(Connection::TRANSACTION_REPEATABLE_READ);

        $this->connection->shouldReceive('beginTransaction')
            ->withNoArgs()
            ->once()
            ->ordered()
            ->globally();

        $this->irhpPermitRangeRepo->shouldReceive('getCombinedRangeSize')
            ->with($irhpPermitStockId, $emissionsCategoryId)
            ->once()
            ->ordered()
            ->globally()
            ->andReturn(40);

        $this->irhpPermitApplicationRepo->shouldReceive('getRequiredPermitCountWhereApplicationAwaitingPayment')
            ->with($irhpPermitStockId, $emissionsCategoryId)
            ->once()
            ->ordered()
            ->globally()
            ->andReturn(8);

        $this->irhpPermitRepo->shouldReceive('getPermitCount')
            ->with($irhpPermitStockId, $emissionsCategoryId)
            ->once()
            ->ordered()
            ->globally()
            ->andReturn(14);

        $this->connection->shouldReceive('commit')
            ->withNoArgs()
            ->once()
            ->ordered()
            ->globally();

        $this->assertEquals(
            18,
            $this->emissionsCategoryAvailabilityCounter->getCount($irhpPermitStockId, $emissionsCategoryId)
        );
    }

    public function testReturnZeroOnNullCombinedRangeSize()
    {
        $irhpPermitStockId = 47;
        $emissionsCategoryId = RefData::EMISSIONS_CATEGORY_EURO6_REF;

        $this->connection->shouldReceive('getTransactionIsolation')
            ->withNoArgs()
            ->andReturn(Connection::TRANSACTION_REPEATABLE_READ);

        $this->connection->shouldReceive('beginTransaction')
            ->withNoArgs()
            ->once()
            ->ordered()
            ->globally();

        $this->irhpPermitRangeRepo->shouldReceive('getCombinedRangeSize')
            ->with($irhpPermitStockId, $emissionsCategoryId)
            ->andReturn(null);

        $this->connection->shouldReceive('commit')
            ->withNoArgs()
            ->once()
            ->ordered()
            ->globally();

        $this->assertEquals(
            0,
            $this->emissionsCategoryAvailabilityCounter->getCount($irhpPermitStockId, $emissionsCategoryId)
        );
    }

    /**
     * @dataProvider dpTestExceptionOnUnexpectedIsolationLevel
     */
    public function testExceptionOnUnexpectedIsolationLevel($isolationLevel)
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage(EmissionsCategoryAvailabilityCounter::ERR_BAD_ISOLATION_LEVEL);

        $this->connection->shouldReceive('getTransactionIsolation')
            ->withNoArgs()
            ->andReturn($isolationLevel);

        $this->emissionsCategoryAvailabilityCounter->getCount(47, RefData::EMISSIONS_CATEGORY_EURO6_REF);
    }

    public function dpTestExceptionOnUnexpectedIsolationLevel()
    {
        return [
            [Connection::TRANSACTION_READ_UNCOMMITTED],
            [Connection::TRANSACTION_READ_COMMITTED],
            [Connection::TRANSACTION_SERIALIZABLE],
        ];
    }
}
