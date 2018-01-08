<?php

/**
 * ProposeToRevokeByCase Test
 */
namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\Cases\ProposeToRevoke;

use Dvsa\Olcs\Api\Domain\QueryHandler\Result;
use Mockery as m;
use Dvsa\Olcs\Api\Domain\QueryHandler\Cases\ProposeToRevoke\ProposeToRevokeByCase;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Dvsa\Olcs\Api\Domain\Repository\ProposeToRevoke as ProposeToRevokeRepo;
use Dvsa\Olcs\Api\Domain\QueryHandler\BundleSerializableInterface;
use Dvsa\Olcs\Transfer\Query\Cases\ProposeToRevoke\ProposeToRevokeByCase as Qry;

/**
 * ProposeToRevokeByCase Test
 */
class ProposeToRevokeByCaseTest extends QueryHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new ProposeToRevokeByCase();
        $this->mockRepo('ProposeToRevoke', ProposeToRevokeRepo::class);

        parent::setUp();
    }

    public function testHandleQuery()
    {
        $query = Qry::create(['case' => 1]);

        $this->repoMap['ProposeToRevoke']
            ->shouldReceive('disableSoftDeleteable')
            ->with(
                [
                    \Dvsa\Olcs\Api\Entity\Pi\Reason::class
                ]
            )
            ->once();
        $this->repoMap['ProposeToRevoke']
            ->shouldReceive('fetchProposeToRevokeUsingCase')
            ->with($query)
            ->andReturn(
                m::mock(BundleSerializableInterface::class)
                    ->shouldReceive('serialize')
                    ->with(['presidingTc', 'reasons', 'assignedCaseworker'])
                    ->andReturn(['foo'])
                    ->getMock()
            );

        /** @var Result $result */
        $result = $this->sut->handleQuery($query);
        $this->assertEquals(['foo'], $result->serialize());
    }
}
