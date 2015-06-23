<?php

/**
 * Void Test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\CommunityLic;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Mockery as m;
use Dvsa\Olcs\Api\Domain\CommandHandler\CommunityLic\Void;
use Dvsa\Olcs\Api\Domain\Repository\CommunityLicRepo;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Transfer\Command\CommunityLic\Void as Cmd;
use Dvsa\Olcs\Api\Entity\CommunityLic\CommunityLic as CommunityLicEntity;
use Dvsa\Olcs\Api\Domain\Util\DateTime\DateTime;
use Dvsa\Olcs\Api\Domain\Command\Licence\UpdateTotalCommunityLicences as UpdateTotalCommunityLicencesCmd;

/**
 * Void Test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class VoidTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new Void();
        $this->mockRepo('CommunityLic', CommunityLicRepo::class);

        parent::setUp();
    }

    protected function initReferences()
    {
        $this->refData = [
            CommunityLicEntity::STATUS_VOID
        ];

        parent::initReferences();
    }

    public function testHandleCommand()
    {
        $licenceId = 1;
        $communityLicenceIds = [10];

        $data = [
            'licence' => $licenceId,
            'communityLicenceIds' => $communityLicenceIds,
            'checkOfficeCopy' => true
        ];

        $command = Cmd::create($data);

        $mockCommunityLicence = m::mock(CommunityLicEntity::class)
            ->shouldReceive('getId')
            ->andReturn(10)
            ->once()
            ->shouldReceive('changeStatusAndExpiryDate')
            ->with($this->refData[CommunityLicEntity::STATUS_VOID], m::type(DateTime::class))
            ->once()
            ->getMock();

        $this->repoMap['CommunityLic']
            ->shouldReceive('hasOfficeCopy')
            ->with($licenceId, $communityLicenceIds)
            ->andReturn(false)
            ->once()
            ->shouldReceive('fetchLicencesByIds')
            ->andReturn([$mockCommunityLicence])
            ->once()
            ->shouldReceive('save')
            ->with($mockCommunityLicence)
            ->once()
            ->getMock();

        $this->expectedSideEffect(
            UpdateTotalCommunityLicencesCmd::class,
            [
                'id' => $licenceId,
            ],
            new Result()
        );

        $expected = [
            'id' => [
                'communityLic10' => 10
            ],
            'messages' => [
                'Community Licence 10 voided'
            ]
        ];

        $result = $this->sut->handleCommand($command);
        $this->assertEquals($expected, $result->toArray());

    }

    public function testCommandHandlerWithException()
    {
        $this->setExpectedException('Dvsa\Olcs\Api\Domain\Exception\ValidationException');

        $licenceId = 1;
        $communityLicenceIds = [10];

        $data = [
            'licence' => $licenceId,
            'communityLicenceIds' => $communityLicenceIds,
            'checkOfficeCopy' => true
        ];
        $command = Cmd::create($data);

        $mockCommunityLicence = m::mock()
            ->shouldReceive('getId')
            ->andReturn(11)
            ->once()
            ->getMock();

        $this->repoMap['CommunityLic']
            ->shouldReceive('hasOfficeCopy')
            ->with($licenceId, $communityLicenceIds)
            ->andReturn(true)
            ->once()
            ->shouldReceive('fetchValidLicences')
            ->with($licenceId)
            ->andReturn([$mockCommunityLicence])
            ->once()
            ->getMock();

        $this->sut->handleCommand($command);
    }
}
