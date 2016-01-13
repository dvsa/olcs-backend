<?php

/**
 * Grace Periods Test
 *
 * @author Joshua Curtis <josh.curtis@valtech.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\GracePeriod;

use Doctrine\ORM\Query;

use Dvsa\Olcs\Api\Domain\QueryHandler\GracePeriod\GracePeriods;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Dvsa\Olcs\Api\Domain\Repository\GracePeriod as GracePeriodRepo;
use Dvsa\Olcs\Transfer\Query\GracePeriod\GracePeriods as Qry;

/**
 * Grace Periods Test
 *
 * @author Joshua Curtis <josh.curtis@valtech.co.uk>
 */
class GracePeriodsTest extends QueryHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new GracePeriods();
        $this->mockRepo('GracePeriod', GracePeriodRepo::class);

        parent::setUp();
    }

    public function testHandleQuery()
    {
        $query = Qry::create(['licence' => 1]);

        $this->repoMap['GracePeriod']
            ->shouldReceive('fetchList')
            ->with($query, Query::HYDRATE_OBJECT)
            ->andReturn(
                [
                    [
                        'id' => 1
                    ],
                    [
                        'id' => 2
                    ]
                ]
            )
            ->shouldReceive('fetchCount');

        $this->sut->handleQuery($query);
    }
}
