<?php

/**
 * RevokeTest
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Licence;

use Doctrine\Common\Collections\ArrayCollection;
use Dvsa\Olcs\Api\Domain\Command\Licence\ReturnAllCommunityLicences;
use Dvsa\Olcs\Api\Entity\CommunityLic\CommunityLic;
use Mockery as m;
use Dvsa\Olcs\Api\Domain\Repository\Licence;
use Dvsa\Olcs\Api\Domain\CommandHandler\Licence\Surrender as CommandHandler;
use Dvsa\Olcs\Transfer\Command\Licence\SurrenderLicence as Command;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Api\Entity\Licence\Licence as LicenceEntity;
use Dvsa\Olcs\Api\Entity\Application\Application;
use Dvsa\Olcs\Api\Entity\System\RefData;
use Dvsa\Olcs\Api\Domain\Command\Discs\CeasePsvDiscs;
use Dvsa\Olcs\Api\Domain\Command\Discs\CeaseGoodsDiscs;
use Dvsa\Olcs\Api\Domain\Command\LicenceVehicle\RemoveLicenceVehicle;
use Dvsa\Olcs\Api\Domain\Command\Tm\DeleteTransportManagerLicence;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\Command\Variation\EndInterim;

/**
 * RevokeTest
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 * @author Josh Curtis <josh.curtis@valtech.co.uk>
 */
class SurrenderTest extends CommandHandlerTestCase
{
    public function setUp(): void
    {
        $this->sut = new CommandHandler();
        $this->mockRepo('Licence', Licence::class);

        parent::setUp();
    }

    protected function initReferences()
    {
        $this->refData = ['lsts_terminated', 'lsts_surrendered', 'lcat_psv', 'lcat_gv'];

        $this->references = [

        ];

        parent::initReferences();
    }

    /**
     * @dataProvider testHandleCommandProvider
     */
    public function testHandleCommand($status, $terminated)
    {
        $command = Command::create(['id' => 532, 'terminated' => $terminated]);

        $licence = new LicenceEntity(
            m::mock(\Dvsa\Olcs\Api\Entity\Organisation\Organisation::class),
            m::mock(\Dvsa\Olcs\Api\Entity\System\RefData::class)
        );
        $licence->setId(532);
        $licence->setGoodsOrPsv($this->refData[LicenceEntity::LICENCE_CATEGORY_PSV]);
        $licence->setCommunityLics(
            new ArrayCollection(
                [
                    new CommunityLic(),
                    new CommunityLic()
                ]
            )
        );
        $licence->setPsvDiscs(
            new ArrayCollection(['disc1', 'disc2'])
        );

        $this->repoMap['Licence']->shouldReceive('fetchUsingId')->with($command)->once()->andReturn($licence);
        $this->repoMap['Licence']->shouldReceive('save')->once()->andReturnUsing(
            function (LicenceEntity $saveLicence) use ($status) {
                $this->assertSame($this->refData[$status]->getId(), $saveLicence->getStatus()->getId());
                $this->assertInstanceOf(\DateTime::class, $saveLicence->getSurrenderedDate());
                $this->assertSame(
                    (new \DateTime())->format('Y-m-d'),
                    $saveLicence->getSurrenderedDate()->format('Y-m-d')
                );
            }
        );

        $ceaseDiscsResult = new Result();
        $this->expectedSideEffect(
            CeasePsvDiscs::class,
            array('licence' => 532),
            $ceaseDiscsResult
        );

        $removeVehicleResult = new Result();
        $this->expectedSideEffect(
            RemoveLicenceVehicle::class,
            array('licence' => 532, 'id' => null),
            $removeVehicleResult
        );

        $removeTmResult = new Result();
        $this->expectedSideEffect(
            DeleteTransportManagerLicence::class,
            array('licence' => $licence, 'id' => null),
            $removeTmResult
        );

        $this->expectedSideEffect(
            ReturnAllCommunityLicences::class,
            [
                'id' => 532
            ],
            new Result()
        );
        $this->expectedSideEffect(
            \Dvsa\Olcs\Api\Domain\Command\Publication\Licence::class,
            ['id' => 532],
            new Result()
        );

        $this->expectedSideEffect(
            EndInterim::class,
            ['licenceId' => 532],
            new Result()
        );

        $result = $this->sut->handleCommand($command);

        $this->assertSame(["Licence ID 532 surrendered"], $result->getMessages());
    }

