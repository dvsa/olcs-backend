<?php

/**
 * Void Test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\CommunityLic;

use Dvsa\Olcs\Api\Domain\Command\Application\UpdateApplicationCompletion;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Mockery as m;
use Dvsa\Olcs\Api\Domain\CommandHandler\CommunityLic\Void;
use Dvsa\Olcs\Api\Domain\Repository\CommunityLic as CommunityLicRepo;
use Dvsa\Olcs\Api\Domain\Repository\Licence as LicenceRepo;
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
        $this->mockRepo('Licence', LicenceRepo::class);

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
            ->twice()
            ->shouldReceive('changeStatusAndExpiryDate')
            ->with($this->refData[CommunityLicEntity::STATUS_VOID], m::type(DateTime::class))
            ->once()
            ->getMock();

        $mockLicence = m::mock()
            ->shouldReceive('hasCommunityLicenceOfficeCopy')
            ->with($communityLicenceIds)
            ->andReturn(true)
            ->once()
            ->getMock();

        $this->repoMap['Licence']
            ->shouldReceive('fetchById')
            ->with($licenceId)
            ->andReturn($mockLicence)
            ->once()
            ->getMock();

        $this->repoMap['CommunityLic']
            ->shouldReceive('fetchValidLicences')
            ->with($licenceId)
            ->andReturn([$mockCommunityLicence])
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

    public function testHandleCommandWithApplication()
    {
        $licenceId = 1;
        $communityLicenceIds = [10];

        $data = [
            'application' => 111,
            'licence' => $licenceId,
            'communityLicenceIds' => $communityLicenceIds,
            'checkOfficeCopy' => true
        ];

        $command = Cmd::create($data);

        $mockCommunityLicence = m::mock(CommunityLicEntity::class)
            ->shouldReceive('getId')
            ->andReturn(10)
            ->twice()
            ->shouldReceive('changeStatusAndExpiryDate')
            ->with($this->refData[CommunityLicEntity::STATUS_VOID], m::type(DateTime::class))
            ->once()
            ->getMock();

        $mockLicence = m::mock()
            ->shouldReceive('hasCommunityLicenceOfficeCopy')
            ->with($communityLicenceIds)
            ->andReturn(true)
            ->once()
            ->getMock();

        $this->repoMap['Licence']
            ->shouldReceive('fetchById')
            ->with($licenceId)
            ->andReturn($mockLicence)
            ->once()
            ->getMock();

        $this->repoMap['CommunityLic']
            ->shouldReceive('fetchValidLicences')
            ->with($licenceId)
            ->andReturn([$mockCommunityLicence])
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

        $this->expectedSideEffect(
            UpdateApplicationCompletion::class,
            [
                'id' => 111,
                'section' => 'communityLicences'
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

        $mockLicence = m::mock()
            ->shouldReceive('hasCommunityLicenceOfficeCopy')
            ->with($communityLicenceIds)
            ->andReturn(true)
            ->once()
            ->getMock();

        $this->repoMap['Licence']
            ->shouldReceive('fetchById')
            ->with($licenceId)
            ->andReturn($mockLicence)
            ->once()
            ->getMock();

        $this->repoMap['CommunityLic']
            ->shouldReceive('fetchValidLicences')
            ->with($licenceId)
            ->andReturn([$mockCommunityLicence])
            ->once()
            ->getMock();

        $this->sut->handleCommand($command);
    }
}
