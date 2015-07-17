<?php

/**
 * RevokeTest
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Licence;

use Doctrine\Common\Collections\ArrayCollection;
use Dvsa\Olcs\Api\Domain\Command\Discs\CeaseGoodsDiscs;
use Mockery as m;
use Dvsa\Olcs\Api\Domain\Repository\Licence;
use Dvsa\Olcs\Api\Domain\CommandHandler\Licence\Revoke as CommandHandler;
use Dvsa\Olcs\Transfer\Command\Licence\RevokeLicence as Command;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Api\Entity\Licence\Licence as LicenceEntity;
use Dvsa\Olcs\Api\Domain\Command\Discs\CeasePsvDiscs;
use Dvsa\Olcs\Api\Domain\Command\LicenceVehicle\RemoveLicenceVehicle;
use Dvsa\Olcs\Api\Domain\Command\Tm\DeleteTransportManagerLicence;
use Dvsa\Olcs\Api\Domain\Command\LicenceStatusRule\RemoveLicenceStatusRulesForLicence;
use Dvsa\Olcs\Api\Domain\Command\Result;

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
        $this->refData = ['lsts_revoked', 'lcat_psv', 'lcat_gv'];

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
            array('discs' => new ArrayCollection(), 'id' => null),
            $ceaseDiscsResult
        );

        $removeVehicleResult = new Result();
        $this->expectedSideEffect(
            RemoveLicenceVehicle::class,
            array('licenceVehicles' => new ArrayCollection(), 'id' => null),
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
            array('licenceVehicles' => new ArrayCollection(), 'id' => null),
            $ceaseDiscsResult
        );

        $removeVehicleResult = new Result();
        $this->expectedSideEffect(
            RemoveLicenceVehicle::class,
            array('licenceVehicles' => new ArrayCollection(), 'id' => null),
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

        $result = $this->sut->handleCommand($command);

        $this->assertSame(["Licence ID 532 revoked"], $result->getMessages());
    }
}
