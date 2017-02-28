<?php

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Licence;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\Licence\ContinueLicence as CommandHandler;
use Dvsa\Olcs\Api\Entity\Licence\ContinuationDetail;
use Dvsa\Olcs\Api\Entity\Licence\Licence as LicenceEntity;
use Dvsa\Olcs\Api\Entity\Organisation\Organisation;
use Dvsa\Olcs\Api\Entity\System\RefData;
use Dvsa\Olcs\Transfer\Command\Licence\ContinueLicence as Command;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Api\Domain\Command\Queue\Create as CreateQueueCmd;
use Dvsa\Olcs\Api\Entity\Queue\Queue as QueueEntity;

/**
 * ContinueLicenceTest
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 *
 */
class ContinueLicenceTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new CommandHandler();
        $this->mockRepo('Licence', LicenceEntity::class);
        $this->mockRepo('ContinuationDetail', \Dvsa\Olcs\Api\Domain\Repository\ContinuationDetail::class);
        $this->mockRepo('GoodsDisc', \Dvsa\Olcs\Api\Domain\Repository\GoodsDisc::class);

        parent::setUp();
    }

    protected function initReferences()
    {
        $this->refData = [
            'con_det_sts_complete',
            LicenceEntity::LICENCE_TYPE_STANDARD_NATIONAL,
            LicenceEntity::LICENCE_TYPE_STANDARD_INTERNATIONAL,
            LicenceEntity::LICENCE_TYPE_SPECIAL_RESTRICTED,
            LicenceEntity::LICENCE_TYPE_RESTRICTED,
            LicenceEntity::LICENCE_CATEGORY_GOODS_VEHICLE,
            LicenceEntity::LICENCE_CATEGORY_PSV,
        ];

        parent::initReferences();
    }

    private function getGoodsLicence($licenceId, $type = LicenceEntity::LICENCE_TYPE_STANDARD_NATIONAL)
    {
        $organisation = new Organisation();
        $licence = new LicenceEntity($organisation, new RefData());
        $licence->setId($licenceId);
        $licence->setLicenceType($this->refData[$type]);
        $licence->setGoodsOrPsv($this->refData[LicenceEntity::LICENCE_CATEGORY_GOODS_VEHICLE]);

        return $licence;
    }

    private function getPsvLicence($licenceId, $type = LicenceEntity::LICENCE_TYPE_STANDARD_NATIONAL)
    {
        $organisation = new Organisation();
        $licence = new LicenceEntity($organisation, new RefData());
        $licence->setId($licenceId);
        $licence->setLicenceType($this->refData[$type]);
        $licence->setGoodsOrPsv($this->refData[LicenceEntity::LICENCE_CATEGORY_PSV]);

        return $licence;
    }

    public function testHandleCommandNoContinuationDetail()
    {
        $data = ['id' => 717, 'version' => 654];

        $command = Command::create($data);

        $this->repoMap['Licence']->shouldReceive('fetchById')->with(717, Query::HYDRATE_OBJECT, 654)
            ->once()->andReturn($this->getGoodsLicence(717));

        $this->repoMap['ContinuationDetail']->shouldReceive('fetchForLicence')->with(717)->once()
            ->andReturn([]);

        $this->setExpectedException(\Dvsa\Olcs\Api\Domain\Exception\RuntimeException::class);

        $this->sut->handleCommand($command);
    }

    public function testHandleCommandPsvSpecialRestricted()
    {
        $data = ['id' => 717, 'version' => 654];

        $command = Command::create($data);

        $licence = $this->getPsvLicence(717, LicenceEntity::LICENCE_TYPE_SPECIAL_RESTRICTED);
        $licence->setExpiryDate('2015-07-17');
        $licence->setReviewDate('2015-12-04');

        $continuationDetail = new ContinuationDetail();

        $this->repoMap['Licence']->shouldReceive('fetchById')->with(717, Query::HYDRATE_OBJECT, 654)
            ->once()->andReturn($licence);

        $this->repoMap['ContinuationDetail']->shouldReceive('fetchForLicence')->with(717)->once()
            ->andReturn([$continuationDetail]);

        $this->repoMap['Licence']->shouldReceive('save')->once()->andReturnUsing(
            function (LicenceEntity $saveLicence) {
                $this->assertEquals(new \DateTime('2020-07-17'), $saveLicence->getExpiryDate());
                $this->assertEquals(new \DateTime('2020-12-04'), $saveLicence->getReviewDate());
            }
        );

        $this->expectedSideEffect(\Dvsa\Olcs\Transfer\Command\Licence\PrintLicence::class, ['id' => 717], new Result());

        $this->repoMap['ContinuationDetail']->shouldReceive('save')->once()->andReturnUsing(
            function (ContinuationDetail $saveCd) {
                $this->assertSame($this->refData['con_det_sts_complete'], $saveCd->getStatus());
            }
        );

        $result = $this->sut->handleCommand($command);

        $this->assertSame(['Licence 717 continued'], $result->getMessages());
    }

    public function testHandleCommandPsvStandardNational()
    {
        $data = ['id' => 717, 'version' => 654];

        $command = Command::create($data);

        $licence = $this->getPsvLicence(717, LicenceEntity::LICENCE_TYPE_STANDARD_NATIONAL);
        $licence->setExpiryDate('2015-07-17');
        $licence->setReviewDate('2015-12-04');
        $licence->setPsvDiscs(['disc1', 'disc2']);

        $continuationDetail = new ContinuationDetail();
        $continuationDetail->setTotAuthVehicles(434);
        $continuationDetail->setTotPsvDiscs(7);

        $this->repoMap['Licence']->shouldReceive('fetchById')->with(717, Query::HYDRATE_OBJECT, 654)
            ->once()->andReturn($licence);

        $this->repoMap['ContinuationDetail']->shouldReceive('fetchForLicence')->with(717)->once()
            ->andReturn([$continuationDetail]);

        $this->repoMap['Licence']->shouldReceive('save')->once()->andReturnUsing(
            function (LicenceEntity $saveLicence) {
                $this->assertEquals(new \DateTime('2020-07-17'), $saveLicence->getExpiryDate());
                $this->assertEquals(new \DateTime('2020-12-04'), $saveLicence->getReviewDate());
                $this->assertEquals(434, $saveLicence->getTotAuthVehicles());
            }
        );

        $this->expectedSideEffect(
            \Dvsa\Olcs\Api\Domain\Command\Discs\CeasePsvDiscs::class,
            ['licence' => 717],
            new Result()
        );
        $this->expectedSideEffect(
            \Dvsa\Olcs\Transfer\Command\Licence\CreatePsvDiscs::class,
            ['licence' => 717, 'amount' => 7, 'isCopy' => 'N'],
            new Result()
        );

        $this->expectedSideEffect(\Dvsa\Olcs\Transfer\Command\Licence\PrintLicence::class, ['id' => 717], new Result());

        $this->repoMap['ContinuationDetail']->shouldReceive('save')->once()->andReturnUsing(
            function (ContinuationDetail $saveCd) {
                $this->assertSame($this->refData['con_det_sts_complete'], $saveCd->getStatus());
            }
        );

        $result = $this->sut->handleCommand($command);

        $this->assertSame(['Licence 717 continued'], $result->getMessages());
    }

    public function testHandleCommandPsvStandardInternational()
    {
        $licenceId = 717;
        $data = ['id' => $licenceId, 'version' => 654];
        $totCommunityLic = 34;

        $command = Command::create($data);

        $licence = $this->getPsvLicence(717, LicenceEntity::LICENCE_TYPE_STANDARD_INTERNATIONAL);
        $licence->setExpiryDate('2015-07-17');
        $licence->setReviewDate('2015-12-04');
        $licence->setPsvDiscs(['disc1', 'disc2']);
        $licence->setId($licenceId);

        $continuationDetail = new ContinuationDetail();
        $continuationDetail->setTotAuthVehicles(434);
        $continuationDetail->setTotPsvDiscs(7);
        $continuationDetail->setTotCommunityLicences($totCommunityLic);

        $this->repoMap['Licence']->shouldReceive('fetchById')->with($licenceId, Query::HYDRATE_OBJECT, 654)
            ->once()->andReturn($licence);

        $this->repoMap['ContinuationDetail']->shouldReceive('fetchForLicence')->with($licenceId)->once()
            ->andReturn([$continuationDetail]);

        $this->repoMap['Licence']->shouldReceive('save')->once()->andReturnUsing(
            function (LicenceEntity $saveLicence) {
                $this->assertEquals(new \DateTime('2020-07-17'), $saveLicence->getExpiryDate());
                $this->assertEquals(new \DateTime('2020-12-04'), $saveLicence->getReviewDate());
                $this->assertEquals(434, $saveLicence->getTotAuthVehicles());
            }
        );

        $this->expectedSideEffect(
            \Dvsa\Olcs\Api\Domain\Command\Discs\CeasePsvDiscs::class,
            ['licence' => $licenceId],
            new Result()
        );
        $this->expectedSideEffect(
            \Dvsa\Olcs\Transfer\Command\Licence\CreatePsvDiscs::class,
            ['licence' => $licenceId, 'amount' => 7, 'isCopy' => 'N'],
            new Result()
        );

        $this->expectedSideEffect(
            \Dvsa\Olcs\Api\Domain\Command\Licence\VoidAllCommunityLicences::class,
            ['id' => $licenceId],
            new Result()
        );
        $this->expectedSideEffect(
            CreateQueueCmd::class,
            [
                'type' => QueueEntity::TYPE_CREATE_COM_LIC,
                'status' => QueueEntity::STATUS_QUEUED,
                'options' => json_encode(
                    [
                        'licence' => $licenceId,
                        'totalLicences' => $totCommunityLic
                    ]
                ),
            ],
            new Result()
        );

        $this->expectedSideEffect(
            \Dvsa\Olcs\Transfer\Command\Licence\PrintLicence::class,
            ['id' => $licenceId], new Result()
        );

        $this->repoMap['ContinuationDetail']->shouldReceive('save')->once()->andReturnUsing(
            function (ContinuationDetail $saveCd) {
                $this->assertSame($this->refData['con_det_sts_complete'], $saveCd->getStatus());
            }
        );

        $result = $this->sut->handleCommand($command);

        $this->assertSame(['Licence 717 continued'], $result->getMessages());
    }

    public function testHandleCommandGoodsNotStandardInternational()
    {
        $data = ['id' => 717, 'version' => 654];

        $command = Command::create($data);

        $licence = $this->getGoodsLicence(717, LicenceEntity::LICENCE_TYPE_RESTRICTED);
        $licence->setExpiryDate('2015-07-17');
        $licence->setReviewDate('2015-12-04');
        $lv1 = new \Dvsa\Olcs\Api\Entity\Licence\LicenceVehicle($licence, new \Dvsa\Olcs\Api\Entity\Vehicle\Vehicle());
        $lv1->setId(12);
        $lv2 = new \Dvsa\Olcs\Api\Entity\Licence\LicenceVehicle($licence, new \Dvsa\Olcs\Api\Entity\Vehicle\Vehicle());
        $lv2->setId(254);
        $licence->setLicenceVehicles([$lv1, $lv2]);

        $continuationDetail = new ContinuationDetail();
        $continuationDetail->setTotCommunityLicences(34);

        $this->repoMap['GoodsDisc']->shouldReceive('createDiscsForLicence')->with(717)->once()->andReturn(1502);

        $this->repoMap['Licence']->shouldReceive('fetchById')->with(717, Query::HYDRATE_OBJECT, 654)
            ->once()->andReturn($licence);

        $this->repoMap['ContinuationDetail']->shouldReceive('fetchForLicence')->with(717)->once()
            ->andReturn([$continuationDetail]);

        $this->repoMap['Licence']->shouldReceive('save')->once()->andReturnUsing(
            function (LicenceEntity $saveLicence) {
                $this->assertEquals(new \DateTime('2020-07-17'), $saveLicence->getExpiryDate());
                $this->assertEquals(new \DateTime('2020-12-04'), $saveLicence->getReviewDate());
            }
        );

        $this->expectedSideEffect(
            \Dvsa\Olcs\Api\Domain\Command\Discs\CeaseGoodsDiscs::class,
            ['licence' => 717],
            new Result()
        );

        $this->expectedSideEffect(\Dvsa\Olcs\Transfer\Command\Licence\PrintLicence::class, ['id' => 717], new Result());

        $this->repoMap['ContinuationDetail']->shouldReceive('save')->once()->andReturnUsing(
            function (ContinuationDetail $saveCd) {
                $this->assertSame($this->refData['con_det_sts_complete'], $saveCd->getStatus());
            }
        );

        $result = $this->sut->handleCommand($command);

        $this->assertSame(['1502 goods discs created', 'Licence 717 continued'], $result->getMessages());
    }

    public function testHandleCommandGoodsStandardInternational()
    {
        $licenceId = 717;
        $totCommunityLic = 34;
        $data = ['id' => $licenceId, 'version' => 654];

        $command = Command::create($data);

        $licence = $this->getGoodsLicence(717, LicenceEntity::LICENCE_TYPE_STANDARD_INTERNATIONAL);
        $licence->setExpiryDate('2015-07-17');
        $licence->setReviewDate('2015-12-04');
        $lv1 = new \Dvsa\Olcs\Api\Entity\Licence\LicenceVehicle($licence, new \Dvsa\Olcs\Api\Entity\Vehicle\Vehicle());
        $lv1->setId(12);
        $lv2 = new \Dvsa\Olcs\Api\Entity\Licence\LicenceVehicle($licence, new \Dvsa\Olcs\Api\Entity\Vehicle\Vehicle());
        $lv2->setId(254);
        $licence->setLicenceVehicles([$lv1, $lv2]);

        $continuationDetail = new ContinuationDetail();
        $continuationDetail->setTotCommunityLicences($totCommunityLic);

        $this->repoMap['GoodsDisc']->shouldReceive('createDiscsForLicence')->with($licenceId)->once()->andReturn(1502);

        $this->repoMap['Licence']->shouldReceive('fetchById')->with(717, Query::HYDRATE_OBJECT, 654)
            ->once()->andReturn($licence);

        $this->repoMap['ContinuationDetail']->shouldReceive('fetchForLicence')->with($licenceId)->once()
            ->andReturn([$continuationDetail]);

        $this->repoMap['Licence']->shouldReceive('save')->once()->andReturnUsing(
            function (LicenceEntity $saveLicence) {
                $this->assertEquals(new \DateTime('2020-07-17'), $saveLicence->getExpiryDate());
                $this->assertEquals(new \DateTime('2020-12-04'), $saveLicence->getReviewDate());
            }
        );

        $this->expectedSideEffect(
            \Dvsa\Olcs\Api\Domain\Command\Discs\CeaseGoodsDiscs::class,
            ['licence' => $licenceId],
            new Result()
        );
        $this->expectedSideEffect(
            \Dvsa\Olcs\Api\Domain\Command\Licence\VoidAllCommunityLicences::class,
            ['id' => $licenceId],
            new Result()
        );
        $this->expectedSideEffect(
            CreateQueueCmd::class,
            [
                'type' => QueueEntity::TYPE_CREATE_COM_LIC,
                'status' => QueueEntity::STATUS_QUEUED,
                'options' => json_encode(
                    [
                        'licence' => $licenceId,
                        'totalLicences' => $totCommunityLic
                    ]
                ),
            ],
            new Result()
        );

        $this->expectedSideEffect(
            \Dvsa\Olcs\Transfer\Command\Licence\PrintLicence::class,
            ['id' => $licenceId], new Result()
        );

        $this->repoMap['ContinuationDetail']->shouldReceive('save')->once()->andReturnUsing(
            function (ContinuationDetail $saveCd) {
                $this->assertSame($this->refData['con_det_sts_complete'], $saveCd->getStatus());
            }
        );

        $result = $this->sut->handleCommand($command);

        $this->assertSame(['1502 goods discs created', 'Licence 717 continued'], $result->getMessages());
    }
}
