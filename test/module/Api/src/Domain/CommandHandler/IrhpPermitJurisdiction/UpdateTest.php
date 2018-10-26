<?php

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\IrhpPermitJurisdiction;

use Mockery as m;
use Dvsa\Olcs\Api\Domain\CommandHandler\IrhpPermitJurisdiction\Update as UpdateHandler;
use Dvsa\Olcs\Api\Domain\Repository\IrhpPermitJurisdiction as PermitJurisdictionQuotaRepo;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Transfer\Command\IrhpPermitJurisdiction\Update as UpdateCmd;

/**
 * Update IRHP Permit Jurisdiction Test
 *
 * @author Scott Callaway <scott.callaway@capgemini.com>
 */
class UpdateTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new UpdateHandler();
        $this->mockRepo('IrhpPermitJurisdictionQuota', PermitJurisdictionQuotaRepo::class);

        parent::setUp();
    }

    public function testHandleCommand()
    {
        $cmdData = [
            'irhpPermitStock' => 1,
            'trafficAreas' => [
                'B' => 100,
                'C' => 200,
                'D' => 300
            ]
        ];

        $command = UpdateCmd::create($cmdData);

        $this->repoMap['IrhpPermitJurisdictionQuota']
            ->shouldReceive('updateTrafficAreaPermitQuantity')
            ->with(
                $cmdData['trafficAreas']['B'],
                'B',
                $command->getIrhpPermitStock()
            )->once();

        $this->repoMap['IrhpPermitJurisdictionQuota']
            ->shouldReceive('updateTrafficAreaPermitQuantity')
            ->with(
                $cmdData['trafficAreas']['C'],
                'C',
                $command->getIrhpPermitStock()
            )->once();

        $this->repoMap['IrhpPermitJurisdictionQuota']
            ->shouldReceive('updateTrafficAreaPermitQuantity')
            ->with(
                $cmdData['trafficAreas']['D'],
                'D',
                $command->getIrhpPermitStock()
            )->once();

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => ['Irhp Permit Stock' => $command->getIrhpPermitStock()],
            'messages' => ["Irhp Permit Jurisdiction for Stock '1' updated"]
        ];

        $this->assertEquals($expected, $result->toArray());
    }
}
