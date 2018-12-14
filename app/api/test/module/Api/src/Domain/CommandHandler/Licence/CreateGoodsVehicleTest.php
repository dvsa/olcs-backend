<?php

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Licence;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\Exception\ValidationException;
use Dvsa\Olcs\Api\Domain\Repository\Licence as  LicenceRepo;
use Dvsa\Olcs\Api\Domain\CommandHandler\Licence\CreateGoodsVehicle;
use Dvsa\Olcs\Api\Entity\Licence\Licence as LicenceEntity;
use Dvsa\Olcs\Transfer\Command\Licence\CreateGoodsVehicle as Cmd;
use Mockery as m;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Api\Domain\Command\Vehicle\CreateGoodsVehicle as VehicleCmd;
use Dvsa\Olcs\Api\Domain\Command\Vehicle\CreateGoodsDiscs as CreateGoodsDiscsCmd;
use Dvsa\Olcs\Api\Domain\Util\DateTime\DateTime;

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
        $this->mockRepo('Licence', LicenceRepo::class);

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
        $this->expectException(ValidationException::class);

        $data = [
            'id' => 111
        ];
        $command = Cmd::create($data);

        /** @var LicenceEntity $licence */
        $licence = m::mock(LicenceEntity::class)->makePartial();
        $licence->shouldReceive('getRemainingSpaces')
            ->andReturn(0);

        $this->repoMap['Licence']->shouldReceive('fetchUsingId')
            ->with($command)
            ->andReturn($licence);

        $this->sut->handleCommand($command);
    }

    public function testHandleCommand()
    {
        $data = [
            'id' => 111,
            'vrm' => 'ABC123',
            'platedWeight' => 100,
            'specifiedDate' => null,
            'receivedDate' => null,
            'confirm' => 1
        ];
        $command = Cmd::create($data);

        /** @var LicenceEntity $licence */
        $licence = m::mock(LicenceEntity::class)->makePartial();
        $licence->shouldReceive('getRemainingSpaces')
            ->andReturn(1);

        $this->repoMap['Licence']->shouldReceive('fetchUsingId')
            ->with($command)
            ->andReturn($licence);

        $data = [
            'licence' => 111,
            'vrm' => 'ABC123',
            'platedWeight' => 100,
            'specifiedDate' => (new DateTime('now'))->format(\DateTime::ISO8601),
            'receivedDate' => null,
            'confirm' => 1
        ];
        $result1 = new Result();
        $result1->addId('licenceVehicle', 123);
        $result1->addMessage('Licence Vehicle Created');
        $this->expectedSideEffect(VehicleCmd::class, $data, $result1);

        $data = [
            'ids' => [123],
            'isCopy' => 'N'
        ];
        $result2 = new Result();
        $result2->addId('goodsDisc', 321);
        $result2->addMessage('Goods Disc Created');
        $this->expectedSideEffect(CreateGoodsDiscsCmd::class, $data, $result2);

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [
                'licenceVehicle' => 123,
                'goodsDisc' => 321
            ],
            'messages' => [
                'Licence Vehicle Created',
                'Goods Disc Created'
            ]
        ];

        $this->assertEquals($expected, $result->toArray());
    }
}
