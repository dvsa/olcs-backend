<?php

/**
 * Update Safety Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Licence;

use Mockery as m;
use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\Repository\Licence;
use Dvsa\Olcs\Api\Domain\CommandHandler\Licence\UpdateSafety;
use Dvsa\Olcs\Transfer\Command\Licence\UpdateSafety as Cmd;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Api\Entity\Licence\Licence as LicenceEntity;

/**
 * Update Safety Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class UpdateSafetyTest extends CommandHandlerTestCase
{
    public function setUp(): void
    {
        $this->sut = new UpdateSafety();
        $this->mockRepo('Licence', Licence::class);

        parent::setUp();
    }

    protected function initReferences()
    {
        $this->refData = [
            LicenceEntity::TACH_EXT
        ];

        $this->references = [];

        parent::initReferences();
    }

    public function testHandleCommand()
    {
        $data = [
            'id' => 222,
            'version' => 1,
            'safetyInsVehicles' => 2,
            'safetyInsTrailers' => 3,
            'safetyInsVaries' => 'Y',
            'tachographIns' => LicenceEntity::TACH_EXT,
            'tachographInsName' => 'Some name'
        ];

        $command = Cmd::create($data);

        /** @var LicenceEntity $licence */
        $licence = m::mock(LicenceEntity::class)->makePartial();
        $licence->shouldReceive('updateSafetyDetails')
            ->with(
                2,
                3,
                $this->refData[LicenceEntity::TACH_EXT],
                'Some name',
                'Y'
            );

        $this->repoMap['Licence']->shouldReceive('fetchUsingId')
            ->with($command, Query::HYDRATE_OBJECT, 1)
            ->once()
            ->andReturn($licence)
            ->shouldReceive('save')
            ->once()
            ->with($licence);

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [],
            'messages' => [
                'Licence updated'
            ]
        ];

        $this->assertEquals($expected, $result->toArray());
    }
}
