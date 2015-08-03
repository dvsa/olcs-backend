<?php

/**
 * CreateForTml Test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\OtherLicence;

use Dvsa\Olcs\Api\Domain\CommandHandler\OtherLicence\CreateForTml as CommandHandler;
use Dvsa\Olcs\Api\Domain\Repository\OtherLicence;
use Dvsa\Olcs\Api\Entity\OtherLicence\OtherLicence as OtherLicenceEntity;
use Dvsa\Olcs\Api\Entity\Tm\TransportManagerLicence;
use Dvsa\Olcs\Transfer\Command\OtherLicence\CreateForTml as Command;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Mockery as m;

/**
 * CreateForTml Test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class CreateForTmlTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new CommandHandler();
        $this->mockRepo('OtherLicence', OtherLicence::class);

        parent::setUp();
    }

    protected function initReferences()
    {
        $this->refData = [
            'role',
        ];

        $this->references = [
            TransportManagerLicence::class => [
                12 => m::mock(TransportManagerLicence::class)
            ]
        ];

        parent::initReferences();
    }

    public function testHandleCommand()
    {
        $command = Command::create(
            [
                'role' => 'role',
                'tmlId' => 12,
                'hoursPerWeek' => 12,
                'licNo' => 'LIC001',
                'operatingCentres' => 'oc',
                'totalAuthVehicles' => 64
            ]
        );

        $this->repoMap['OtherLicence']
            ->shouldReceive('save')->once()->andReturnUsing(
                function (OtherLicenceEntity $ol) {
                    $ol->setId(422);
                    $this->assertSame(
                        $this->references[TransportManagerLicence::class][12],
                        $ol->getTransportManagerLicence()
                    );
                    $this->assertSame($this->refData['role'], $ol->getRole());
                    $this->assertSame(12, $ol->getHoursPerWeek());
                    $this->assertSame('LIC001', $ol->getLicNo());
                    $this->assertSame('oc', $ol->getOperatingCentres());
                    $this->assertSame(64, $ol->getTotalAuthVehicles());
                }
            );

        $response = $this->sut->handleCommand($command);

        $this->assertSame(['otherLicence' => 422], $response->getIds());
        $this->assertSame(['Other licence created'], $response->getMessages());
    }
}
