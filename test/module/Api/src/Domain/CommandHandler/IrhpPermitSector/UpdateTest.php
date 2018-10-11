<?php

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\IrhpPermitSector;

use Mockery as m;
use Dvsa\Olcs\Api\Domain\CommandHandler\IrhpPermitSector\Update as UpdateHandler;
use Dvsa\Olcs\Api\Domain\Repository\IrhpPermitStock as PermitStockRepo;
use Dvsa\Olcs\Api\Domain\Repository\IrhpPermitSector as PermitSectorQuotaRepo;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Transfer\Command\IrhpPermitSector\Update as UpdateCmd;
use Dvsa\Olcs\Api\Domain\Command\Result;

/**
 * Update IRHP Permit Sector Test
 *
 * @author Scott Callaway <scott.callaway@capgemini.com>
 */
class UpdateTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new UpdateHandler();
        $this->mockRepo('IrhpPermitSectorQuota', PermitSectorQuotaRepo::class);
        $this->mockRepo('IrhpPermitStock', PermitStockRepo::class);

        parent::setUp();
    }

    public function testHandleCommand()
    {
        $cmdData = [
            'irhpPermitStock' => 1,
            'sectors' => [
                1 => 100,
                2 => 200,
                3 => 300
            ]
        ];

        $command = UpdateCmd::create($cmdData);

        $this->repoMap['IrhpPermitSectorQuota']
            ->shouldReceive('updateSectorPermitQuantity')
            ->with(
                $cmdData['sectors']['1'],
                1,
                $command->getIrhpPermitStock()
            )->once();

        $this->repoMap['IrhpPermitSectorQuota']
            ->shouldReceive('updateSectorPermitQuantity')
            ->with(
                $cmdData['sectors']['2'],
                2,
                $command->getIrhpPermitStock()
            )->once();

        $this->repoMap['IrhpPermitSectorQuota']
            ->shouldReceive('updateSectorPermitQuantity')
            ->with(
                $cmdData['sectors']['3'],
                3,
                $command->getIrhpPermitStock()
            )->once();

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => ['Irhp Permit Stock' => $command->getIrhpPermitStock()],
            'messages' => ["Irhp Permit Sectors for Stock '1' updated"]
        ];

        $this->assertEquals($expected, $result->toArray());
    }
}
