<?php

/**
 * WithdrawApplication.php
 *
 * @author Josh Curtis <josh.curtis@valtech.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Application;

use Dvsa\Olcs\Transfer\Command\Application\CreateSnapshot;
use Mockery as m;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Api\Domain\CommandHandler\Application\WithdrawApplication as CommandHandler;
use Dvsa\Olcs\Transfer\Command\Application\WithdrawApplication as Command;
use Dvsa\Olcs\Api\Entity\Application\Application;
use Dvsa\Olcs\Api\Domain\Command\Discs\CeaseGoodsDiscs;
use Dvsa\Olcs\Api\Domain\Command\Licence\Withdraw;
use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\Repository\Application as ApplicationRepo;
use Dvsa\Olcs\Api\Domain\Repository\LicenceVehicle as LicenceVehicleRepo;

/**
 * Withdraw Application Test
 *
 * @author Josh Curtis <josh.curtis@valtech.co.uk>
 */
class WithdrawApplicationTest extends CommandHandlerTestCase
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
            'apsts_withdrawn', 'withdrawn'
        ];

        parent::initReferences();
    }

    public function testHandleCommand()
    {
        $command = Command::create(['id' => 532, 'reason' => 'withdrawn']);

        $mockLicenceVehicle = m::mock()
            ->shouldReceive('setSpecifiedDate')->with(null)->once()
            ->shouldReceive('setInterimApplication')->with(null)->once()->getMock();

        $licence = m::mock(Licence::class)
            ->shouldReceive('getId')
            ->andReturn(123)
            ->shouldReceive('getLicenceVehicles')
            ->andReturn([$mockLicenceVehicle])
            ->twice()
            ->getMock();

        $application = m::mock(Application::class)->makePartial();
        $application->setId(1);
        $application->setLicence($licence);
        $application->shouldReceive('getIsVariation')->andReturn(false);

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

        $this->expectedSideEffect(Withdraw::class, ['id' => 123], new Result());
        $this->expectedSideEffect(CeaseGoodsDiscs::class, ['licenceVehicles' => [$mockLicenceVehicle]], new Result());

        $result1 = new Result();
        $result1->addMessage('Snapshot created');
        $data = ['id' => 532, 'event' => CreateSnapshot::ON_WITHDRAW];
        $this->expectedSideEffect(CreateSnapshot::class, $data, $result1);

        $result = $this->sut->handleCommand($command);

        $this->assertSame(['Snapshot created', 'Application 1 withdrawn.'], $result->getMessages());
    }
}
