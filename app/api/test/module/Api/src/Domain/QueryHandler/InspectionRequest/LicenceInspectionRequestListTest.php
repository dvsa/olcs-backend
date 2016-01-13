<?php

/**
 * Licence Inspection Request Test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\InspectionRequest;

use Dvsa\Olcs\Api\Domain\QueryHandler\InspectionRequest\LicenceInspectionRequestList as QueryHandler;
use Dvsa\Olcs\Transfer\Query\InspectionRequest\LicenceInspectionRequestList as Query;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Dvsa\Olcs\Api\Domain\Repository\InspectionRequest as InspectionRequestRepo;

/**
 * Licence Inspection Request List Test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class LicenceInspectionRequestListTest extends QueryHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new QueryHandler();
        $this->mockRepo('InspectionRequest', InspectionRequestRepo::class);

        parent::setUp();
    }

    public function testHandleQuery()
    {
        $query = Query::create(['licence' => 1]);

        $this->repoMap['InspectionRequest']
            ->shouldReceive('fetchList')
            ->with($query)
            ->andReturn('foo')
            ->once()
            ->shouldReceive('fetchCount')
            ->with($query)
            ->andReturn('bar')
            ->once()
            ->getMock();

        $this->assertSame(['result' => 'foo', 'count' => 'bar'], $this->sut->handleQuery($query));
    }
}
