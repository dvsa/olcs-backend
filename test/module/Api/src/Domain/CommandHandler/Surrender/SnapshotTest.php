<?php

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Surrender;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\Surrender\Snapshot;
use Dvsa\Olcs\Api\Entity\Surrender;
use Dvsa\Olcs\Transfer\Command\Document\Upload;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Api\Domain\Repository;
use Dvsa\Olcs\Api\Entity\System\Category;
use Dvsa\Olcs\Api\Domain\Command\Surrender\Snapshot as Command;
use Dvsa\Olcs\Snapshot\Service\Snapshots\Surrender\Generator;
use Mockery as m;

class SnapshotTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new Snapshot();
        $this->mockRepo('Snapshot', Repository\TransportManagerApplication::class);

        $this->mockedSmServices[Generator::class] = m::mock(Generator::class);

        parent::setUp();
    }

    public function testHandleCommand($tmaStatus, $expectedString)
    {
        $command = Command::create(['id' => 111]);

        $mockSurrenderEntity = m::mock(Surrender::class);

        $this->mockedSmServices[Generator::class]->shouldReceive('generate')
            ->once()
            ->with($mockSurrenderEntity)
            ->andReturn('<markup>');



    }
}