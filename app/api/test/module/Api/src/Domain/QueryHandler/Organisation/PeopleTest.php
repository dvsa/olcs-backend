<?php

namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\Organisation;

use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Dvsa\Olcs\Api\Domain\QueryHandler\Organisation\People as QueryHandler;
use Dvsa\Olcs\Transfer\Query\Organisation\People as Qry;
use Mockery as m;

/**
 * PeopleTest
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class PeopleTest extends QueryHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new QueryHandler();
        $this->mockRepo('Organisation', \Dvsa\Olcs\Api\Domain\Repository\Organisation::class);

        parent::setUp();
    }

    public function testHandleQuery()
    {
        $query = Qry::create(['id' => 724]);

        $mockOrganisation = m::mock('Dvsa\Olcs\Api\Domain\QueryHandler\BundleSerializableInterface');
        $mockOrganisation->shouldReceive('isSoleTrader')->with()->once()->andReturn('IS_SOLE_TRADER');
        $mockOrganisation->shouldReceive('serialize')->with(
            [
                'organisationPersons' => [
                    'person' => ['title']
                ]
            ]
        )->once()->andReturn(['SERIALIZED']);

        $this->repoMap['Organisation']->shouldReceive('fetchUsingId')->with($query)->once()
            ->andReturn($mockOrganisation);

        $result = $this->sut->handleQuery($query);

        $this->assertSame(['SERIALIZED', 'isSoleTrader' => 'IS_SOLE_TRADER'], $result->serialize());
    }
}
