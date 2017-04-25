<?php

/**
 * Create Goods Vehicle Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Application;

use Dvsa\Olcs\Api\Domain\Command\Application\UpdateApplicationCompletion;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\Exception\ValidationException;
use Mockery as m;
use Dvsa\Olcs\Api\Domain\CommandHandler\Application\CreateGoodsVehicle;
use Dvsa\Olcs\Api\Entity\Application\Application as ApplicationEntity;
use Dvsa\Olcs\Api\Entity\Licence\Licence as LicenceEntity;
use Dvsa\Olcs\Api\Entity\Licence\LicenceVehicle as LicenceVehicleEntity;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Api\Domain\Repository\Application as ApplicationRepo;
use Dvsa\Olcs\Api\Domain\Repository\LicenceVehicle as LicenceVehicleRepo;
use Dvsa\Olcs\Transfer\Command\Application\CreateGoodsVehicle as Cmd;
use Dvsa\Olcs\Api\Domain\Command\Vehicle\CreateGoodsVehicle as VehicleCmd;

/**
 * Create Goods Vehicle Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class CreateGoodsVehicleTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new CreateGoodsVehicle();
        $this->mockRepo('Application', ApplicationRepo::class);
        $this->mockRepo('LicenceVehicle', LicenceVehicleRepo::class);

        parent::setUp();
    }

    protected function initReferences()
    {
        $this->refData = [];
        $this->references = [];

        parent::initReferences();
    }

    public function testHandleCommandWithoutSpaces()
    {
        $this->setExpectedException(ValidationException::class);

        $data = [];
        $command = Cmd::create($data);

        /** @var ApplicationEntity $application */
        $application = m::mock(ApplicationEntity::class);

        $application->shouldReceive('getRemainingSpaces')
            ->andReturn(0);

        $this->repoMap['Application']->shouldReceive('fetchUsingId')
            ->with($command)
            ->andReturn($application);

        $this->sut->handleCommand($command);
    }

    public function testHandleCommand()
    {
        $data = [
            'id' => 111,
            'vrm' => 'ABC123',
            'platedWeight' => 100,
            'receivedDate' => '2015-01-01',
            'confirm' => 1
        ];
        $command = Cmd::create($data);

        /** @var LicenceVehicleEntity $licenceVehicle */
        $licenceVehicle = m::mock(LicenceVehicleEntity::class)->makePartial();

        /** @var LicenceEntity $licence */
        $licence = m::mock(LicenceEntity::class)->makePartial();
        $licence->setId(222);

        /** @var ApplicationEntity $application */
        $application = m::mock(ApplicationEntity::class)->makePartial();
        $application->setId(111);
        $application->setLicence($licence);

        $application->shouldReceive('getRemainingSpaces')
            ->andReturn(1);

        $this->repoMap['Application']->shouldReceive('fetchUsingId')
            ->with($command)
            ->andReturn($application);

        $data = [
            'licence' => 222,
            'vrm' => 'ABC123',
            'platedWeight' => 100,
            'specifiedDate' => null,
            'receivedDate' => '2015-01-01',
            'confirm' => 1,
            'applicationId' => 111
        ];
        $result1 = new Result();
        $result1->addId('licenceVehicle', 123);
        $result1->addMessage('Goods Vehicle Created');
        $this->expectedSideEffect(VehicleCmd::class, $data, $result1);

        $this->repoMap['LicenceVehicle']->shouldReceive('fetchById')
            ->with(123)
            ->andReturn($licenceVehicle)
            ->shouldReceive('save')
            ->with($licenceVehicle);

        $data = [
            'id' => 111,
            'section' => 'vehicles'
        ];
        $result2 = new Result();
        $result2->addMessage('Section Updated');
        $this->expectedSideEffect(UpdateApplicationCompletion::class, $data, $result2);

        $result = $this->sut->handleCommand($command);

        $this->assertSame($application, $licenceVehicle->getApplication());
        $this->assertInstanceOf(Result::class, $result);

        $expected = [
            'id' => [
                'licenceVehicle' => 123
            ],
            'messages' => [
                'Goods Vehicle Created',
                'Section Updated'
            ]
        ];

        $this->assertEquals($expected, $result->toArray());
    }
}
