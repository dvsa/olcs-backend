<?php

namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\Permits;

use Dvsa\Olcs\Api\Domain\QueryHandler\Permits\AvailableTypes;
use Dvsa\Olcs\Api\Domain\Repository\IrhpPermitType as IrhpPermitTypeRepo;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitType;
use Dvsa\Olcs\Transfer\Query\Permits\AvailableTypes as AvailableTypesQuery;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Mockery as m;
use DateTime;

class AvailableTypesTest extends QueryHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new AvailableTypes();
        $this->mockRepo('IrhpPermitType', IrhpPermitTypeRepo::class);

        parent::setUp();
    }

    public function testHandleQuery()
    {
        $irhpPermitTypes = [
            m::mock(IrhpPermitType::class),
            m::mock(IrhpPermitType::class),
            m::mock(IrhpPermitType::class)
        ];

        $query = AvailableTypesQuery::create([]);

        $this->repoMap['IrhpPermitType']->shouldReceive('fetchAvailableTypes')
            ->with(m::type(DateTime::class))
            ->andReturn($irhpPermitTypes);

        $this->assertEquals(
            ['types' => $irhpPermitTypes],
            $this->sut->handleQuery($query)
        );
    }
}
