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
use Dvsa\Olcs\Api\Domain\Command\Discs\CeasePsvDiscs;
use Dvsa\Olcs\Api\Domain\Command\LicenceVehicle\RemoveLicenceVehicle;
use Dvsa\Olcs\Api\Domain\Command\Tm\DeleteTransportManagerLicence;
use Dvsa\Olcs\Api\Domain\Command\Result;

/**
 * RevokeTest
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 * @author Josh Curtis <josh.curtis@valtech.co.uk>
 */
class SurrenderTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new CommandHandler();
        $this->mockRepo('Licence', Licence::class);

        parent::setUp();
    }

    protected function initReferences()
    {
        $this->refData = ['lsts_terminated', 'lsts_surrendered', 'lcat_psv'];

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
            array('discs' => null),
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

        $this->expectedSideEffect(
            ReturnAllCommunityLicences::class,
            [
                'id' => 532
            ],
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
}
