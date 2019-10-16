<?php

namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\IrhpApplication;

use Dvsa\Olcs\Api\Domain\QueryHandler\IrhpApplication\ById as IrhpApplicationByIdHandler;
use Dvsa\Olcs\Api\Domain\Repository\IrhpApplication as IrhpApplicationRepo;
use Dvsa\Olcs\Api\Entity\Permits\IrhpApplication as IrhpApplicationEntity;
use Dvsa\Olcs\Transfer\Query\IrhpApplication\ById as QryClass;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Exception;
use Mockery as m;

/**
 * ById Test
 *
 * @author Scott Callaway <scott.callaway@capgemini.com>
 */
class ByIdTest extends QueryHandlerTestCase
{
    protected $bundle = [
        'licence' => ['trafficArea', 'organisation'],
        'irhpPermitType' => ['name'],
        'fees' => ['feeType' => ['feeType'], 'feeStatus'],
        'irhpPermitApplications' => ['irhpPermitWindow' => ['irhpPermitStock' => ['country']]],
        'sectors',
    ];

    public function setUp()
    {
        $this->sut = new IrhpApplicationByIdHandler();
        $this->mockRepo('IrhpApplication', IrhpApplicationRepo::class);

        parent::setUp();
    }

    public function testHandleQuery()
    {
        $canViewCandidatePermits = true;
        $totalPermitsAwarded = 5;
        $totalPermitsRequired = 10;

        $irhpApplication = m::mock(IrhpApplicationEntity::class);
        $irhpApplication->shouldReceive('serialize')
            ->once()
            ->with($this->bundle)
            ->andReturn(['results' => ['serialized']])
            ->shouldReceive('canViewCandidatePermits')
            ->once()
            ->withNoArgs()
            ->andReturn($canViewCandidatePermits)
            ->shouldReceive('calculateTotalPermitsRequired')
            ->once()
            ->withNoArgs()
            ->andReturn($totalPermitsRequired)
            ->shouldReceive('getPermitsAwarded')
            ->once()
            ->withNoArgs()
            ->andReturn($totalPermitsAwarded);

        $query = QryClass::create(['id' => 1]);

        $this->repoMap['IrhpApplication']->shouldReceive('fetchUsingId')
            ->with($query)
            ->andReturn($irhpApplication);

        $this->assertEquals(
            [
                'results' => ['serialized'],
                'canViewCandidatePermits' => $canViewCandidatePermits,
                'totalPermitsAwarded' => $totalPermitsAwarded,
                'totalPermitsRequired' => $totalPermitsRequired,
            ],
            $this->sut->handleQuery($query)->serialize()
        );
    }

    public function testHandleQueryWithException()
    {
        $canViewCandidatePermits = true;

        $irhpApplication = m::mock(IrhpApplicationEntity::class);
        $irhpApplication->shouldReceive('serialize')
            ->once()
            ->with($this->bundle)
            ->andReturn(['results' => ['serialized']])
            ->shouldReceive('canViewCandidatePermits')
            ->once()
            ->withNoArgs()
            ->andReturn($canViewCandidatePermits)
            ->shouldReceive('calculateTotalPermitsRequired')
            ->once()
            ->withNoArgs()
            ->andReturn(10)
            ->shouldReceive('getPermitsAwarded')
            ->once()
            ->withNoArgs()
            ->andThrow(new Exception('error'));

        $query = QryClass::create(['id' => 1]);

        $this->repoMap['IrhpApplication']->shouldReceive('fetchUsingId')
            ->with($query)
            ->andReturn($irhpApplication);

        $this->assertEquals(
            [
                'results' => ['serialized'],
                'canViewCandidatePermits' => $canViewCandidatePermits,
                'totalPermitsAwarded' => null,
                'totalPermitsRequired' => null,
            ],
            $this->sut->handleQuery($query)->serialize()
        );
    }
}
