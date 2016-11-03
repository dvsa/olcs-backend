<?php

/**
 * Grant Interim Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Application;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\Command\Application\CreateApplicationFee;
use Dvsa\Olcs\Api\Domain\Command\Fee\CancelFee;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\Application\UpdateInterim;
use Dvsa\Olcs\Api\Domain\Exception\ValidationException;
use Dvsa\Olcs\Api\Entity\Application\Application as ApplicationEntity;
use Dvsa\Olcs\Api\Entity\Application\ApplicationOperatingCentre;
use Dvsa\Olcs\Api\Entity\Fee\Fee;
use Dvsa\Olcs\Api\Entity\Fee\FeeType;
use Dvsa\Olcs\Api\Entity\Licence\LicenceVehicle;
use Dvsa\Olcs\Api\Entity\Vehicle\GoodsDisc;
use Dvsa\Olcs\Transfer\Command\Application\UpdateInterim as Cmd;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Mockery as m;
use Doctrine\Common\Collections\ArrayCollection;
use Dvsa\Olcs\Api\Domain\Util\DateTime\DateTime;

/**
 * Grant Interim Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class UpdateInterimTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new UpdateInterim();
        $this->mockRepo('Application', \Dvsa\Olcs\Api\Domain\Repository\Application::class);
        $this->mockRepo('GoodsDisc', \Dvsa\Olcs\Api\Domain\Repository\GoodsDisc::class);
        $this->mockRepo('Fee', \Dvsa\Olcs\Api\Domain\Repository\Fee::class);
        $this->mockRepo('LicenceVehicle', \Dvsa\Olcs\Api\Domain\Repository\LicenceVehicle::class);

        parent::setUp();
    }

    protected function initReferences()
    {
        $this->refData = [
            ApplicationEntity::INTERIM_STATUS_REQUESTED,
            ApplicationEntity::INTERIM_STATUS_REFUSED,
            ApplicationEntity::INTERIM_STATUS_INFORCE,
            ApplicationEntity::INTERIM_STATUS_GRANTED,
            Fee::STATUS_CANCELLED
        ];

        parent::initReferences();
    }

    public function testHandleCommandValidationError()
    {
        $this->setExpectedException(ValidationException::class);

        $data = [
            'id' => 111,
            'version' => 1,
            'requested' => 'Y'
        ];
        $command = Cmd::create($data);

        /** @var ApplicationEntity $application */
        $application = m::mock(ApplicationEntity::class)->makePartial();
        $application->setId(111);

        $this->repoMap['Application']->shouldReceive('fetchUsingId')
            ->with($command, Query::HYDRATE_OBJECT, 1)
            ->andReturn($application);

        $this->sut->handleCommand($command);
    }

    public function testHandleCommand()
    {
        $application = m::mock(ApplicationEntity::class)->makePartial();

        $data = [
            'id' => 111,
            'version' => 1,
            'requested' => 'Y',
            'reason' => 'Foo',
            'startDate' => '2015-01-01',
            'endDate' => '2015-01-01',
            'authVehicles' => 10,
            'authTrailers' => 12,
            'operatingCentres' => [11],
            'vehicles' => [22]
        ];
        $command = Cmd::create($data);

        /** @var ApplicationOperatingCentre $oc1 */
        $oc1 = m::mock(ApplicationOperatingCentre::class)->makePartial();
        $oc1->setIsInterim('Y');
        $oc1->setId(99);
        /** @var ApplicationOperatingCentre $oc2 */
        $oc2 = m::mock(ApplicationOperatingCentre::class)->makePartial();
        $oc2->setIsInterim('N');
        $oc2->setId(11);

        $ocs = [
            $oc1,
            $oc2
        ];

        /** @var LicenceVehicle $lv1 */
        $lv1 = m::mock(LicenceVehicle::class)->makePartial();
        $lv1->setInterimApplication($application);
        $lv1->setId(88);
        /** @var LicenceVehicle $lv2 */
        $lv2 = m::mock(LicenceVehicle::class)->makePartial();
        $lv2->setId(22);

        $lv3 = m::mock(LicenceVehicle::class)->makePartial();
        $lv3->setId(23);
        $lv3->setRemovalDate(new DateTime());

        $lvs = [
            $lv1,
            $lv2,
            $lv3
        ];

        /** @var ApplicationEntity $application */
        $application->setId(111);
        $application->setOperatingCentres($ocs);
        $application->setLicenceVehicles($lvs);
        $application->setIsVariation(false);

        $this->repoMap['Application']->shouldReceive('fetchUsingId')
            ->with($command, Query::HYDRATE_OBJECT, 1)
            ->andReturn($application)
            ->shouldReceive('save')
            ->with($application);

        $fees = [];

        $this->repoMap['Fee']->shouldReceive('fetchInterimFeesByApplicationId')
            ->with(111, true)
            ->andReturn($fees)
            ->shouldReceive('fetchFeeByTypeAndApplicationId')
            ->with(FeeType::FEE_TYPE_VAR, 111)
            ->andReturn([])
            ->once()
            ->getMock();

        $result1 = new Result();
        $result1->addMessage('CreateApplicationFee');
        $data = ['id' => 111, 'feeTypeFeeType' => FeeType::FEE_TYPE_GRANTINT, 'description' => null];
        $this->expectedSideEffect(CreateApplicationFee::class, $data, $result1);

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [],
            'messages' => [
                'Interim data updated',
                'CreateApplicationFee'
            ]
        ];

        $this->assertEquals($expected, $result->toArray());

        $this->assertEquals('N', $oc1->getIsInterim());
        $this->assertEquals('Y', $oc2->getIsInterim());
        $this->assertNull($lv1->getInterimApplication());
        $this->assertSame($application, $lv2->getInterimApplication());

        $this->assertEquals('Foo', $application->getInterimReason());
        $this->assertEquals('2015-01-01', $application->getInterimStart()->format('Y-m-d'));
        $this->assertEquals('2015-01-01', $application->getInterimEnd()->format('Y-m-d'));
        $this->assertEquals(10, $application->getInterimAuthVehicles());
        $this->assertEquals(12, $application->getInterimAuthTrailers());
    }

    public function testHandleCommandRequestedNo()
    {
        $application = m::mock(ApplicationEntity::class)->makePartial();

        $data = [
            'id' => 111,
            'version' => 1,
            'requested' => 'N',
            'reason' => 'Foo',
            'startDate' => '2015-01-01',
            'endDate' => '2015-01-01',
            'authVehicles' => 10,
            'authTrailers' => 12,
            'operatingCentres' => [11],
            'vehicles' => [22]
        ];
        $command = Cmd::create($data);

        /** @var ApplicationEntity $application */
        $application->setId(111);
        $application->setIsVariation(false);

        /** @var ApplicationOperatingCentre $oc1 */
        $oc1 = m::mock(ApplicationOperatingCentre::class)->makePartial();
        $oc1->setIsInterim('Y');
        $oc1->setId(99);
        /** @var ApplicationOperatingCentre $oc2 */
        $oc2 = m::mock(ApplicationOperatingCentre::class)->makePartial();
        $oc2->setIsInterim('N');
        $oc2->setId(11);

        $ocs = [
            $oc1,
            $oc2
        ];

        /** @var LicenceVehicle $lv1 */
        $lv1 = m::mock(LicenceVehicle::class)->makePartial();
        $lv1->setInterimApplication($application);
        $lv1->setId(88);
        /** @var LicenceVehicle $lv2 */
        $lv2 = m::mock(LicenceVehicle::class)->makePartial();
        $lv2->setId(22);

        $lvs = [
            $lv1,
            $lv2
        ];

        /** @var ApplicationEntity $application */
        $application->setId(111);
        $application->setOperatingCentres($ocs);
        $application->setLicenceVehicles($lvs);

        $this->repoMap['Application']
            ->shouldReceive('fetchUsingId')
            ->with($command, Query::HYDRATE_OBJECT, 1)
            ->andReturn($application)
            ->shouldReceive('save')
            ->with($application);

        /** @var Fee $fee */
        $fee = m::mock(Fee::class)
            ->shouldReceive('getId')
            ->andReturn(222)
            ->shouldReceive('isFullyOutstanding')
            ->andReturn(true)
            ->getMock();

        $fees = [$fee];

        $this->repoMap['Fee']
            ->shouldReceive('fetchInterimFeesByApplicationId')
            ->with(111, true)
            ->andReturn($fees);

        $this->expectedSideEffect(
            CancelFee::class,
            ['id' => 222],
            (new Result())->addMessage('fee 222 cancelled')
        );

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [],
            'messages' => [
                'Interim data reset',
                'fee 222 cancelled',
            ]
        ];

        $this->assertEquals($expected, $result->toArray());

        $this->assertEquals('N', $oc1->getIsInterim());
        $this->assertEquals('N', $oc2->getIsInterim());
        $this->assertNull($lv1->getInterimApplication());
        $this->assertNull($lv2->getInterimApplication());

        $this->assertNull($application->getInterimReason());
        $this->assertNull($application->getInterimStart());
        $this->assertNull($application->getInterimEnd());
        $this->assertNull($application->getInterimAuthVehicles());
        $this->assertNull($application->getInterimAuthTrailers());
    }

    public function testHandleCommandRefusedRevoked()
    {
        $data = [
            'id' => 111,
            'version' => 1,
            'status' => ApplicationEntity::INTERIM_STATUS_REQUESTED
        ];
        $command = Cmd::create($data);

        /** @var ApplicationEntity $application */
        $application = m::mock(ApplicationEntity::class)->makePartial();
        $application->setId(111);
        $application->setInterimStatus($this->refData[ApplicationEntity::INTERIM_STATUS_REFUSED]);

        $this->repoMap['Application']->shouldReceive('fetchUsingId')
            ->with($command, Query::HYDRATE_OBJECT, 1)
            ->andReturn($application)
            ->shouldReceive('save')
            ->with($application);

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [],
            'messages' => [
                'Interim status updated'
            ]
        ];

        $this->assertEquals($expected, $result->toArray());

        $this->assertSame(
            $this->refData[ApplicationEntity::INTERIM_STATUS_REQUESTED],
            $application->getInterimStatus()
        );
    }

    public function testHandleCommandRequestedYesInforce()
    {
        $application = m::mock(ApplicationEntity::class)->makePartial();

        $data = [
            'id' => 111,
            'version' => 1,
            'requested' => 'Y',
            'reason' => 'Foo',
            'startDate' => '2015-01-01',
            'endDate' => '2015-01-01',
            'authVehicles' => 10,
            'authTrailers' => 12,
            'operatingCentres' => [11],
            'vehicles' => [22],
            'status' => ApplicationEntity::INTERIM_STATUS_REQUESTED
        ];
        $command = Cmd::create($data);

        /** @var ApplicationEntity $application */
        $application->setId(111);

        /** @var ApplicationOperatingCentre $oc1 */
        $oc1 = m::mock(ApplicationOperatingCentre::class)->makePartial();
        $oc1->setIsInterim('Y');
        $oc1->setId(99);
        /** @var ApplicationOperatingCentre $oc2 */
        $oc2 = m::mock(ApplicationOperatingCentre::class)->makePartial();
        $oc2->setIsInterim('N');
        $oc2->setId(11);

        $ocs = [
            $oc1,
            $oc2
        ];

        /** @var GoodsDisc $gd */
        $gd = m::mock(GoodsDisc::class)->makePartial();
        $gd->setId(96);

        $gds = [
            $gd
        ];

        /** @var LicenceVehicle $lv1 */
        $lv1 = m::mock(LicenceVehicle::class)->makePartial();
        $lv1->setInterimApplication($application);
        $lv1->setId(88);
        $lv1->setGoodsDiscs($gds);

        /** @var LicenceVehicle $lv2 */
        $lv2 = m::mock(LicenceVehicle::class)->makePartial();
        $lv2->setId(22);

        $lvs = [
            $lv1,
            $lv2
        ];

        /** @var GoodsDisc $gd */
        $gdInterim = m::mock(GoodsDisc::class)->makePartial();
        $gdInterim->setId(69);
        $gdsInterim = new ArrayCollection();
        $gdsInterim->add($gdInterim);
        $lv3 = m::mock(LicenceVehicle::class)->makePartial();
        $lv3->setGoodsDiscs($gdsInterim);

        /** @var LicenceVehicle $lv4 */
        $lv4 = m::mock(LicenceVehicle::class)->makePartial();
        $lv4->setId(25);
        $lv4->setRemovalDate(new DateTime());

        $interimLicenceVehicles = new ArrayCollection();
        $interimLicenceVehicles->add($lv3);
        $interimLicenceVehicles->add($lv4);

        $application->setOperatingCentres($ocs);
        $application->setLicenceVehicles($lvs);
        $application->setInterimStatus($this->refData[ApplicationEntity::INTERIM_STATUS_INFORCE]);
        $application->setInterimLicenceVehicles($interimLicenceVehicles);

        $this->repoMap['Application']->shouldReceive('fetchUsingId')
            ->with($command, Query::HYDRATE_OBJECT, 1)
            ->andReturn($application)
            ->shouldReceive('save')
            ->with($application);

        $this->repoMap['GoodsDisc']->shouldReceive('save')
            ->andReturnUsing(
                function (GoodsDisc $gd1) use ($lv2) {
                    if ($gd1->getId() === 96) {
                        $this->assertEquals('Y', $gd1->getIsInterim());
                        $this->assertSame($lv2, $gd1->getLicenceVehicle());
                    }
                }
            );

        $this->repoMap['LicenceVehicle']->shouldReceive('save')->once();

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [],
            'messages' => [
                'Interim data updated'
            ]
        ];

        $this->assertEquals($expected, $result->toArray());

        $this->assertEquals('N', $oc1->getIsInterim());
        $this->assertEquals('Y', $oc2->getIsInterim());
        $this->assertNull($lv1->getInterimApplication());
        $this->assertSame($application, $lv2->getInterimApplication());

        $this->assertNull($lv1->getSpecifiedDate());
        $this->assertNotNull($gd->getCeasedDate());

        $this->assertNotNull($lv2->getSpecifiedDate());
    }

    public function testHandleCommandGranted()
    {
        $application = m::mock(ApplicationEntity::class)->makePartial();

        $data = [
            'id' => 111,
            'version' => 1,
            'requested' => 'N',
            'reason' => 'Foo',
            'startDate' => '2015-01-01',
            'endDate' => '2015-01-01',
            'authVehicles' => 10,
            'authTrailers' => 12,
            'operatingCentres' => [11],
            'vehicles' => [22]
        ];
        $command = Cmd::create($data);

        /** @var ApplicationEntity $application */
        $application->setId(111);

        /** @var ApplicationOperatingCentre $oc1 */
        $oc1 = m::mock(ApplicationOperatingCentre::class)->makePartial();
        $oc1->setIsInterim('Y');
        $oc1->setId(99);
        /** @var ApplicationOperatingCentre $oc2 */
        $oc2 = m::mock(ApplicationOperatingCentre::class)->makePartial();
        $oc2->setIsInterim('N');
        $oc2->setId(11);

        $ocs = [
            $oc1,
            $oc2
        ];

        /** @var LicenceVehicle $lv1 */
        $lv1 = m::mock(LicenceVehicle::class)->makePartial();
        $lv1->setInterimApplication($application);
        $lv1->setId(88);
        /** @var LicenceVehicle $lv2 */
        $lv2 = m::mock(LicenceVehicle::class)->makePartial();
        $lv2->setId(22);

        $lvs = [
            $lv1,
            $lv2
        ];

        /** @var ApplicationEntity $application */
        $application->setId(111);
        $application->setOperatingCentres($ocs);
        $application->setLicenceVehicles($lvs);
        $application->setInterimStatus($this->refData[ApplicationEntity::INTERIM_STATUS_GRANTED]);

        $this->repoMap['Application']
            ->shouldReceive('fetchUsingId')
            ->with($command, Query::HYDRATE_OBJECT, 1)
            ->andReturn($application)
            ->shouldReceive('save')
            ->with($application);

        /** @var Fee $fee */
        $fee = m::mock(Fee::class)
            ->shouldReceive('getId')
            ->andReturn(222)
            ->shouldReceive('isFullyOutstanding')
            ->andReturn(true)
            ->getMock();

        $fees = [$fee];

        $this->repoMap['Fee']
            ->shouldReceive('fetchInterimFeesByApplicationId')
            ->with(111, true)
            ->andReturn($fees);

        $this->expectedSideEffect(
            CancelFee::class,
            ['id' => 222],
            (new Result())->addMessage('fee 222 cancelled')
        );

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [],
            'messages' => [
                'Interim data reset',
                'fee 222 cancelled',
            ]
        ];

        $this->assertEquals($expected, $result->toArray());

        $this->assertEquals('N', $oc1->getIsInterim());
        $this->assertEquals('N', $oc2->getIsInterim());
        $this->assertNull($lv1->getInterimApplication());
        $this->assertNull($lv2->getInterimApplication());

        $this->assertNull($application->getInterimReason());
        $this->assertNull($application->getInterimStart());
        $this->assertNull($application->getInterimEnd());
        $this->assertNull($application->getInterimAuthVehicles());
        $this->assertNull($application->getInterimAuthTrailers());
    }

    /**
     * @dataProvider statusProvider
     */
    public function testHandleCommandRequestedYesInforceNoInterimVehicles($status)
    {
        $application = m::mock(ApplicationEntity::class)->makePartial();

        $data = [
            'id' => 111,
            'version' => 1,
            'requested' => 'Y',
            'reason' => 'Foo',
            'startDate' => '2015-01-01',
            'endDate' => '2015-01-01',
            'authVehicles' => 10,
            'authTrailers' => 12,
            'operatingCentres' => [11],
            'vehicles' => [22],
            'status' => $status
        ];
        $command = Cmd::create($data);

        /** @var ApplicationEntity $application */
        $application->setId(111);

        /** @var ApplicationOperatingCentre $oc1 */
        $oc1 = m::mock(ApplicationOperatingCentre::class)->makePartial();
        $oc1->setIsInterim('Y');
        $oc1->setId(99);
        /** @var ApplicationOperatingCentre $oc2 */
        $oc2 = m::mock(ApplicationOperatingCentre::class)->makePartial();
        $oc2->setIsInterim('N');
        $oc2->setId(11);

        $ocs = [
            $oc1,
            $oc2
        ];

        /** @var GoodsDisc $gd */
        $gd = m::mock(GoodsDisc::class)->makePartial();
        $gd->setId(96);

        $gds = [
            $gd
        ];

        /** @var LicenceVehicle $lv1 */
        $lv1 = m::mock(LicenceVehicle::class)->makePartial();
        $lv1->setInterimApplication($application);
        $lv1->setId(88);
        $lv1->setGoodsDiscs($gds);
        /** @var LicenceVehicle $lv2 */
        $lv2 = m::mock(LicenceVehicle::class)->makePartial();
        $lv2->setId(22);

        $lvs = [
            $lv1,
            $lv2
        ];

        $application->setOperatingCentres($ocs);
        $application->setLicenceVehicles($lvs);
        $application->setInterimStatus($this->refData[ApplicationEntity::INTERIM_STATUS_INFORCE]);
        $application->setInterimLicenceVehicles([]);

        $this->repoMap['Application']->shouldReceive('fetchUsingId')
            ->with($command, Query::HYDRATE_OBJECT, 1)
            ->andReturn($application)
            ->shouldReceive('save')
            ->with($application);

        $this->repoMap['GoodsDisc']->shouldReceive('save')
            ->andReturnUsing(
                function (GoodsDisc $gd1) use ($lv2) {
                    $this->assertEquals('Y', $gd1->getIsInterim());
                    $this->assertSame($lv2, $gd1->getLicenceVehicle());
                }
            );

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [],
            'messages' => [
                'Interim data updated'
            ]
        ];

        $this->assertEquals($expected, $result->toArray());

        $this->assertEquals('N', $oc1->getIsInterim());
        $this->assertEquals('Y', $oc2->getIsInterim());
        $this->assertNull($lv1->getInterimApplication());
        $this->assertSame($application, $lv2->getInterimApplication());

        $this->assertNull($lv1->getSpecifiedDate());
        $this->assertNotNull($gd->getCeasedDate());

        $this->assertNotNull($lv2->getSpecifiedDate());
    }

    /**
     * Status provider
     *
     * @return array
     */
    public function statusProvider()
    {
        return [
            [ApplicationEntity::INTERIM_STATUS_REQUESTED],
            [ApplicationEntity::INTERIM_STATUS_INFORCE]
        ];
    }

    public function testHandleCommandRefusedToInforce()
    {
        $data = [
            'id' => 111,
            'version' => 1,
            'status' => ApplicationEntity::INTERIM_STATUS_INFORCE
        ];
        $command = Cmd::create($data);

        /** @var ApplicationEntity $application */
        $application = m::mock(ApplicationEntity::class)->makePartial();
        $application->setId(111);
        $application->setInterimStatus($this->refData[ApplicationEntity::INTERIM_STATUS_REFUSED]);

        $licenceVehicles = new ArrayCollection();

        $licenceVehicle1 = m::mock(\Dvsa\Olcs\Api\Entity\Licence\LicenceVehicle::class)
            ->shouldReceive('getRemovalDate')
            ->andReturn(new DateTime())
            ->once()
            ->getMock();

        $licenceVehicle2 = m::mock(\Dvsa\Olcs\Api\Entity\Licence\LicenceVehicle::class)
            ->shouldReceive('getRemovalDate')
            ->andReturnNull()
            ->once()
            ->shouldReceive('setSpecifiedDate')
            ->with(m::type(DateTime::class))
            ->once()
            ->getMock();

        $licenceVehicles->add($licenceVehicle1);
        $licenceVehicles->add($licenceVehicle2);

        $application->setInterimLicenceVehicles($licenceVehicles);

        $this->repoMap['LicenceVehicle']
            ->shouldReceive('save')
            ->with($licenceVehicle2)
            ->once()
            ->getMock();

        $this->repoMap['GoodsDisc']
            ->shouldReceive('save')
            ->with(m::type(GoodsDisc::class))
            ->once()
            ->getMock();

        $this->repoMap['Application']->shouldReceive('fetchUsingId')
            ->with($command, Query::HYDRATE_OBJECT, 1)
            ->andReturn($application)
            ->shouldReceive('save')
            ->with($application);

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [],
            'messages' => [
                'Interim status updated'
            ]
        ];

        $this->assertEquals($expected, $result->toArray());

        $this->assertSame(
            $this->refData[ApplicationEntity::INTERIM_STATUS_INFORCE],
            $application->getInterimStatus()
        );
    }

    public function testHandleCommandRefusedToInforceNoLicenceVehicles()
    {
        $data = [
            'id' => 111,
            'version' => 1,
            'status' => ApplicationEntity::INTERIM_STATUS_INFORCE
        ];
        $command = Cmd::create($data);

        /** @var ApplicationEntity $application */
        $application = m::mock(ApplicationEntity::class)->makePartial();
        $application->setId(111);
        $application->setInterimStatus($this->refData[ApplicationEntity::INTERIM_STATUS_REFUSED]);

        $application->setInterimLicenceVehicles(new ArrayCollection());

        $this->repoMap['Application']->shouldReceive('fetchUsingId')
            ->with($command, Query::HYDRATE_OBJECT, 1)
            ->andReturn($application)
            ->shouldReceive('save')
            ->with($application);

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [],
            'messages' => [
                'Interim status updated'
            ]
        ];

        $this->assertEquals($expected, $result->toArray());

        $this->assertSame(
            $this->refData[ApplicationEntity::INTERIM_STATUS_INFORCE],
            $application->getInterimStatus()
        );
    }
}
