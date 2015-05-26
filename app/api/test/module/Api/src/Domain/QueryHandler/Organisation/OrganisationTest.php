<?php

/**
 * Organisation Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\Organisation;

use Dvsa\Olcs\Api\Domain\QueryHandler\Organisation\Organisation;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Dvsa\Olcs\Api\Domain\Repository\Organisation as OrganisationRepo;
use Dvsa\Olcs\Transfer\Query\Organisation\Organisation as Qry;

/**
 * Organisation Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class OrganisationTest extends QueryHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new Organisation();
        $this->mockRepo('Organisation', OrganisationRepo::class);

        parent::setUp();
    }

    public function testHandleQuery()
    {
        $query = Qry::create(['id' => 111]);

        $this->repoMap['Organisation']->shouldReceive('fetchUsingId')
            ->with($query)
            ->andReturn(['id' => 111])
            ->shouldReceive('hasInforceLicences')
            ->with(111)
            ->andReturn(true);

        $this->assertEquals(['id' => 111, 'hasInforceLicences' => true], $this->sut->handleQuery($query));
    }
}
