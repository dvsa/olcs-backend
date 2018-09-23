<?php

namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\Permits;

use Dvsa\Olcs\Api\Domain\QueryHandler\Permits\LastOpenWindow;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitWindow;
use Dvsa\Olcs\Api\Domain\Repository\IrhpPermitWindow as IrhpPermitWindowRepo;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Mockery as m;
use DateTime;

class LastOpenWindowTest extends QueryHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new LastOpenWindow();
        $this->mockRepo('IrhpPermitWindow', IrhpPermitWindowRepo::class);

        parent::setUp();
    }

    public function testHandleQuery()
    {
        $irhpPermitWindow = m::mock(IrhpPermitWindow::class);

        $dateTimeAsString = '2018-04-01 14:30:00';

        $query = m::mock(QueryInterface::class);
        $query->shouldReceive('getCurrentDateTime')
            ->andReturn($dateTimeAsString);

        $this->repoMap['IrhpPermitWindow']->shouldReceive('fetchLastOpenWindow')
            ->with(m::on(function ($dateTime) use ($dateTimeAsString) {
                return ($dateTime->format('Y-m-d H:i:s') == $dateTimeAsString);
            }))
            ->andReturn($irhpPermitWindow);

        $this->assertEquals(
            ['result' => $irhpPermitWindow],
            $this->sut->handleQuery($query)
        );
    }
}
