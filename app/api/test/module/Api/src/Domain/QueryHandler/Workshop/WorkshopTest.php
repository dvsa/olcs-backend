<?php

/**
 * Workshop Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\Workshop;

use Dvsa\Olcs\Api\Domain\QueryHandler\Workshop\Workshop;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Dvsa\Olcs\Api\Domain\Repository\Workshop as WorkshopRepo;
use Dvsa\Olcs\Transfer\Query\Workshop\Workshop as Qry;

/**
 * Workshop Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class WorkshopTest extends QueryHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new Workshop();
        $this->mockRepo('Workshop', WorkshopRepo::class);

        parent::setUp();
    }

    public function testHandleQuery()
    {
        $query = Qry::create(['id' => 111]);

        $this->repoMap['Workshop']->shouldReceive('fetchUsingId')
            ->with($query)
            ->andReturn(['foo']);

        $this->assertEquals(['foo'], $this->sut->handleQuery($query));
    }
}