    public function testHandleCommandGoods()
    {
        $status = 'lsts_surrendered';
        $command = Command::create(['id' => 532, 'terminated' => false]);

        $licence = new LicenceEntity(
            m::mock(\Dvsa\Olcs\Api\Entity\Organisation\Organisation::class),
            m::mock(\Dvsa\Olcs\Api\Entity\System\RefData::class)
        );
        $licence->setId(532);
        $licence->setGoodsOrPsv($this->refData[LicenceEntity::LICENCE_CATEGORY_GOODS_VEHICLE]);
        $licence->setCommunityLics(
            new ArrayCollection(
                [
                    new CommunityLic(),
                    new CommunityLic()
                ]
            )
        );
        $licence->setLicenceVehicles(
            new ArrayCollection(['lv1', 'lv2'])
        );

        $this->repoMap['Licence']->shouldReceive('fetchUsingId')->with($command)->once()->andReturn($licence);
        $this->repoMap['Licence']->shouldReceive('save')->once()->andReturnUsing(
            function (LicenceEntity $saveLicence) use ($status) {
                $this->assertSame($this->refData[$status]->getId(), $saveLicence->getStatus()->getId());
                $this->assertInstanceOf(\DateTime::class, $saveLicence->getSurrenderedDate());
                $this->assertSame(
                    (new \DateTime())->format('Y-m-d'),
                    $saveLicence->getSurrenderedDate()->format('Y-m-d')
                );
            }
        );

        $ceaseDiscsResult = new Result();
        $this->expectedSideEffect(
            CeaseGoodsDiscs::class,
            array('licence' => 532),
            $ceaseDiscsResult
        );

        $removeVehicleResult = new Result();
        $this->expectedSideEffect(
            RemoveLicenceVehicle::class,
            array('licence' => 532, 'id' => null),
            $removeVehicleResult
        );

        $removeTmResult = new Result();
        $this->expectedSideEffect(
            DeleteTransportManagerLicence::class,
            array('licence' => $licence, 'id' => null),
            $removeTmResult
        );

        $this->expectedSideEffect(
            ReturnAllCommunityLicences::class,
            [
                'id' => 532
            ],
            new Result()
        );
        $this->expectedSideEffect(
            \Dvsa\Olcs\Api\Domain\Command\Publication\Licence::class,
            ['id' => 532],
            new Result()
        );

        $this->expectedSideEffect(
            EndInterim::class,
            ['licenceId' => 532],
            new Result()
        );

        $result = $this->sut->handleCommand($command);

        $this->assertSame(["Licence ID 532 surrendered"], $result->getMessages());
    }

    public function testHandleCommandPsvSpecialRestricted()
    {
        $command = Command::create(['id' => 532, 'terminated' => true]);

        $licence = new LicenceEntity(
            m::mock(\Dvsa\Olcs\Api\Entity\Organisation\Organisation::class),
            m::mock(\Dvsa\Olcs\Api\Entity\System\RefData::class)
        );
        $licence->setId(532);
        $licence->setGoodsOrPsv($this->refData[LicenceEntity::LICENCE_CATEGORY_PSV]);
        $licence->setLicenceType(new RefData(LicenceEntity::LICENCE_TYPE_SPECIAL_RESTRICTED));
        $licence->setCommunityLics(
            new ArrayCollection(
                [
                    new CommunityLic(),
                    new CommunityLic()
                ]
            )
        );
        $licence->setPsvDiscs(
            new ArrayCollection(['disc1', 'disc2'])
        );

        $this->repoMap['Licence']->shouldReceive('fetchUsingId')->with($command)->once()->andReturn($licence);
        $status = LicenceEntity::LICENCE_STATUS_TERMINATED;
        $this->repoMap['Licence']->shouldReceive('save')->once()->andReturnUsing(
            function (LicenceEntity $saveLicence) use ($status) {
                $this->assertSame($this->refData[$status]->getId(), $saveLicence->getStatus()->getId());
                $this->assertInstanceOf(\DateTime::class, $saveLicence->getSurrenderedDate());
                $this->assertSame(
                    (new \DateTime())->format('Y-m-d'),
                    $saveLicence->getSurrenderedDate()->format('Y-m-d')
                );
            }
        );

        $ceaseDiscsResult = new Result();
        $this->expectedSideEffect(
            CeasePsvDiscs::class,
            array('licence' => 532),
            $ceaseDiscsResult
        );

        $removeVehicleResult = new Result();
        $this->expectedSideEffect(
            RemoveLicenceVehicle::class,
            array('licence' => 532, 'id' => null),
            $removeVehicleResult
        );

        $removeTmResult = new Result();
        $this->expectedSideEffect(
            DeleteTransportManagerLicence::class,
            array('licence' => $licence, 'id' => null),
            $removeTmResult
        );

        $this->expectedSideEffect(
            ReturnAllCommunityLicences::class,
            [
                'id' => 532
            ],
            new Result()
        );
        $this->expectedSideEffect(
            EndInterim::class,
            ['licenceId' => 532],
            new Result()
        );

        $result = $this->sut->handleCommand($command);

        $this->assertSame(["Licence ID 532 surrendered"], $result->getMessages());
    }

