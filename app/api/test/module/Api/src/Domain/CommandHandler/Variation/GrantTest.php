<?php

/**
 * Grant Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Variation;

use Doctrine\Common\Collections\ArrayCollection;
use Dvsa\Olcs\Api\Domain\Command\Application\Grant\CommonGrant;
use Dvsa\Olcs\Api\Domain\Command\Application\Grant\CreateDiscRecords;
use Dvsa\Olcs\Api\Domain\Command\Application\Grant\ProcessApplicationOperatingCentres;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\Variation\Grant;
use Dvsa\Olcs\Api\Entity\Application\Application as ApplicationEntity;
use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Dvsa\Olcs\Api\Entity\Licence\LicenceVehicle;
use Dvsa\Olcs\Api\Entity\Licence\PsvDisc;
use Dvsa\Olcs\Api\Entity\Vehicle\GoodsDisc;
use Dvsa\Olcs\Transfer\Command\Application\CreateSnapshot;
use Dvsa\Olcs\Transfer\Command\Licence\CreatePsvDiscs;
use Dvsa\Olcs\Transfer\Command\Licence\VoidPsvDiscs;
use Mockery as m;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Transfer\Command\Variation\Grant as Cmd;
use Dvsa\Olcs\Api\Domain\Command\Application\EndInterim as EndInterimCmd;

/**
 * Grant Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class GrantTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new Grant();
        $this->mockRepo('Application', \Dvsa\Olcs\Api\Domain\Repository\Application::class);
        $this->mockRepo('GoodsDisc', \Dvsa\Olcs\Api\Domain\Repository\GoodsDisc::class);

        parent::setUp();
    }

    protected function initReferences()
    {
        $this->refData = [
            ApplicationEntity::APPLICATION_STATUS_VALID,
            Licence::LICENCE_TYPE_STANDARD_NATIONAL,
            Licence::LICENCE_TYPE_STANDARD_INTERNATIONAL
        ];
        $this->references = [];

        parent::initReferences();
    }

    public function testHandleCommand()
    {
        $data = [
            'id' => 111
        ];

        $command = Cmd::create($data);

        /** @var Licence $licence */
        $licence = m::mock(Licence::class)->makePartial();
        $licence->setLicenceType($this->refData[Licence::LICENCE_TYPE_STANDARD_NATIONAL]);
        $licence->setTotAuthVehicles(10);

        /** @var ApplicationEntity $application */
        $application = m::mock(ApplicationEntity::class)->makePartial();
        $application->setLicenceType($this->refData[Licence::LICENCE_TYPE_STANDARD_NATIONAL]);
        $application->setId(111);
        $application->setLicence($licence);

        $application->shouldReceive('getCurrentInterimStatus')
            ->andReturn(ApplicationEntity::INTERIM_STATUS_INFORCE)
            ->once()
            ->shouldReceive('isGoods')
            ->andReturn(true)
            ->once()
            ->shouldReceive('isVariation')
            ->andReturn(true)
            ->once()
            ->getMock();
        $this->expectedSideEffect(EndInterimCmd::class, ['id' => 111], new Result());

        $licence->shouldReceive('copyInformationFromApplication')
            ->with($application);

        $this->repoMap['Application']->shouldReceive('fetchUsingId')
            ->with($command)
            ->andReturn($application)
            ->shouldReceive('save')
            ->once()
            ->with($application);

        $result1 = new Result();
        $result1->addMessage('CreateSnapshot');
        $this->expectedSideEffect(CreateSnapshot::class, ['id' => 111, 'event' => CreateSnapshot::ON_GRANT], $result1);

        $result2 = new Result();
        $result2->addMessage('CreateDiscRecords');
        $discData = $data;
        $discData['currentTotAuth'] = 10;
        $this->expectedSideEffect(CreateDiscRecords::class, $discData, $result2);

        $result3 = new Result();
        $result3->addMessage('ProcessApplicationOperatingCentres');
        $this->expectedSideEffect(ProcessApplicationOperatingCentres::class, $data, $result3);

        $result4 = new Result();
        $result4->addMessage('CommonGrant');
        $this->expectedSideEffect(CommonGrant::class, $data, $result4);

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [],
            'messages' => [
                'CreateSnapshot',
                'CreateDiscRecords',
                'ProcessApplicationOperatingCentres',
                'CommonGrant'
            ]
        ];

        $this->assertEquals($expected, $result->toArray());

        $this->assertEquals(ApplicationEntity::APPLICATION_STATUS_VALID, $application->getStatus()->getId());
    }

    public function testHandleCommandUpgradeGoods()
    {
        $data = [
            'id' => 111
        ];

        $command = Cmd::create($data);

        /** @var Licence $licence */
        $licence = m::mock(Licence::class)->makePartial();
        $licence->setLicenceType($this->refData[Licence::LICENCE_TYPE_STANDARD_NATIONAL]);
        $licence->setTotAuthVehicles(10);

        /** @var ApplicationEntity $application */
        $application = m::mock(ApplicationEntity::class)->makePartial();
        $application->setLicenceType($this->refData[Licence::LICENCE_TYPE_STANDARD_INTERNATIONAL]);
        $application->setId(111);
        $application->setLicence($licence);
        $application->shouldReceive('isGoods')
            ->andReturn(true);

        /** @var GoodsDisc $goodsDisc1 */
        $goodsDisc1 = m::mock(GoodsDisc::class)->makePartial();
        $goodsDisc1->setCeasedDate('2015-01-01');
        /** @var GoodsDisc $goodsDisc2 */
        $goodsDisc2 = m::mock(GoodsDisc::class)->makePartial();

        $goodsDiscs = [
            $goodsDisc1,
            $goodsDisc2
        ];

        /** @var LicenceVehicle $licenceVehicle */
        $licenceVehicle = m::mock(LicenceVehicle::class)->makePartial();
        $licenceVehicle->setGoodsDiscs($goodsDiscs);

        $licenceVehicles = new ArrayCollection();
        $licenceVehicles->add($licenceVehicle);

        $licence->shouldReceive('copyInformationFromApplication')
            ->with($application)
            ->shouldReceive('getLicenceVehicles->matching')
            ->andReturn($licenceVehicles);

        $this->repoMap['Application']->shouldReceive('fetchUsingId')
            ->with($command)
            ->andReturn($application)
            ->shouldReceive('save')
            ->once()
            ->with($application);

        $this->repoMap['GoodsDisc']->shouldReceive('save')
            ->andReturnUsing(
                function (GoodsDisc $goodsDisc) use ($licenceVehicle, $goodsDisc2) {
                    if ($goodsDisc !== $goodsDisc2) {
                        $this->assertSame($licenceVehicle, $goodsDisc->getLicenceVehicle());
                        $this->assertEquals('N', $goodsDisc->getIsCopy());
                    }
                }
            );

        $result1 = new Result();
        $result1->addMessage('CreateSnapshot');
        $this->expectedSideEffect(CreateSnapshot::class, ['id' => 111, 'event' => CreateSnapshot::ON_GRANT], $result1);

        $result2 = new Result();
        $result2->addMessage('CreateDiscRecords');
        $discData = $data;
        $discData['currentTotAuth'] = 10;
        $this->expectedSideEffect(CreateDiscRecords::class, $discData, $result2);

        $result3 = new Result();
        $result3->addMessage('ProcessApplicationOperatingCentres');
        $this->expectedSideEffect(ProcessApplicationOperatingCentres::class, $data, $result3);

        $result4 = new Result();
        $result4->addMessage('CommonGrant');
        $this->expectedSideEffect(CommonGrant::class, $data, $result4);

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [],
            'messages' => [
                'CreateSnapshot',
                '1 Goods Disc(s) replaced',
                'CreateDiscRecords',
                'ProcessApplicationOperatingCentres',
                'CommonGrant'
            ]
        ];

        $this->assertEquals($expected, $result->toArray());

        $this->assertEquals(ApplicationEntity::APPLICATION_STATUS_VALID, $application->getStatus()->getId());

        $this->assertEquals(date('Y-m-d'), $goodsDisc2->getCeasedDate()->format('Y-m-d'));
    }

    public function testHandleCommandUpgradePsv()
    {
        $data = [
            'id' => 111
        ];

        $command = Cmd::create($data);

        /** @var Licence $licence */
        $licence = m::mock(Licence::class)->makePartial();
        $licence->setId(222);
        $licence->setLicenceType($this->refData[Licence::LICENCE_TYPE_STANDARD_NATIONAL]);
        $licence->setTotAuthVehicles(10);

        /** @var ApplicationEntity $application */
        $application = m::mock(ApplicationEntity::class)->makePartial();
        $application->setLicenceType($this->refData[Licence::LICENCE_TYPE_STANDARD_INTERNATIONAL]);
        $application->setId(111);
        $application->setLicence($licence);
        $application->shouldReceive('isGoods')
            ->andReturn(false);

        /** @var PsvDisc $psvDisc */
        $psvDisc = m::mock(PsvDisc::class)->makePartial();
        $psvDisc->setId(123);

        $psvDiscs = [$psvDisc];

        $licence->shouldReceive('copyInformationFromApplication')
            ->with($application)
            ->shouldReceive('getPsvDiscs->matching')
            ->andReturn($psvDiscs);

        $this->repoMap['Application']->shouldReceive('fetchUsingId')
            ->with($command)
            ->andReturn($application)
            ->shouldReceive('save')
            ->once()
            ->with($application);

        $result1 = new Result();
        $result1->addMessage('CreateSnapshot');
        $this->expectedSideEffect(CreateSnapshot::class, ['id' => 111, 'event' => CreateSnapshot::ON_GRANT], $result1);

        $result2 = new Result();
        $result2->addMessage('CreateDiscRecords');
        $discData = $data;
        $discData['currentTotAuth'] = 10;
        $this->expectedSideEffect(CreateDiscRecords::class, $discData, $result2);

        $result3 = new Result();
        $result3->addMessage('ProcessApplicationOperatingCentres');
        $this->expectedSideEffect(ProcessApplicationOperatingCentres::class, $data, $result3);

        $result4 = new Result();
        $result4->addMessage('CommonGrant');
        $this->expectedSideEffect(CommonGrant::class, $data, $result4);

        $result5 = new Result();
        $result5->addMessage('VoidPsvDiscs');
        $this->expectedSideEffect(VoidPsvDiscs::class, ['licence' => 222, 'ids' => [123]], $result5);

        $result6 = new Result();
        $result6->addMessage('CreatePsvDiscs');
        $this->expectedSideEffect(CreatePsvDiscs::class, ['licence' => 222, 'amount' => 1, 'isCopy' => 'N'], $result6);

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [],
            'messages' => [
                'CreateSnapshot',
                'VoidPsvDiscs',
                'CreatePsvDiscs',
                'CreateDiscRecords',
                'ProcessApplicationOperatingCentres',
                'CommonGrant'
            ]
        ];

        $this->assertEquals($expected, $result->toArray());

        $this->assertEquals(ApplicationEntity::APPLICATION_STATUS_VALID, $application->getStatus()->getId());
    }
}
