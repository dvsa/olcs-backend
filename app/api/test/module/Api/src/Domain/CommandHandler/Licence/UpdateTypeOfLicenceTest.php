<?php

/**
 * Update Type of Licence Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Licence;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\CommandHandler\Licence\UpdateTypeOfLicence;
use Dvsa\Olcs\Api\Domain\Exception\ForbiddenException;
use Dvsa\Olcs\Api\Domain\Exception\RequiresVariationException;
use Dvsa\Olcs\Api\Domain\Exception\ValidationException;
use Dvsa\Olcs\Api\Domain\Util\DateTime\DateTime;
use Dvsa\Olcs\Api\Entity\ContactDetails\ContactDetails;
use Dvsa\Olcs\Api\Entity\Licence\Licence as Licence;
use Dvsa\Olcs\Api\Entity\Licence\LicenceVehicle;
use Dvsa\Olcs\Api\Entity\Licence\PsvDisc;
use Dvsa\Olcs\Api\Entity\Tm\TransportManager;
use Dvsa\Olcs\Api\Entity\Tm\TransportManagerLicence;
use Dvsa\Olcs\Api\Entity\User\Permission;
use Dvsa\Olcs\Api\Entity\Vehicle\Vehicle;
use Dvsa\Olcs\Transfer\Command\Licence\UpdateTypeOfLicence as Cmd;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Mockery as m;
use ZfcRbac\Service\AuthorizationService;

/**
 * Update Type of Licence Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class UpdateTypeOfLicenceTest extends CommandHandlerTestCase
{
    public function setUp(): void
    {
        $this->sut = new UpdateTypeOfLicence();
        $this->mockRepo('Licence', \Dvsa\Olcs\Api\Domain\Repository\Licence::class);
        $this->mockRepo('TransportManagerLicence', \Dvsa\Olcs\Api\Domain\Repository\TransportManagerLicence::class);
        $this->mockRepo('ContactDetails', \Dvsa\Olcs\Api\Domain\Repository\ContactDetails::class);
        $this->mockRepo('GoodsDisc', \Dvsa\Olcs\Api\Domain\Repository\GoodsDisc::class);
        $this->mockRepo('PsvDisc', \Dvsa\Olcs\Api\Domain\Repository\PsvDisc::class);

        $this->mockedSmServices[AuthorizationService::class] = m::mock(AuthorizationService::class);

        parent::setUp();
    }

    protected function initReferences()
    {
        $this->refData = [
            Licence::LICENCE_TYPE_STANDARD_NATIONAL,
            Licence::LICENCE_TYPE_STANDARD_INTERNATIONAL,
            Licence::LICENCE_TYPE_SPECIAL_RESTRICTED,
            Licence::LICENCE_TYPE_RESTRICTED,
            Licence::LICENCE_CATEGORY_GOODS_VEHICLE,
            Licence::LICENCE_CATEGORY_PSV
        ];

        $this->references = [
            ContactDetails::class => [
                12 => m::mock(ContactDetails::class),
            ]
        ];

        parent::initReferences();
    }

    public function testHandleCommandWithoutChange()
    {
        $data = [
            'licenceType' => Licence::LICENCE_TYPE_STANDARD_NATIONAL,
            'version' => 1
        ];
        $command = Cmd::create($data);

        /** @var Licence $licence */
        $licence = m::mock(Licence::class)->makePartial();
        $licence->setLicenceType($this->refData[Licence::LICENCE_TYPE_STANDARD_NATIONAL]);

        $this->repoMap['Licence']->shouldReceive('fetchUsingId')
            ->with($command, Query::HYDRATE_OBJECT, 1)
            ->andReturn($licence);

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [],
            'messages' => [
                'No updates required'
            ]
        ];

        $this->assertEquals($expected, $result->toArray());
    }

    public function testHandleCommandWithChangeWhenNotAllowed()
    {
        $this->expectException(ForbiddenException::class);

        $data = [
            'licenceType' => Licence::LICENCE_TYPE_STANDARD_INTERNATIONAL,
            'version' => 1
        ];
        $command = Cmd::create($data);

        /** @var Licence $licence */
        $licence = m::mock(Licence::class)->makePartial();
        $licence->setLicenceType($this->refData[Licence::LICENCE_TYPE_STANDARD_NATIONAL]);

        $this->mockedSmServices[AuthorizationService::class]->shouldReceive('isGranted')
            ->with(Permission::CAN_UPDATE_LICENCE_LICENCE_TYPE, $licence)
            ->andReturn(false);

        $this->repoMap['Licence']->shouldReceive('fetchUsingId')
            ->with($command, Query::HYDRATE_OBJECT, 1)
            ->andReturn($licence);

        $this->sut->handleCommand($command);
    }

    public function testHandleCommandWithSrChangeWithoutPermission()
    {
        $this->expectException(ValidationException::class);

        $data = [
            'licenceType' => Licence::LICENCE_TYPE_SPECIAL_RESTRICTED,
            'version' => 1
        ];
        $command = Cmd::create($data);

        /** @var Licence $licence */
        $licence = m::mock(Licence::class)->makePartial();
        $licence->setLicenceType($this->refData[Licence::LICENCE_TYPE_STANDARD_NATIONAL]);
        $licence->setGoodsOrPsv($this->refData[Licence::LICENCE_CATEGORY_GOODS_VEHICLE]);

        $this->mockedSmServices[AuthorizationService::class]->shouldReceive('isGranted')
            ->with(Permission::CAN_UPDATE_LICENCE_LICENCE_TYPE, $licence)
            ->andReturn(true);

        $this->repoMap['Licence']->shouldReceive('fetchUsingId')
            ->with($command, Query::HYDRATE_OBJECT, 1)
            ->andReturn($licence);

        $this->sut->handleCommand($command);
    }

    public function testHandleCommandWithChangeSelfserve()
    {
        $this->expectException(RequiresVariationException::class);

        $data = [
            'licenceType' => Licence::LICENCE_TYPE_STANDARD_NATIONAL,
            'version' => 1
        ];
        $command = Cmd::create($data);

        /** @var Licence $licence */
        $licence = m::mock(Licence::class)->makePartial();
        $licence->setLicenceType($this->refData[Licence::LICENCE_TYPE_STANDARD_INTERNATIONAL]);
        $licence->setGoodsOrPsv($this->refData[Licence::LICENCE_CATEGORY_GOODS_VEHICLE]);

        $this->mockedSmServices[AuthorizationService::class]->shouldReceive('isGranted')
            ->with(Permission::CAN_UPDATE_LICENCE_LICENCE_TYPE, $licence)
            ->andReturn(true)
            ->shouldReceive('isGranted')
            ->with(Permission::INTERNAL_USER, null)
            ->andReturn(false);

        $this->repoMap['Licence']->shouldReceive('fetchUsingId')
            ->with($command, Query::HYDRATE_OBJECT, 1)
            ->andReturn($licence);

        $this->sut->handleCommand($command);
    }

    public function testHandleCommandInternalGoodsSnToR()
    {
        $data = [
            'licenceType' => Licence::LICENCE_TYPE_RESTRICTED,
            'version' => 1
        ];
        $command = Cmd::create($data);

        $licence = $this->setupLicence($command, Licence::LICENCE_TYPE_STANDARD_NATIONAL, true);
        $this->setupAuth($licence);

        $this->expectDelinkTm($licence);
        $this->expectRemoveEstablishmentCd($licence);

        $this->expectReissueGoodsDiscs($licence);

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [],
            'messages' => [
                '33 goods discs ceased, 56 discs created',
                'Licence saved successfully',
            ]
        ];

        $this->assertEquals($expected, $result->toArray());

        $this->assertCount(0, $licence->getTmLicences());
        $this->assertNull($licence->getEstablishmentCd());
        $this->assertEquals($this->refData[Licence::LICENCE_TYPE_RESTRICTED], $licence->getLicenceType());
    }

    public function testHandleCommandInternalPsvSnToR()
    {
        $data = [
            'licenceType' => Licence::LICENCE_TYPE_RESTRICTED,
            'version' => 1
        ];
        $command = Cmd::create($data);

        $licence = $this->setupLicence($command, Licence::LICENCE_TYPE_STANDARD_NATIONAL, false);
        $this->setupAuth($licence);

        $this->expectDelinkTm($licence);
        $this->expectRemoveEstablishmentCd($licence);

        $this->expectReissuePsvDiscs($licence);

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [],
            'messages' => [
                '33 psv discs ceased, 56 discs created',
                'Licence saved successfully',
            ]
        ];

        $this->assertEquals($expected, $result->toArray());

        $this->assertCount(0, $licence->getTmLicences());
        $this->assertNull($licence->getEstablishmentCd());
        $this->assertEquals($this->refData[Licence::LICENCE_TYPE_RESTRICTED], $licence->getLicenceType());
    }

    public function testHandleCommandInternalGoodsSiToR()
    {
        $data = [
            'licenceType' => Licence::LICENCE_TYPE_RESTRICTED,
            'version' => 1
        ];
        $command = Cmd::create($data);

        $licence = $this->setupLicence($command, Licence::LICENCE_TYPE_STANDARD_INTERNATIONAL, true);
        $this->setupAuth($licence);

        $this->expectDelinkTm($licence);
        $this->expectRemoveEstablishmentCd($licence);
        $this->expectCeaseCommunityLicences($licence);

        $this->expectReissueGoodsDiscs($licence);

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [],
            'messages' => [
                'ReturnAllCommunityLicences',
                '33 goods discs ceased, 56 discs created',
                'Licence saved successfully',
            ]
        ];

        $this->assertEquals($expected, $result->toArray());

        $this->assertCount(0, $licence->getTmLicences());
        $this->assertNull($licence->getEstablishmentCd());
        $this->assertEquals($this->refData[Licence::LICENCE_TYPE_RESTRICTED], $licence->getLicenceType());
    }

    public function testHandleCommandInternalPsvSiToR()
    {
        $data = [
            'licenceType' => Licence::LICENCE_TYPE_RESTRICTED,
            'version' => 1
        ];
        $command = Cmd::create($data);

        $licence = $this->setupLicence($command, Licence::LICENCE_TYPE_STANDARD_INTERNATIONAL, false);
        $this->setupAuth($licence);

        $this->expectDelinkTm($licence);
        $this->expectRemoveEstablishmentCd($licence);

        $this->expectReissuePsvDiscs($licence);

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [],
            'messages' => [
                '33 psv discs ceased, 56 discs created',
                'Licence saved successfully',
            ]
        ];

        $this->assertEquals($expected, $result->toArray());

        $this->assertCount(0, $licence->getTmLicences());
        $this->assertNull($licence->getEstablishmentCd());
        $this->assertEquals($this->refData[Licence::LICENCE_TYPE_RESTRICTED], $licence->getLicenceType());
    }

    public function testHandleCommandInternalGoodsSiToSn()
    {
        $data = [
            'licenceType' => Licence::LICENCE_TYPE_STANDARD_NATIONAL,
            'version' => 1
        ];
        $command = Cmd::create($data);

        $licence = $this->setupLicence($command, Licence::LICENCE_TYPE_STANDARD_INTERNATIONAL, true);
        $this->setupAuth($licence);

        $this->expectCeaseCommunityLicences($licence);
        $this->expectReissueGoodsDiscs($licence);

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [],
            'messages' => [
                'ReturnAllCommunityLicences',
                '33 goods discs ceased, 56 discs created',
                'Licence saved successfully',
            ]
        ];

        $this->assertEquals($expected, $result->toArray());

        $this->assertNotNull($licence->getEstablishmentCd());
        $this->assertEquals($this->refData[Licence::LICENCE_TYPE_STANDARD_NATIONAL], $licence->getLicenceType());
    }

    public function testHandleCommandInternalPsvSiToSn()
    {
        $data = [
            'licenceType' => Licence::LICENCE_TYPE_STANDARD_NATIONAL,
            'version' => 1
        ];
        $command = Cmd::create($data);

        $licence = $this->setupLicence($command, Licence::LICENCE_TYPE_STANDARD_INTERNATIONAL, false);
        $this->setupAuth($licence);

        $this->expectCeaseCommunityLicences($licence);
        $this->expectReissuePsvDiscs($licence);

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [],
            'messages' => [
                'ReturnAllCommunityLicences',
                '33 psv discs ceased, 56 discs created',
                'Licence saved successfully',
            ]
        ];

        $this->assertEquals($expected, $result->toArray());

        $this->assertNotNull($licence->getEstablishmentCd());
        $this->assertEquals($this->refData[Licence::LICENCE_TYPE_STANDARD_NATIONAL], $licence->getLicenceType());
    }

    public function testHandleCommandInternalPsvOther()
    {
        $data = [
            'licenceType' => Licence::LICENCE_TYPE_STANDARD_INTERNATIONAL,
            'version' => 1
        ];
        $command = Cmd::create($data);

        $licence = $this->setupLicence($command, Licence::LICENCE_TYPE_STANDARD_NATIONAL, false);
        $this->setupAuth($licence);

        $this->expectReissuePsvDiscs($licence);

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [],
            'messages' => [
                '33 psv discs ceased, 56 discs created',
                'Licence saved successfully',
            ]
        ];

        $this->assertEquals($expected, $result->toArray());

        $this->assertNotNull($licence->getEstablishmentCd());
        $this->assertEquals($this->refData[Licence::LICENCE_TYPE_STANDARD_INTERNATIONAL], $licence->getLicenceType());
    }

    /**
     * Setup the Licence
     *
     * @param object $command
     * @param string $licenceType Licence type constant
     * @param bool   $goods
     *
     * @return Licence
     */
    private function setupLicence($command, $licenceType, $goods)
    {
        /** @var Licence $licence */
        $licence = m::mock(Licence::class)->makePartial();
        $licence->setId(208);
        $licence->setLicenceType($this->refData[$licenceType]);
        $goodsOrPsv = ($goods) ? Licence::LICENCE_CATEGORY_GOODS_VEHICLE : Licence::LICENCE_CATEGORY_PSV;
        $licence->setGoodsOrPsv($this->refData[$goodsOrPsv]);

        if ($goods) {
            $lv1 = new LicenceVehicle($licence, new Vehicle());
            $lv1->setId(143)
                ->setSpecifiedDate(new DateTime())
                ->setRemovalDate(new DateTime());
            $lv2 = new LicenceVehicle($licence, new Vehicle());
            $lv2->setId(243)
                ->setSpecifiedDate(null)
                ->setRemovalDate(new DateTime());
            $lv3 = new LicenceVehicle($licence, new Vehicle());
            $lv3->setId(343)
                ->setSpecifiedDate(new DateTime())
                ->setRemovalDate(null);
            $lv4 = new LicenceVehicle($licence, new Vehicle());
            $lv4->setId(443)
                ->setSpecifiedDate(null)
                ->setRemovalDate(null);
            $lv5 = new LicenceVehicle($licence, new Vehicle());
            $lv5->setId(543)
                ->setSpecifiedDate(new DateTime())
                ->setRemovalDate(null);
            $licence->setLicenceVehicles(new ArrayCollection([$lv1, $lv2, $lv3, $lv4, $lv5]));
        } else {
            $psvDisc1 = new PsvDisc($licence);
            $psvDisc1->setId(185)
                ->setCeasedDate(null);
            $psvDisc2 = new PsvDisc($licence);
            $psvDisc2->setId(285)
                ->setCeasedDate(new DateTime());
            $psvDisc3 = new PsvDisc($licence);
            $psvDisc3->setId(385)
                ->setCeasedDate(null);
            $licence->setPsvDiscs(new ArrayCollection([$psvDisc1, $psvDisc2, $psvDisc3]));
        }

        $licence->setEstablishMentCd($this->references[ContactDetails::class][12]);

        $this->repoMap['Licence']->shouldReceive('fetchUsingId')
            ->with($command, Query::HYDRATE_OBJECT, 1)
            ->andReturn($licence)
            ->shouldReceive('save')
            ->with($licence);

        return $licence;
    }

    private function setupAuth($licence)
    {
        $this->mockedSmServices[AuthorizationService::class]->shouldReceive('isGranted')
            ->with(Permission::CAN_UPDATE_LICENCE_LICENCE_TYPE, $licence)
            ->andReturn(true)
            ->shouldReceive('isGranted')
            ->with(Permission::INTERNAL_USER, null)
            ->andReturn(true);
    }

    private function expectReissueGoodsDiscs($licence)
    {
        $licenceId = $licence->getId();
        $this->repoMap['GoodsDisc']->shouldReceive('ceaseDiscsForLicence')->with($licenceId)->once()->andReturn(33);
        $this->repoMap['GoodsDisc']->shouldReceive('createDiscsForLicence')->with($licenceId)->once()->andReturn(56);
    }

    private function expectReissuePsvDiscs($licence)
    {
        $licenceId = $licence->getId();
        $this->repoMap['PsvDisc']->shouldReceive('ceaseDiscsForLicence')->with($licenceId)->once()->andReturn(33);
        $this->repoMap['PsvDisc']->shouldReceive('createPsvDiscs')->with($licenceId, 2)->once()->andReturn(56);
    }

    private function expectCeaseCommunityLicences($licence)
    {
        $this->expectedSideEffect(
            \Dvsa\Olcs\Api\Domain\Command\Licence\ReturnAllCommunityLicences::class,
            ['id' => $licence->getId()],
            (new \Dvsa\Olcs\Api\Domain\Command\Result())->addMessage('ReturnAllCommunityLicences')
        );
    }

    /**
     * Expect Transport Manager Licences to be unlinked
     *
     * @param type $licence
     */
    private function expectDelinkTm($licence)
    {
        $tml1 = new TransportManagerLicence($licence, new TransportManager());
        $tml1->setId(195);
        $tml2 = new TransportManagerLicence($licence, new TransportManager());
        $tml2->setId(295);
        $licence->setTmLicences(new ArrayCollection([$tml1, $tml2]));

        $this->repoMap['TransportManagerLicence']->shouldReceive('delete')->with($tml1)->once();
        $this->repoMap['TransportManagerLicence']->shouldReceive('delete')->with($tml2)->once();
    }

    /**
     * Expect Licence EstablishmentCd to be deleted
     */
    private function expectRemoveEstablishmentCd()
    {
        $this->repoMap['ContactDetails']->shouldReceive('delete')->with($this->references[ContactDetails::class][12])
            ->once();
    }
}
