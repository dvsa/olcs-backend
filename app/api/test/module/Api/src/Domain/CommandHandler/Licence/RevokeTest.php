<?php

/**
 * RevokeTest
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Licence;

use Doctrine\Common\Collections\ArrayCollection;
use Dvsa\Olcs\Api\Domain\Command\Discs\CeaseGoodsDiscs;
use Dvsa\Olcs\Api\Domain\Command\Licence\ReturnAllCommunityLicences;
use Dvsa\Olcs\Api\Entity\CommunityLic\CommunityLic;
use Mockery as m;
use Dvsa\Olcs\Api\Domain\Repository\Licence;
use Dvsa\Olcs\Api\Domain\CommandHandler\Licence\Revoke as CommandHandler;
use Dvsa\Olcs\Transfer\Command\Licence\RevokeLicence as Command;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Api\Entity\Licence\Licence as LicenceEntity;
use Dvsa\Olcs\Api\Entity\Application\Application;
use Dvsa\Olcs\Api\Entity\System\RefData;
use Dvsa\Olcs\Api\Domain\Command\Discs\CeasePsvDiscs;
use Dvsa\Olcs\Api\Domain\Command\LicenceVehicle\RemoveLicenceVehicle;
use Dvsa\Olcs\Api\Domain\Command\Tm\DeleteTransportManagerLicence;
use Dvsa\Olcs\Api\Domain\Command\LicenceStatusRule\RemoveLicenceStatusRulesForLicence;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\Command\Variation\EndInterim;

/**
 * RevokeTest
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class RevokeTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new CommandHandler();
        $this->mockRepo('Licence', Licence::class);

        parent::setUp();
    }

    protected function initReferences()
    {
        $this->refData = [
            LicenceEntity::LICENCE_STATUS_REVOKED,
            LicenceEntity::LICENCE_CATEGORY_PSV,
            LicenceEntity::LICENCE_CATEGORY_GOODS_VEHICLE,
            LicenceEntity::LICENCE_TYPE_SPECIAL_RESTRICTED,
        ];

        $this->references = [

        ];

        parent::initReferences();
    }

    public function testHandleCommandPsv()
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

        $this->repoMap['Licence']->shouldReceive('fetchUsingId')->with($command)->once()->andReturn($licence);
        $this->repoMap['Licence']->shouldReceive('save')->once()->andReturnUsing(
            function (LicenceEntity $saveLicence) {
                $this->assertSame($this->refData['lsts_revoked'], $saveLicence->getStatus());
                $this->assertInstanceOf(\DateTime::class, $saveLicence->getRevokedDate());
                $this->assertSame((new \DateTime())->format('Y-m-d'), $saveLicence->getRevokedDate()->format('Y-m-d'));
            }
        );

        $this->expectedSideEffect(
            EndInterim::class,
            ['licenceId' => 532],
            new Result()
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

        $removeRulesResult = new Result();
        $this->expectedSideEffect(
            RemoveLicenceStatusRulesForLicence::class,
            [
                'licence' => $licence
            ],
            $removeRulesResult
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

        $result = $this->sut->handleCommand($command);

        $this->assertSame(["Licence ID 532 revoked"], $result->getMessages());
    }

    public function testHandleCommandPsvSpecialRestricted()
    {
        $command = Command::create(['id' => 532]);

        $licence = new LicenceEntity(
            m::mock(\Dvsa\Olcs\Api\Entity\Organisation\Organisation::class),
            m::mock(\Dvsa\Olcs\Api\Entity\System\RefData::class)
        );
        $licence->setId(532);
        $licence->setGoodsOrPsv($this->refData[LicenceEntity::LICENCE_CATEGORY_PSV]);
        $licence->setLicenceType($this->refData[LicenceEntity::LICENCE_TYPE_SPECIAL_RESTRICTED]);
        $licence->setCommunityLics(
            new ArrayCollection(
                [
                    new CommunityLic(),
                    new CommunityLic()
                ]
            )
        );

        $this->repoMap['Licence']->shouldReceive('fetchUsingId')->with($command)->once()->andReturn($licence);
        $this->repoMap['Licence']->shouldReceive('save')->once()->andReturnUsing(
            function (LicenceEntity $saveLicence) {
                $this->assertSame($this->refData['lsts_revoked'], $saveLicence->getStatus());
                $this->assertInstanceOf(\DateTime::class, $saveLicence->getRevokedDate());
                $this->assertSame((new \DateTime())->format('Y-m-d'), $saveLicence->getRevokedDate()->format('Y-m-d'));
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

        $removeRulesResult = new Result();
        $this->expectedSideEffect(
            RemoveLicenceStatusRulesForLicence::class,
            [
                'licence' => $licence
            ],
            $removeRulesResult
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

        $this->assertSame(["Licence ID 532 revoked"], $result->getMessages());
    }

    public function testHandleCommandGoods()
    {
        $command = Command::create(['id' => 532]);

        $licence = new LicenceEntity(
            m::mock(\Dvsa\Olcs\Api\Entity\Organisation\Organisation::class),
            m::mock(\Dvsa\Olcs\Api\Entity\System\RefData::class)
        );
        $licence->setId(532);
        $licence->setGoodsOrPsv($this->refData[LicenceEntity::LICENCE_CATEGORY_GOODS_VEHICLE]);

        $this->repoMap['Licence']->shouldReceive('fetchUsingId')->with($command)->once()->andReturn($licence);
        $this->repoMap['Licence']->shouldReceive('save')->once()->andReturnUsing(
            function (LicenceEntity $saveLicence) {
                $this->assertSame($this->refData['lsts_revoked'], $saveLicence->getStatus());
                $this->assertInstanceOf(\DateTime::class, $saveLicence->getRevokedDate());
                $this->assertSame((new \DateTime())->format('Y-m-d'), $saveLicence->getRevokedDate()->format('Y-m-d'));
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

        $removeRulesResult = new Result();
        $this->expectedSideEffect(
            RemoveLicenceStatusRulesForLicence::class,
            [
                'licence' => $licence
            ],
            $removeRulesResult
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

        $this->assertSame(["Licence ID 532 revoked"], $result->getMessages());
    }

    public function testHandleCommandApplications()
    {
        $command = Command::create(['id' => 532]);

        $licence = new LicenceEntity(
            m::mock(\Dvsa\Olcs\Api\Entity\Organisation\Organisation::class),
            m::mock(\Dvsa\Olcs\Api\Entity\System\RefData::class)
        );
        $licence->setId(532);
        $licence->setGoodsOrPsv($this->refData[LicenceEntity::LICENCE_CATEGORY_GOODS_VEHICLE]);

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
            CeaseGoodsDiscs::class,
            array('licence' => 532),
            new Result()
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
        $this->expectedSideEffect(
            RemoveLicenceStatusRulesForLicence::class,
            ['licence' => $licence],
            new Result()
        );
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
                'Licence ID 532 revoked'
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
