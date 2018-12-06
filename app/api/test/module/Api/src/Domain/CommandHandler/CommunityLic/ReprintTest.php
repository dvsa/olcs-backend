<?php

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\CommunityLic;

use Dvsa\Olcs\Api\Domain\Command\CommunityLic\GenerateBatch as GenerateBatchCmd;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\CommunityLic\Reprint;
use Dvsa\Olcs\Api\Domain\Repository\CommunityLic as CommunityLicRepo;
use Dvsa\Olcs\Api\Domain\Repository\Licence as LicenceRepo;
use Dvsa\Olcs\Api\Entity\CommunityLic\CommunityLic as CommunityLicEntity;
use Dvsa\Olcs\Api\Entity\Licence\Licence as LicenceEntity;
use Dvsa\Olcs\Transfer\Command as TransferCmd;
use Dvsa\Olcs\Transfer\Command\CommunityLic\Reprint as Cmd;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Api\Domain\Command\Licence\UpdateTotalCommunityLicences as UpdateTotalCommunityLicencesCommand;
use Mockery as m;

/**
 * @covers \Dvsa\Olcs\Api\Domain\CommandHandler\CommunityLic\Reprint
 */
class ReprintTest extends CommandHandlerTestCase
{
    /** @var Reprint */
    protected $sut;

    public function setUp()
    {
        $this->sut = new Reprint();

        $this->mockRepo('CommunityLic', CommunityLicRepo::class);
        $this->mockRepo('Licence', LicenceRepo::class);

        parent::setUp();
    }

    protected function initReferences()
    {
        $this->refData = [
            CommunityLicEntity::STATUS_ACTIVE
        ];

        $this->references = [
            LicenceEntity::class => [
                1 => m::mock(LicenceEntity::class)
            ],
        ];

        parent::initReferences();
    }

    public function testHandleCommand()
    {
        $licenceId = 1;
        $communityLicenceIds = [10];
        $issueNo = 2;
        $serialNoPrefix = 'A';

        $data = [
            'licence' => $licenceId,
            'communityLicenceIds' => $communityLicenceIds
        ];

        $command = Cmd::create($data);

        $mockActiveLicence = m::mock()
            ->shouldReceive('getId')
            ->andReturn(10)
            ->once()
            ->shouldReceive('getIssueNo')
            ->andReturn($issueNo)
            ->once()
            ->getMock();

        /** @var CommunityLicEntity $communityLic */
        $communityLic = null;

        $this->repoMap['CommunityLic']
            ->shouldReceive('fetchActiveLicences')
            ->with($licenceId)
            ->andReturn([$mockActiveLicence])
            ->once()
            ->shouldReceive('fetchLicencesByIds')
            ->with($communityLicenceIds)
            ->andReturn([$mockActiveLicence])
            ->once()
            ->shouldReceive('save')
            ->with(m::type(CommunityLicEntity::class))
            ->andReturnUsing(
                function (CommunityLicEntity $lic) use (&$communityLic) {
                    $lic->setId(111);
                    $communityLic = $lic;
                }
            )
            ->once()
            ->getMock();

        $this->expectedSideEffect(
            TransferCmd\CommunityLic\Annul::class,
            [
                'licence' => $licenceId,
                'communityLicenceIds' => $communityLicenceIds,
                'checkOfficeCopy' => false
            ],
            new Result()
        );

        $this->expectedSideEffect(UpdateTotalCommunityLicencesCommand::class, ['id' => $licenceId], new Result());

        $mockLicence = m::mock()
            ->shouldReceive('getSerialNoPrefixFromTrafficArea')
            ->andReturn($serialNoPrefix)
            ->once()
            ->getMock();

        $this->repoMap['Licence']
            ->shouldReceive('fetchById')
            ->with($licenceId)
            ->andReturn($mockLicence)
            ->once()
            ->getMock();

        $this->expectedSideEffect(
            GenerateBatchCmd::class,
            [
                'licence' => $licenceId,
                'communityLicenceIds' => $communityLicenceIds,
                'identifier' => null
            ],
            new Result()
        );

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [
                'communityLic111' => 111
            ],
            'messages' => [
                'The selected licence with issue number 2 has been generated'
            ]
        ];

        $this->assertEquals($expected, $result->toArray());
        $this->assertEquals('A', $communityLic->getSerialNoPrefix());
        $this->assertEquals($licenceId, $communityLic->getLicence()->getId());
        $this->assertEquals(2, $communityLic->getIssueNo());
        $this->assertEquals(CommunityLicEntity::STATUS_ACTIVE, $communityLic->getStatus()->getId());
    }

    public function testHandleCommandWithException()
    {
        $this->setExpectedException('Dvsa\Olcs\Api\Domain\Exception\ValidationException');

        $licenceId = 1;
        $communityLicenceIds = [10];

        $data = [
            'licence' => $licenceId,
            'communityLicenceIds' => $communityLicenceIds
        ];

        $command = Cmd::create($data);

        $mockActiveLicence = m::mock()
            ->shouldReceive('getId')
            ->andReturn(null)
            ->once()
            ->getMock();

        $this->repoMap['CommunityLic']
            ->shouldReceive('fetchActiveLicences')
            ->with($licenceId)
            ->andReturn([$mockActiveLicence])
            ->once()
            ->getMock();

        $this->sut->handleCommand($command);
    }
}
