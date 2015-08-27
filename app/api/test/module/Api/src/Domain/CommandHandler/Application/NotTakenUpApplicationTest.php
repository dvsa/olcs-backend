<?php

/**
 * NotTakenUpApplicationTest.php
 *
 * @author Josh Curtis <josh.curtis@valtech.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Application;

use Dvsa\Olcs\Api\Domain\Command\LicenceVehicle\RemoveLicenceVehicle;
use Dvsa\Olcs\Api\Entity\CommunityLic\CommunityLic;
use Dvsa\Olcs\Api\Entity\Tm\TransportManagerApplication;
use Dvsa\Olcs\Transfer\Command\Application\CreateSnapshot;
use Mockery as m;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Api\Domain\CommandHandler\Application\NotTakenUpApplication as CommandHandler;
use Dvsa\Olcs\Api\Domain\Command\Licence\NotTakenUp as Command;
use Dvsa\Olcs\Api\Entity\Application\Application;
use Dvsa\Olcs\Api\Domain\Command\Discs\CeaseGoodsDiscs;
use Dvsa\Olcs\Api\Domain\Command\Licence\NotTakenUp;
use Dvsa\Olcs\Transfer\Command\TransportManagerApplication\Delete;
use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Dvsa\Olcs\Api\Domain\Command\CommunityLic\Void;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\Repository\Application as ApplicationRepo;
use Dvsa\Olcs\Api\Domain\Repository\LicenceVehicle as LicenceVehicleRepo;

/**
 * Class WithdrawApplicationTest
 *
 * @package Dvsa\OlcsTest\Api\Domain\CommandHandler\Licence
 * @author Josh Curtis <josh.curtis@valtech.co.uk>
 */
class NotTakenUpApplicationTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new CommandHandler();
        $this->mockRepo('Application', ApplicationRepo::class);
        $this->mockRepo('LicenceVehicle', LicenceVehicleRepo::class);

        parent::setUp();
    }

    protected function initReferences()
    {
        $this->refData = [
            'apsts_ntu',
        ];

        parent::initReferences();
    }

    public function testHandleCommand()
    {
        $command = Command::create(['id' => 532]);

        $mockLicenceVehicle = m::mock()
            ->shouldReceive('setSpecifiedDate')->with(null)->once()
            ->shouldReceive('setInterimApplication')->with(null)->once()->getMock();

        $licence = m::mock(Licence::class)
            ->shouldReceive('getId')
            ->andReturn(123)
            ->shouldReceive('getLicenceVehicles')
            ->andReturn([$mockLicenceVehicle])
            ->times(3)
            ->getMock();

        $application = m::mock(Application::class)->makePartial();
        $application->setId(1);
        $application->setLicence($licence);

        $application->shouldReceive('getTransportManagers->toArray')
            ->once()
            ->andReturn(
                [
                    m::mock(TransportManagerApplication::class)
                        ->shouldReceive('getId')
                        ->once()
                        ->andReturn(1)
                        ->getMock(),
                    m::mock(TransportManagerApplication::class)
                        ->shouldReceive('getId')
                        ->once()
                        ->andReturn(2)
                        ->getMock(),
                ]
            );

            $licence->shouldReceive('getCommunityLics->toArray')
            ->once()
            ->andReturn(
                [
                    m::mock(CommunityLic::class)
                        ->shouldReceive('getId')
                        ->once()
                        ->andReturn(1)
                        ->getMock(),
                    m::mock(CommunityLic::class)
                        ->shouldReceive('getId')
                        ->once()
                        ->andReturn(2)
                        ->getMock(),
                ]
            );

        $this->repoMap['Application']->shouldReceive('fetchById')
            ->with(532)
            ->andReturn($application)
            ->shouldReceive('save')
            ->once()
            ->with(m::type(Application::class));

        $this->repoMap['LicenceVehicle']->shouldReceive('save')
            ->with($mockLicenceVehicle)
            ->once()
            ->getMock();

        $result1 = new Result();
        $result1->addMessage('Snapshot created');
        $this->expectedSideEffect(CreateSnapshot::class, ['id' => 532, 'event' => CreateSnapshot::ON_NTU], $result1);

        $this->expectedSideEffect(NotTakenUp::class, ['id' => 123], new Result());

        $this->expectedSideEffect(
            CeaseGoodsDiscs::class,
            [
                'licenceVehicles' => [$mockLicenceVehicle],
            ],
            new Result()
        );

        $this->expectedSideEffect(
            RemoveLicenceVehicle::class,
            [
                'licenceVehicles' => [$mockLicenceVehicle],
                'id' => null
            ],
            new Result()
        );

        $this->expectedSideEffect(Delete::class, ['ids' => array(1,2)], new Result());

        $this->expectedSideEffect(
            Void::class,
            [
                'licence' => $licence,
                'communityLicenceIds' => null,
                'checkOfficeCopy' => false
            ],
            new Result()
        );

        $result = $this->sut->handleCommand($command);

        $this->assertSame(['Snapshot created', 'Application 1 set to not taken up.'], $result->getMessages());
    }
}
