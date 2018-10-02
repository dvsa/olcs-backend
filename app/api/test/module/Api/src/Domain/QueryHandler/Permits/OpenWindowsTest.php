<?php

namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\Permits;

use Dvsa\Olcs\Api\Domain\QueryHandler\Permits\OpenWindows;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitWindow;
use Dvsa\Olcs\Api\Domain\Repository\IrhpPermitWindow as IrhpPermitWindowRepo;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Mockery as m;
use DateTime;

class OpenWindowsTest extends QueryHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new OpenWindows();
        $this->mockRepo('IrhpPermitWindow', IrhpPermitWindowRepo::class);

        parent::setUp();
    }

    public function testHandleQuery()
    {
        $irhpPermitWindows = [
            m::mock(IrhpPermitWindow::class),
            m::mock(IrhpPermitWindow::class),
            m::mock(IrhpPermitWindow::class)
        ];

        $dateTimeAsString = '2018-04-01 14:30:00';

        $query = m::mock(QueryInterface::class);
        $query->shouldReceive('getCurrentDateTime')
            ->andReturn($dateTimeAsString);

        $this->repoMap['IrhpPermitWindow']->shouldReceive('fetchOpenWindows')
            ->with(m::on(function ($dateTime) use ($dateTimeAsString) {
                return ($dateTime->format('Y-m-d H:i:s') == $dateTimeAsString);
            }))
            ->andReturn($irhpPermitWindows);

        $this->assertEquals(
            ['windows' => $irhpPermitWindows],
            $this->sut->handleQuery($query)
        );
    }
}
