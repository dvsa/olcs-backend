<?php

/**
 * RefuseApplicationTest.php
 *
 * @author Josh Curtis <josh.curtis@valtech.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Application;

use Dvsa\Olcs\Api\Domain\Command\Licence\ReturnAllCommunityLicences;
use Dvsa\Olcs\Transfer\Command\Application\CreateSnapshot;
use Mockery as m;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Api\Domain\CommandHandler\Application\RefuseApplication as CommandHandler;
use Dvsa\Olcs\Transfer\Command\Application\RefuseApplication as Command;
use Dvsa\Olcs\Api\Entity\Application\Application;
use Dvsa\Olcs\Api\Domain\Command\Discs\CeaseGoodsDiscs;
use Dvsa\Olcs\Api\Domain\Command\Licence\Refuse;
use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\Repository\Application as ApplicationRepo;
use Dvsa\Olcs\Api\Domain\Repository\LicenceVehicle as LicenceVehicleRepo;

/**
 * Refuse Application Test
 *
 * @author Josh Curtis <josh.curtis@valtech.co.uk>
 */
class RefuseApplicationTest extends CommandHandlerTestCase
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
            'apsts_refused'
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
            ->twice()
            ->shouldReceive('getCommunityLics')
            ->andReturn(
                m::mock()
                    ->shouldReceive('toArray')
                    ->once()
                    ->andReturn([1,2,3])
                    ->getMock()
            )
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

        $this->expectedSideEffect(Refuse::class, ['id' => 123], new Result());
        $this->expectedSideEffect(CeaseGoodsDiscs::class, ['licenceVehicles' => [$mockLicenceVehicle]], new Result());

        $result1 = new Result();
        $result1->addMessage('Snapshot created');
        $this->expectedSideEffect(CreateSnapshot::class, ['id' => 532, 'event' => CreateSnapshot::ON_REFUSE], $result1);

        $this->expectedSideEffect(ReturnAllCommunityLicences::class, ['id' => 123], new Result());

        $result = $this->sut->handleCommand($command);

        $this->assertSame(['Snapshot created', 'Application 1 refused.'], $result->getMessages());
    }
}
