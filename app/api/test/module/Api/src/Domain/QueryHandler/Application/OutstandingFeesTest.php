<?php

/**
 * Outstanding Fees Test
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\Application;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\QueryHandler\Application\OutstandingFees;
use Dvsa\Olcs\Api\Domain\QueryHandler\Result;
use Dvsa\Olcs\Api\Domain\Repository\Application as ApplicationRepo;
use Dvsa\Olcs\Api\Entity\Application\Application as ApplicationEntity;
use Dvsa\Olcs\Api\Entity\Fee\Fee as FeeEntity;
use Dvsa\Olcs\Transfer\Query\Application\OutstandingFees as Qry;
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
        $this->sut = m::mock(OutstandingFees::class)
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();

        $this->mockRepo('Application', ApplicationRepo::class);

        parent::setUp();
    }

    public function testHandleQuery()
    {
        $applicationId = 111;

        $query = Qry::create(['id' => $applicationId]);

        $mockApplication = m::mock(ApplicationEntity::class)->makePartial();
        $mockApplication
            ->setId($applicationId)
            ->shouldReceive('serialize')
            ->andReturn(['id' => $applicationId]);

        $this->repoMap['Application']
            ->shouldReceive('fetchUsingId')
            ->with($query)
            ->once()
            ->andReturn($mockApplication);

        $applicationFee = $this->getMockFee('100');
        $interimFee =  $this->getMockFee('66.70');
        $fees = [$applicationFee, $interimFee];

        // mock getOutstandingFeesForApplication, it is tested elsewhere
        $this->sut
            ->shouldReceive('getOutstandingFeesForApplication')
            ->with($applicationId)
            ->once()
            ->andReturn($fees);

        $result = $this->sut->handleQuery($query);

        $this->assertInstanceOf(Result::class, $result);

        $this->assertEquals(
            [
                'id' => 111,
                'outstandingFeeTotal' => '166.70',
                'outstandingFees' => [
                    ['amount' => '100'],
                    ['amount' => '66.70'],
                ],
            ],
            $result->serialize()
        );
    }

    protected function getMockFee($amount)
    {
        $mock = m::mock(FeeEntity::class)->makePartial();
        $mock
            ->setAmount($amount)
            ->shouldReceive('serialize')
            ->andReturn(['amount' => $amount]);
        return $mock;
    }
}
