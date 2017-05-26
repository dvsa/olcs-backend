<?php

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Application;

use Dvsa\Olcs\Api\Domain\Command as DomainCmd;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\Application\CreateGoodsVehicle;
use Dvsa\Olcs\Api\Domain\Exception\ValidationException;
use Dvsa\Olcs\Api\Domain\Repository;
use Dvsa\Olcs\Api\Entity;
use Dvsa\Olcs\Api\Entity\Application\Application as ApplicationEntity;
use Dvsa\Olcs\Transfer\Command\Application\CreateGoodsVehicle as Cmd;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Mockery as m;

/**
 * @covers \Dvsa\Olcs\Api\Domain\CommandHandler\Application\CreateGoodsVehicle
 */
class CreateGoodsVehicleTest extends CommandHandlerTestCase
{
    const APP_ID = 9001;
    const LIC_ID = 8001;
    const LIC_VEHICLE_ID = 7001;

    /** @var  CreateGoodsVehicle */
    protected $sut;

    public function setUp()
    {
        $this->sut = new CreateGoodsVehicle();

        $this->mockRepo('Application', Repository\Application::class);

        parent::setUp();
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
            'id' => self::APP_ID,
            'vrm' => 'ABC123',
            'platedWeight' => 100,
            'receivedDate' => '2015-01-01',
            'confirm' => 1,
        ];
        $command = Cmd::create($data);

        /** @var  Entity\Licence\Licence $licence */
        $licence = m::mock(Entity\Licence\Licence::class)->makePartial();
        $licence->setId(self::LIC_ID);

        /** @var ApplicationEntity | m\MockInterface $mockApp */
        $mockApp = m::mock(Entity\Application\Application::class)->makePartial();
        $mockApp->setId(self::APP_ID);
        $mockApp->setLicence($licence);

        $mockApp->shouldReceive('getRemainingSpaces')->andReturn(1);

        $this->repoMap['Application']
            ->shouldReceive('fetchUsingId')->with($command)->andReturn($mockApp);

        //  check vehicle create
        $data = [
            'vrm' => 'ABC123',
            'platedWeight' => 100,
            'specifiedDate' => null,
            'receivedDate' => '2015-01-01',
            'confirm' => 1,
            'licence' => self::LIC_ID,
            'applicationId' => self::APP_ID,
        ];
        $result1 = (new Result())
            ->addId('licenceVehicle', self::LIC_VEHICLE_ID)
            ->addMessage('Goods Vehicle Created');
        $this->expectedSideEffect(DomainCmd\Vehicle\CreateGoodsVehicle::class, $data, $result1);

        $data = [
            'id' => self::APP_ID,
            'section' => 'vehicles'
        ];

        //  check application completion
        $result2 = (new Result())
            ->addMessage('Section Updated');
        $this->expectedSideEffect(DomainCmd\Application\UpdateApplicationCompletion::class, $data, $result2);

        $result = $this->sut->handleCommand($command);

        $this->assertInstanceOf(Result::class, $result);

        $expected = [
            'id' => [
                'licenceVehicle' => self::LIC_VEHICLE_ID
            ],
            'messages' => [
                'Goods Vehicle Created',
                'Section Updated'
            ]
        ];

        $this->assertEquals($expected, $result->toArray());
    }
}
