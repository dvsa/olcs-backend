<?php

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\IrhpPermitWindow;

use Mockery as m;
use Dvsa\Olcs\Api\Domain\CommandHandler\IrhpPermitWindow\Create as CreateHandler;
use Dvsa\Olcs\Api\Domain\Repository\IrhpPermitStock as PermitStockRepo;
use Dvsa\Olcs\Api\Domain\Repository\IrhpPermitWindow as PermitWindowRepo;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Transfer\Command\IrhpPermitWindow\Create as CreateCmd;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitWindow as PermitWindowEntity;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitStock;

/**
 * Create IRHP Permit Type Test
 *
 * @author Scott Callaway <scott.callaway@capgemini.com>
 */
class CreateTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new CreateHandler();
        $this->mockRepo('IrhpPermitWindow', PermitWindowRepo::class);
        $this->mockRepo('IrhpPermitStock', PermitStockRepo::class);

        parent::setUp();
    }

    public function testHandleCommand()
    {
        $cmdData = [
            'irhpPermitStock' => '1',
            'startDate' => '2019-01-01',
            'endDate' => '2019-01-10',
            'daysForPayment' => '14'
        ];


        $this->repoMap['IrhpPermitWindow']
            ->shouldReceive('findOverlappingWindowsByType')
            ->once()
            ->andReturn([]);

        $command = CreateCmd::create($cmdData);

        $this->repoMap['IrhpPermitStock']
            ->shouldReceive('fetchById')
            ->andReturn(m::mock(IrhpPermitStock::class)->makePartial());

        $this->repoMap['IrhpPermitWindow']
            ->shouldReceive('save')
            ->once()
            ->with(m::type(PermitWindowEntity::class))
            ->andReturnUsing(
                function (PermitWindowEntity $permitWindow) use (&$savedPermitStock) {
                    $permitWindow->setId(1);
                    $savedPermitStock = $permitWindow;
                }
            );

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => ['IrhpPermitWindow' => 1],
            'messages' => ["IRHP Permit Window '1' created"]
        ];

        $this->assertEquals($expected, $result->toArray());
    }
}
