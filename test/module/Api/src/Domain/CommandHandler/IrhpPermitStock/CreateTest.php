<?php

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\IrhpPermitStock;

use Mockery as m;
use Dvsa\Olcs\Api\Domain\CommandHandler\IrhpPermitStock\Create as CreateHandler;
use Dvsa\Olcs\Api\Domain\Repository\IrhpPermitStock as PermitStockRepo;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Transfer\Command\IrhpPermitStock\Create as CreateCmd;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitStock as PermitStockEntity;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitType;

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
        $this->mockRepo('IrhpPermitStock', PermitStockRepo::class);
        $this->mockRepo('IrhpPermitType', PermitTypeRepo::class);

        parent::setUp();
    }

    public function testHandleCommand()
    {
        $cmdData = [
            'permitType' => '1',
            'validFrom' => '2019-01-01',
            'validTo' => '2019-02-01',
            'initialStock' => '1500'
        ];

        $command = CreateCmd::create($cmdData);

        $this->repoMap['IrhpPermitType']
            ->shouldReceive('fetchById')
            ->andReturn(m::mock(IrhpPermitType::class)->makePartial());

        $this->repoMap['IrhpPermitStock']
            ->shouldReceive('save')
            ->once()
            ->with(m::type(PermitStockEntity::class))
            ->andReturnUsing(
                function (PermitStockEntity $permitStock) use (&$savedPermitStock) {
                    $permitStock->setId(1);
                    $savedPermitStock = $permitStock;
                }
            );

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => ['IrhpPermitStock' => 1],
            'messages' => ["IRHP Permit Stock '1' created"]
        ];

        $this->assertEquals($expected, $result->toArray());
    }
}
