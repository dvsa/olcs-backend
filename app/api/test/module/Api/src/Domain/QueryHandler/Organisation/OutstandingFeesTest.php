<?php

/**
 * Outstanding Fees Test
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\Organisation;

use Dvsa\Olcs\Api\Domain\QueryHandler\Organisation\OutstandingFees;
use Dvsa\Olcs\Api\Domain\QueryHandler\Result;
use Dvsa\Olcs\Api\Domain\Repository\Organisation as OrganisationRepo;
use Dvsa\Olcs\Api\Domain\Repository\Fee as FeeRepo;
use Dvsa\Olcs\Transfer\Query\Organisation\OutstandingFees as Qry;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Mockery as m;

/**
 * Outstanding Fees Test
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class OutstandingFeesTest extends QueryHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new OutstandingFees();
        $this->mockRepo('Organisation', OrganisationRepo::class);
        $this->mockRepo('Fee', FeeRepo::class);

        parent::setUp();
    }

    public function testHandleQuery()
    {
        $organisationId = 69;

        $query = Qry::create(['id' => $organisationId]);

        $mockOrganisation = m::mock('Dvsa\Olcs\Api\Domain\QueryHandler\BundleSerializableInterface')
            ->shouldReceive('getId')
            ->andReturn($organisationId)
            ->shouldReceive('serialize')
            // ->with()
            ->getMock();

        $fees = [
            $this->getMockFee(98, 198),
            $this->getMockFee(99, 199),
        ];

        $this->repoMap['Organisation']
            ->shouldReceive('fetchUsingId')
            ->with($query)
            ->once()
            ->andReturn($mockOrganisation);

        $this->repoMap['Fee']
            ->shouldReceive('fetchOutstandingFeesByOrganisationId')
            ->once()
            ->with($organisationId)
            ->andReturn($fees);

        $result = $this->sut->handleQuery($query);

        $this->assertInstanceOf(Result::class, $result);
    }

    private function getMockFee($feeId)
    {
        return m::mock()
            ->shouldReceive('serialize')
            ->shouldReceive('getId')
            ->andReturn($feeId)
            ->getMock();
    }
}
