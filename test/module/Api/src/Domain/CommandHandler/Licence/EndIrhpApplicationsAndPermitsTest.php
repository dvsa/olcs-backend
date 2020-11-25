<?php

/**
 * EndIrhpApplicationsAndPermitsTest
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Licence;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Query;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Api\Domain\CommandHandler\Licence\EndIrhpApplicationsAndPermits as CommandHandler;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\Repository\IrhpPermit as IrhpPermitRepository;
use Dvsa\Olcs\Api\Domain\Repository\Licence as LicenceRepository;
use Dvsa\Olcs\Api\Entity\IrhpInterface;
use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Dvsa\Olcs\Api\Entity\Permits\IrhpApplication;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermit;
use Dvsa\Olcs\Api\Entity\WithdrawableInterface;
use Dvsa\Olcs\Transfer\Command\IrhpApplication\CancelApplication;
use Dvsa\Olcs\Transfer\Command\IrhpApplication\Withdraw;
use Dvsa\Olcs\Transfer\Command\IrhpPermit\Terminate;
use Dvsa\Olcs\Api\Domain\Command\Licence\EndIrhpApplicationsAndPermits as Command;
use Dvsa\Olcs\Transfer\Query\IrhpPermit\GetListByLicence;
use Mockery as m;

/**
 * EndIrhpApplicationsAndPermitsTest
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class EndIrhpApplicationsAndPermitsTest extends CommandHandlerTestCase
{
    public function setUp(): void
    {
        $this->mockRepo('Licence', LicenceRepository::class);
        $this->mockRepo('IrhpPermit', IrhpPermitRepository::class);

        $this->sut = new CommandHandler();

        parent::setUp();
    }

    public function testHandleCommand()
    {
        $licenceId = 52;

        $irhpApplicationNotYetSubmittedId = 22;
        $irhpApplicationNotYetSubmitted = m::mock(IrhpApplication::class);
        $irhpApplicationNotYetSubmitted->shouldReceive('getId')
            ->withNoArgs()
            ->andReturn($irhpApplicationNotYetSubmittedId);
        $irhpApplicationNotYetSubmitted->shouldReceive('getStatus->getId')
            ->withNoArgs()
            ->andReturn(IrhpInterface::STATUS_NOT_YET_SUBMITTED);

        $irhpApplicationUnderConsiderationId = 45;
        $irhpApplicationUnderConsideration = m::mock(IrhpApplication::class);
        $irhpApplicationUnderConsideration->shouldReceive('getId')
            ->withNoArgs()
            ->andReturn($irhpApplicationUnderConsiderationId);
        $irhpApplicationUnderConsideration->shouldReceive('getStatus->getId')
            ->withNoArgs()
            ->andReturn(IrhpInterface::STATUS_UNDER_CONSIDERATION);

        $irhpApplicationAwaitingFeeId = 83;
        $irhpApplicationAwaitingFee = m::mock(IrhpApplication::class);
        $irhpApplicationAwaitingFee->shouldReceive('getId')
            ->withNoArgs()
            ->andReturn($irhpApplicationAwaitingFeeId);
        $irhpApplicationAwaitingFee->shouldReceive('getStatus->getId')
            ->withNoArgs()
            ->andReturn(IrhpInterface::STATUS_AWAITING_FEE);

        $ongoingIrhpApplications = new ArrayCollection(
            [$irhpApplicationNotYetSubmitted, $irhpApplicationUnderConsideration, $irhpApplicationAwaitingFee]
        );

        $licence = m::mock(Licence::class)->makePartial();
        $licence->shouldReceive('getOngoingIrhpApplications')
            ->withNoArgs()
            ->andReturn($ongoingIrhpApplications);

        $this->repoMap['Licence']->shouldReceive('fetchById')
            ->with($licenceId)
            ->andReturn($licence);

        $this->expectedSideEffect(
            CancelApplication::class,
            ['id' => $irhpApplicationNotYetSubmittedId],
            new Result()
        );

        $this->expectedSideEffect(
            Withdraw::class,
            [
                'id' => $irhpApplicationUnderConsiderationId,
                'reason' => WithdrawableInterface::WITHDRAWN_REASON_BY_USER
            ],
            new Result()
        );

        $this->expectedSideEffect(
            Withdraw::class,
            [
                'id' => $irhpApplicationAwaitingFeeId,
                'reason' => WithdrawableInterface::WITHDRAWN_REASON_BY_USER
            ],
            new Result()
        );

        $activeIrhpPermit1Id = 84;
        $activeIrhpPermit1 = m::mock(IrhpPermit::class);
        $activeIrhpPermit1->shouldReceive('getId')
            ->withNoArgs()
            ->andReturn($activeIrhpPermit1Id);

        $activeIrhpPermit2Id = 86;
        $activeIrhpPermit2 = m::mock(IrhpPermit::class);
        $activeIrhpPermit2->shouldReceive('getId')
            ->withNoArgs()
            ->andReturn($activeIrhpPermit2Id);

        $this->repoMap['IrhpPermit']->shouldReceive('fetchList')
            ->with(m::type(GetListByLicence::class), Query::HYDRATE_OBJECT)
            ->andReturnUsing(function ($query) use ($licenceId, $activeIrhpPermit1, $activeIrhpPermit2) {
                $this->assertEquals($licenceId, $query->getLicence());
                $this->assertTrue($query->getValidOnly());

                return [$activeIrhpPermit1, $activeIrhpPermit2];
            });

        $this->expectedSideEffect(
            Terminate::class,
            ['id' => $activeIrhpPermit1Id],
            new Result()
        );

        $this->expectedSideEffect(
            Terminate::class,
            ['id' => $activeIrhpPermit2Id],
            new Result()
        );

        $command = Command::create(['id' => $licenceId]);
        $result = $this->sut->handleCommand($command);

        $this->assertEquals(
            ['Cleared IRHP applications and permits for licence 52'],
            $result->getMessages()
        );
    }
}
