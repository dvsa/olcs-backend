<?php

namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\Permits;

use Dvsa\Olcs\Api\Domain\QueryHandler\Permits\LastOpenWindow;
use Dvsa\Olcs\Api\Domain\Util\DateTime\DateTime;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitStock;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitType;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitWindow;
use Dvsa\Olcs\Api\Domain\Repository\IrhpPermitType as IrhpPermitTypeRepo;
use Dvsa\Olcs\Api\Domain\Repository\IrhpPermitStock as IrhpPermitStockRepo;
use Dvsa\Olcs\Api\Domain\Repository\IrhpPermitWindow as IrhpPermitWindowRepo;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Mockery as m;

class LastOpenWindowTest extends QueryHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new LastOpenWindow();
        $this->mockRepo('IrhpPermitWindow', IrhpPermitWindowRepo::class);
        $this->mockRepo('IrhpPermitType', IrhpPermitTypeRepo::class);
        $this->mockRepo('IrhpPermitStock', IrhpPermitStockRepo::class);

        parent::setUp();
    }

    public function initReferences()
    {
        $this->references = [
            IrhpPermitType::class => [
                1 => m::mock(IrhpPermitType::class)
            ],
            IrhpPermitStock::class => [
                100 => m::mock(IrhpPermitStock::class),
                200 => m::mock(IrhpPermitStock::class)
            ]
        ];

        parent::initReferences();
    }

    public function testHandleQuery()
    {
        /** @var IrhpPermitType $irhpPermitType */
        $irhpPermitType = $this->references[IrhpPermitType::class][1];

        $stocks = $this->references[IrhpPermitStock::class];

        $tomorrow = new DateTime('tomorrow');
        $oneHourAgo = new DateTime('-1 hour');

        $irhpPermitWindowA['endDate'] = $tomorrow->format('Y-m-d H:i:s');
        $irhpPermitWindowA['irhpPermitStock'] = $stocks[100];

        $irhpPermitWindowB['endDate'] = $oneHourAgo->format('Y-m-d H:i:s');
        $irhpPermitWindowB['irhpPermitStock'] = $stocks[200];

        $now = new DateTime('now');
        $nowString = $now->format('Y-m-d H:i:s');

        $query = m::mock(QueryInterface::class);
        $query->shouldReceive('getCurrentDateTime')
            ->andReturn($nowString);

        $query->shouldReceive('getPermitType')
            ->andReturn(1);

        $this->repoMap['IrhpPermitType']
            ->shouldReceive('fetchById')
            ->with(1)
            ->andReturn($irhpPermitType);

        $this->repoMap['IrhpPermitStock']
            ->shouldReceive('fetchByIrhpPermitType')
            ->with(1)
            ->andReturn($stocks);

        $this->repoMap['IrhpPermitWindow']->shouldReceive('fetchLastOpenWindow')
            ->with(100, m::on(function ($dateTime) use ($nowString) {
                return ($dateTime->format('Y-m-d H:i:s') == $nowString);
            }))
            ->andReturn($irhpPermitWindowA);

        $this->repoMap['IrhpPermitWindow']->shouldReceive('fetchLastOpenWindow')
            ->with(200, m::on(function ($dateTime) use ($nowString) {
                return ($dateTime->format('Y-m-d H:i:s') == $nowString);
            }))
            ->andReturn($irhpPermitWindowB);

        $this->assertEquals(
            ['lastOpenWindow' => $irhpPermitWindowA],
            $this->sut->handleQuery($query)
        );
    }
}
