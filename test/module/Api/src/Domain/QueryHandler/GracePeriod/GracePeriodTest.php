<?php

/**
 * Grace Period Test
 *
 * @author Joshua Curtis <josh.curtis@valtech.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\GracePeriod;

use Dvsa\Olcs\Api\Domain\QueryHandler\GracePeriod\GracePeriod;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Dvsa\Olcs\Api\Domain\Repository\GracePeriod as GracePeriodRepo;
use Dvsa\Olcs\Transfer\Query\GracePeriod\GracePeriod as Qry;

/**
 * Grace Periods Test
 *
 * @author Joshua Curtis <josh.curtis@valtech.co.uk>
 */
class GracePeriodTest extends QueryHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new GracePeriod();
        $this->mockRepo('GracePeriod', GracePeriodRepo::class);

        parent::setUp();
    }

    public function testHandleQuery()
    {
        $query = Qry::create(['id' => 1]);

        $this->repoMap['GracePeriod']
            ->shouldReceive('fetchUsingId')
            ->with($query)
            ->andReturn(
                [
                    [
                        'id' => 1
                    ],
                ]
            );

        $this->sut->handleQuery($query);
    }
}
