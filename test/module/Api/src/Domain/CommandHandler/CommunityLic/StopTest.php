<?php

/**
 * Stop Test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\CommunityLic;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Mockery as m;
use Dvsa\Olcs\Api\Domain\CommandHandler\CommunityLic\Stop;
use Dvsa\Olcs\Api\Domain\Repository\CommunityLicRepo;
use Dvsa\Olcs\Api\Domain\Repository\CommunityLicSuspensionRepo;
use Dvsa\Olcs\Api\Domain\Repository\CommunityLicSuspensionReasonRepo;
use Dvsa\Olcs\Api\Domain\Repository\CommunityLicWithdrawalRepo;
use Dvsa\Olcs\Api\Domain\Repository\CommunityLicWithdrawalReasonRepo;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Transfer\Command\CommunityLic\Stop as Cmd;
use Dvsa\Olcs\Api\Entity\CommunityLic\CommunityLic as CommunityLicEntity;
use Dvsa\Olcs\Api\Entity\CommunityLic\CommunityLicSuspension as CommunityLicSuspensionEntity;
use Dvsa\Olcs\Api\Entity\CommunityLic\CommunityLicSuspensionReason as CommunityLicSuspensionReasonEntity;
use Dvsa\Olcs\Api\Entity\CommunityLic\CommunityLicWithdrawal as CommunityLicWithdrawalEntity;
use Dvsa\Olcs\Api\Entity\CommunityLic\CommunityLicWithdrawalReason as CommunityLicWithdrawalReasonEntity;
use Dvsa\Olcs\Api\Domain\Util\DateTime\DateTime;

/**
 * Stop Test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class StopTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new Stop();
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
            CommunityLicEntity::STATUS_PENDING,
            CommunityLicEntity::STATUS_WITHDRAWN,
            CommunityLicEntity::STATUS_SUSPENDED,
            'reason'
        ];

        $this->references = [
            CommunityLicSuspensionEntity::class => [
                111 => m::mock(CommunityLicSuspensionEntity::class)
            ],
            CommunityLicWithdrawalEntity::class => [
                111 => m::mock(CommunityLicWithdrawalEntity::class)
            ],
            CommunityLicEntity::class => [
                10 => m::mock(CommunityLicEntity::class)
            ],
        ];

        parent::initReferences();
    }

    public function testHandleCommandSuspension()
    {
        $licenceId = 1;
        $communityLicenceIds = [10];
        $startDate = '2014-01-01';
        $endDate = '2015-01-01';

        $data = [
            'licence' => $licenceId,
            'communityLicenceIds' => $communityLicenceIds,
            'type' => 'suspension',
            'startDate' => $startDate,
            'endDate' => $endDate,
            'reasons' => [
                'reason'
            ]
        ];

        $command = Cmd::create($data);

        $mockCommunityLicence = m::mock(CommunityLicEntity::class)
            ->shouldReceive('getId')
            ->andReturn(10)
            ->twice()
            ->shouldReceive('changeStatusAndExpiryDate')
            ->with($this->refData[CommunityLicEntity::STATUS_SUSPENDED])
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
            ->with(m::type(CommunityLicEntity::class))
            ->once()
            ->getMock();

        $communityLicSuspension = null;
        $this->repoMap['CommunityLicSuspension']
            ->shouldReceive('save')
            ->with(m::type(CommunityLicSuspensionEntity::class))
            ->andReturnUsing(
                function (CommunityLicSuspensionEntity $suspension) use (&$communityLicSuspension) {
                    $suspension->setId(111);
                    $communityLicSuspension = $suspension;
                }
            )
            ->once()
            ->getMock();

        $communityLicSuspensionReason = null;
        $this->repoMap['CommunityLicSuspensionReason']
            ->shouldReceive('save')
            ->with(m::type(CommunityLicSuspensionReasonEntity::class))
            ->andReturnUsing(
                function (CommunityLicSuspensionReasonEntity $suspReason) use (&$communityLicSuspensionReason) {
                    $suspReason->setId(222);
                    $communityLicSuspensionReason = $suspReason;
                }
            )
            ->once()
            ->getMock();

        $expected = [
            'id' => [
                'communityLic10' => 10
            ],
            'messages' => [
                'The licence 10 have been suspended'
            ]
        ];

        $result = $this->sut->handleCommand($command);
        $this->assertEquals($expected, $result->toArray());
        $this->assertEquals(111, $communityLicSuspension->getId());
        $this->assertEquals(10, $communityLicSuspension->getCommunityLic()->getId());
        $this->assertEquals(new DateTime($startDate), $communityLicSuspension->getStartDate());
        $this->assertEquals(new DateTime($endDate), $communityLicSuspension->getEndDate());
        $this->assertEquals(222, $communityLicSuspensionReason->getId());
        $this->assertEquals(111, $communityLicSuspensionReason->getCommunityLicSuspension()->getId());
        $this->assertEquals('reason', $communityLicSuspensionReason->getType()->getId());

    }

    public function testHandleCommandWithdrawal()
    {
        $licenceId = 1;
        $communityLicenceIds = [10];

        $data = [
            'licence' => $licenceId,
            'communityLicenceIds' => $communityLicenceIds,
            'type' => 'withdrawal',
            'startDate' => '',
            'endDate' => '',
            'reasons' => [
                'reason'
            ]
        ];
        $command = Cmd::create($data);
        $mockCommunityLicence = m::mock(CommunityLicEntity::class)
            ->shouldReceive('getId')
            ->andReturn(10)
            ->twice()
            ->shouldReceive('changeStatusAndExpiryDate')
            ->with(
                $this->refData[CommunityLicEntity::STATUS_WITHDRAWN],
                m::type(DateTime::class)
            )
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
            ->with(m::type(CommunityLicEntity::class))
            ->once()
            ->getMock();

        $communityLicWithdrawal = null;
        $this->repoMap['CommunityLicWithdrawal']
            ->shouldReceive('save')
            ->with(m::type(CommunityLicWithdrawalEntity::class))
            ->andReturnUsing(
                function (CommunityLicWithdrawalEntity $withdrawal) use (&$communityLicWithdrawal) {
                    $withdrawal->setId(111);
                    $communityLicWithdrawal = $withdrawal;
                }
            )
            ->once()
            ->getMock();

        $communityLicWithdrawalReason = null;
        $this->repoMap['CommunityLicWithdrawalReason']
            ->shouldReceive('save')
            ->with(m::type(CommunityLicWithdrawalReasonEntity::class))
            ->andReturnUsing(
                function (CommunityLicWithdrawalReasonEntity $withdrawalReason) use (&$communityLicWithdrawalReason) {
                    $withdrawalReason->setId(222);
                    $communityLicWithdrawalReason = $withdrawalReason;
                }
            )
            ->once()
            ->getMock();

        $expected = [
            'id' => [
                'communityLic10' => 10
            ],
            'messages' => [
                'The licence 10 have been withdrawn'
            ]
        ];

        $result = $this->sut->handleCommand($command);
        $this->assertEquals($expected, $result->toArray());
        $this->assertEquals(111, $communityLicWithdrawal->getId());
        $this->assertEquals(10, $communityLicWithdrawal->getCommunityLic()->getId());
        $this->assertEquals(222, $communityLicWithdrawalReason->getId());
        $this->assertEquals(111, $communityLicWithdrawalReason->getCommunityLicWithdrawal()->getId());
        $this->assertEquals('reason', $communityLicWithdrawalReason->getType()->getId());
    }

    public function testCommandHandlerWithException()
    {
        $this->setExpectedException('Dvsa\Olcs\Api\Domain\Exception\ValidationException');

        $licenceId = 1;
        $communityLicenceIds = [10];

        $data = [
            'licence' => $licenceId,
            'communityLicenceIds' => $communityLicenceIds,
            'type' => 'withdrawal',
            'startDate' => '',
            'endDate' => '',
            'reasons' => [
                'reason'
            ]
        ];
        $command = Cmd::create($data);

        $mockCommunityLicence = m::mock()
            ->shouldReceive('getStatus')
            ->andReturn(
                m::mock()
                ->shouldReceive('getId')
                ->andReturn(CommunityLicEntity::STATUS_PENDING)
                ->once()
                ->getMock()
            )
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
