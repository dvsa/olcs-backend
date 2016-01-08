<?php

/**
 * NotTakenUpList Test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\Application;

use Dvsa\Olcs\Api\Domain\QueryHandler\Application\NotTakenUpList;
use Dvsa\Olcs\Api\Domain\Repository\Application as ApplicationRepo;
use Dvsa\Olcs\Api\Domain\Repository\TrafficArea as TrafficAreaRepo;
use Dvsa\Olcs\Api\Domain\Repository\PublicHoliday as PublicHolidayRepo;
use Dvsa\Olcs\Api\Domain\Query\Application\NotTakenUpList as Qry;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Dvsa\Olcs\Api\Domain\Util\DateTime\DateTime;
use Dvsa\Olcs\Api\Entity\TrafficArea\TrafficArea as TrafficAreaEntity;
use Mockery as m;

/**
 * NotTakenUpList Test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class NotTakenUpListTest extends QueryHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new NotTakenUpList();
        $this->mockRepo('Application', ApplicationRepo::class);
        $this->mockRepo('TrafficArea', TrafficAreaRepo::class);
        $this->mockRepo('PublicHoliday', PublicHolidayRepo::class);

        $this->mockedSmServices = [
            'FeesHelperService' => m::mock(),
            'SectionAccessService' => m::mock(),
        ];

        parent::setUp();
    }

    public function testHandleQuery()
    {
        $trafficAreaId = 1;
        $query = Qry::create(['date' => new DateTime('2015-01-01')]);

        $mockTrafficArea = m::mock(TrafficAreaEntity::class)
            ->shouldReceive('getId')
            ->andReturn($trafficAreaId)
            ->once()
            ->getMock();

        $this->repoMap['TrafficArea']
            ->shouldReceive('fetchAll')
            ->andReturn([$mockTrafficArea])
            ->once();

        $mockApplication = m::mock()
            ->shouldReceive('getLicence')
            ->andReturn(
                m::mock()
                ->shouldReceive('getTrafficArea')
                ->andReturn(
                    m::mock()
                    ->shouldReceive('getId')
                    ->andReturn($trafficAreaId)
                    ->once()
                    ->getMock()
                )
                ->once()
                ->getMock()
            )
            ->shouldReceive('getGrantedDate')
            ->andReturn('2014-12-01')
            ->once()
            ->shouldReceive('getId')
            ->andReturn(999)
            ->once()
            ->getMock();

        $this->repoMap['Application']
            ->shouldReceive('fetchForNtu')
            ->andReturn([$mockApplication])
            ->once();

        $this->repoMap['PublicHoliday']
            ->shouldReceive('fetchBetweenByTa')
            ->with(m::type(DateTime::class), m::type(DateTime::class), $mockTrafficArea)
            ->andReturn([1, 2, 3]);

        $result = $this->sut->handleQuery($query);

        $this->assertEquals(1, $result['count']);
        $this->assertEquals(999, $result['result'][0]->getId());
    }
}