    public function testHandleCommandProvider()
    {
        return [
            [
                'lsts_terminated',
                true,
            ],
            [
                'lsts_surrendered',
                false
            ]
        ];
    }

    public function testHandleCommandApplications()
    {
        $command = Command::create(['id' => 532]);

        $licence = new LicenceEntity(
            m::mock(\Dvsa\Olcs\Api\Entity\Organisation\Organisation::class),
            m::mock(\Dvsa\Olcs\Api\Entity\System\RefData::class)
        );
        $licence->setId(532);
        $licence->setGoodsOrPsv($this->refData[LicenceEntity::LICENCE_CATEGORY_PSV]);
        $licence->setCommunityLics(
            new ArrayCollection(
                [
                    new CommunityLic(),
                    new CommunityLic()
                ]
            )
        );
        $licence->setPsvDiscs(new ArrayCollection(['disc1', 'disc2']));

        $this->stubApplication($licence, 174, Application::APPLICATION_STATUS_CURTAILED, true);
        $this->stubApplication($licence, 224, Application::APPLICATION_STATUS_CURTAILED, false);
        $this->stubApplication($licence, 345, Application::APPLICATION_STATUS_NOT_SUBMITTED, true);
        $this->stubApplication($licence, 445, Application::APPLICATION_STATUS_NOT_SUBMITTED, false);
        $this->stubApplication($licence, 567, Application::APPLICATION_STATUS_UNDER_CONSIDERATION, true);
        $this->stubApplication($licence, 667, Application::APPLICATION_STATUS_UNDER_CONSIDERATION, false);
        $this->stubApplication($licence, 778, Application::APPLICATION_STATUS_GRANTED, true);
        $this->stubApplication($licence, 878, Application::APPLICATION_STATUS_VALID, true);

        $this->repoMap['Licence']->shouldReceive('fetchUsingId')->with($command)->once()->andReturn($licence);
        $this->repoMap['Licence']->shouldReceive('save')->once();

        $this->expectedSideEffect(
            CeasePsvDiscs::class,
            array('licence' => 532), new Result()
        );
        $this->expectedSideEffect(
            RemoveLicenceVehicle::class,
            array('licence' => 532, 'id' => null),
            new Result()
        );
        $this->expectedSideEffect(
            DeleteTransportManagerLicence::class,
            array('licence' => $licence, 'id' => null),
            new Result()
        );
        $this->expectedSideEffect(ReturnAllCommunityLicences::class, ['id' => 532], new Result());

        $this->expectedSideEffect(
            \Dvsa\Olcs\Api\Domain\Command\Application\DeleteApplication::class,
            ['id' => 345],
            (new Result())->addMessage('NOT_SUBMITTED')
        );
        $this->expectedSideEffect(
            \Dvsa\Olcs\Transfer\Command\Application\RefuseApplication::class,
            ['id' => 567],
            (new Result())->addMessage('UNDER_CONSIDERATION')
        );
        $this->expectedSideEffect(
            \Dvsa\Olcs\Api\Domain\Command\Publication\Licence::class,
            ['id' => 532],
            new Result()
        );
        $this->expectedSideEffect(
            EndInterim::class,
            ['licenceId' => 532],
            new Result()
        );

        $result = $this->sut->handleCommand($command);

        $this->assertSame(
            [
                'NOT_SUBMITTED',
                'UNDER_CONSIDERATION',
                'Licence ID 532 surrendered'
            ],
            $result->getMessages()
        );
    }

    /**
     *
     * @param Licence $licence
     * @param int     $id
     * @param string  $status
     * @param bool    $isVariation
     *
     * @return Application
     */
    private function stubApplication($licence, $id, $status, $isVariation = false)
    {
        $application = new Application($licence, (new RefData())->setId($status), $isVariation);
        $application->setId($id);
        $licence->addApplications($application);

        return $application;
    }
}
