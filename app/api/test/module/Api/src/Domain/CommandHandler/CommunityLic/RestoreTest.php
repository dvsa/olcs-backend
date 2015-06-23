<?php

/**
 * Restore Test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\CommunityLic;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Mockery as m;
use Dvsa\Olcs\Api\Domain\CommandHandler\CommunityLic\Restore;
use Dvsa\Olcs\Api\Domain\Repository\CommunityLicRepo;
use Dvsa\Olcs\Api\Domain\Repository\CommunityLicSuspensionRepo;
use Dvsa\Olcs\Api\Domain\Repository\CommunityLicSuspensionReasonRepo;
use Dvsa\Olcs\Api\Domain\Repository\CommunityLicWithdrawalRepo;
use Dvsa\Olcs\Api\Domain\Repository\CommunityLicWithdrawalReasonRepo;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Transfer\Command\CommunityLic\Restore as Cmd;
use Dvsa\Olcs\Api\Entity\CommunityLic\CommunityLic as CommunityLicEntity;
use Dvsa\Olcs\Api\Domain\Command\Licence\UpdateTotalCommunityLicences as UpdateTotalCommunityLicencesCmd;
use Dvsa\Olcs\Api\Domain\Util\DateTime\DateTime;

/**
 * Restore Test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class RestoreTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new Restore();
        $this->mockRepo('CommunityLic', CommunityLicRepo::class);
        $this->mockRepo('CommunityLicSuspension', CommunityLicSuspensionRepo::class);
        $this->mockRepo('CommunityLicSuspensionReason', CommunityLicSuspensionReasonRepo::class);
        $this->mockRepo('CommunityLicWithdrawal', CommunityLicWithdrawalRepo::class);
        $this->mockRepo('CommunityLicWithdrawalReason', CommunityLicWithdrawalReasonRepo::class);

        parent::setUp();
    }

    protected function initReferences()
    {
        $this->refData = [
            CommunityLicEntity::STATUS_ACTIVE,
            CommunityLicEntity::STATUS_PENDING
        ];

        parent::initReferences();
    }

    protected function mockDeleteSuspensions($ids)
    {
        $mockSuspension = m::mock()
            ->shouldReceive('getId')
            ->andReturn(2)
            ->once()
            ->getMock();

        $this->repoMap['CommunityLicSuspension']
            ->shouldReceive('fetchByCommunityLicIds')
            ->with($ids)
            ->andReturn([$mockSuspension])
            ->once()
            ->shouldReceive('delete')
            ->once()
            ->getMock();

        $this->repoMap['CommunityLicSuspensionReason']
            ->shouldReceive('fetchBySuspensionIds')
            ->with([2])
            ->andReturn(['suspensionReason'])
            ->once()
            ->shouldReceive('delete')
            ->with('suspensionReason')
            ->once();
    }

    protected function mockDeleteWithdrawals($ids)
    {
        $mockWithdrawal = m::mock()
            ->shouldReceive('getId')
            ->andReturn(2)
            ->once()
            ->getMock();

        $this->repoMap['CommunityLicWithdrawal']
            ->shouldReceive('fetchByCommunityLicIds')
            ->with($ids)
            ->andReturn([$mockWithdrawal])
            ->once()
            ->shouldReceive('delete')
            ->once()
            ->getMock();

        $this->repoMap['CommunityLicWithdrawalReason']
            ->shouldReceive('fetchByWithdrawalIds')
            ->with([2])
            ->andReturn(['withdrawalReason'])
            ->once()
            ->shouldReceive('delete')
            ->with('withdrawalReason')
            ->once();
    }

    /**
     * @dataProvider statusProvider
     */
    public function testHandleCommand($specifiedDate, $status)
    {
        $licenceId = 1;
        $communityLicenceIds = [10];

        $data = [
            'licence' => $licenceId,
            'communityLicenceIds' => $communityLicenceIds
        ];

        $command = Cmd::create($data);

        $mockOfficeCopy = m::mock()
            ->shouldReceive('getStatus')
            ->andReturn(
                m::mock()
                ->shouldReceive('getId')
                ->andReturn(CommunityLicEntity::STATUS_ACTIVE)
                ->once()
                ->getMock()
            )
            ->once()
            ->getMock();

        $mockCommunityLicence = m::mock(CommunityLicEntity::class)
            ->shouldReceive('getId')
            ->andReturn(10)
            ->once()
            ->shouldReceive('getSpecifiedDate')
            ->andReturn($specifiedDate)
            ->twice()
            ->shouldReceive('changeStatusAndExpiryDate')
            ->with($this->refData[$status], null)
            ->once()
            ->getMock();

        $communityLic = null;

        $this->repoMap['CommunityLic']
            ->shouldReceive('hasOfficeCopy')
            ->with($licenceId, $communityLicenceIds)
            ->andReturn(false)
            ->once()
            ->shouldReceive('fetchOfficeCopy')
            ->with($licenceId)
            ->andReturn($mockOfficeCopy)
            ->once()
            ->shouldReceive('fetchLicencesByIds')
            ->with($communityLicenceIds)
            ->andReturn([$mockCommunityLicence])
            ->once()
            ->shouldReceive('save')
            ->with(m::type(CommunityLicEntity::class))
            ->andReturnUsing(
                function (CommunityLicEntity $lic) use (&$communityLic) {
                    $communityLic = $lic;
                }
            )
            ->once()
            ->getMock();

        $this->mockDeleteSuspensions($communityLicenceIds);
        $this->mockDeleteWithdrawals($communityLicenceIds);

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
                'Community Licence 10 restored'
            ]
        ];

        $result = $this->sut->handleCommand($command);
        $this->assertEquals($expected, $result->toArray());
        $this->assertEquals($specifiedDate, $communityLic->getSpecifiedDate());
    }

    public function statusProvider()
    {
        return [
            [new DateTime('now'),  CommunityLicEntity::STATUS_ACTIVE],
            [null, CommunityLicEntity::STATUS_PENDING],
        ];
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

        $mockOfficeCopy = m::mock()
            ->shouldReceive('getStatus')
            ->andReturn(
                m::mock()
                ->shouldReceive('getId')
                ->andReturn(CommunityLicEntity::STATUS_WITHDRAWN)
                ->once()
                ->getMock()
            )
            ->once()
            ->getMock();

        $this->repoMap['CommunityLic']
            ->shouldReceive('hasOfficeCopy')
            ->with($licenceId, $communityLicenceIds)
            ->andReturn(false)
            ->once()
            ->shouldReceive('fetchOfficeCopy')
            ->with($licenceId)
            ->andReturn($mockOfficeCopy)
            ->once()
            ->getMock();

        $this->sut->handleCommand($command);
    }
}
