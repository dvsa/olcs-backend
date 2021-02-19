<?php

/**
 * EndIrhpApplicationsAndPermitsTest
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Licence;

use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Api\Domain\CommandHandler\Licence\EndIrhpApplicationsAndPermits as CommandHandler;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\Command\Licence\EndIrhpApplicationsAndPermits as Command;
use Dvsa\Olcs\Api\Domain\Command\Licence\EndIrhpApplications;
use Dvsa\Olcs\Api\Domain\Command\Licence\EndIrhpPermits;
use Dvsa\Olcs\Api\Entity\WithdrawableInterface;

/**
 * EndIrhpApplicationsAndPermitsTest
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class EndIrhpApplicationsAndPermitsTest extends CommandHandlerTestCase
{
    public function setUp(): void
    {
        $this->sut = new CommandHandler();

        parent::setUp();
    }

    /**
     * @dataProvider dpHandleCommand
     */
    public function testHandleCommand($withdrawReason, $context)
    {
        $licenceId = 52;

        $endIrhpApplicationsCmdMessage = 'Cleared IRHP applications for licence 52';

        $this->expectedSideEffect(
            EndIrhpApplications::class,
            [
                'id' => $licenceId,
                'reason' => $withdrawReason,
            ],
            (new Result())->addMessage($endIrhpApplicationsCmdMessage)
        );

        $endIrhpPermitsCmdMessage = 'Cleared IRHP permits for licence 52';

        $this->expectedSideEffect(
            EndIrhpPermits::class,
            [
                'id' => $licenceId,
                'context' => $context,
            ],
            (new Result())->addMessage($endIrhpPermitsCmdMessage)
        );

        $command = Command::create(
            [
                'id' => $licenceId,
                'reason' => $withdrawReason,
                'context' => $context,
            ]
        );

        $result = $this->sut->handleCommand($command);

        $this->assertEquals(
            [
                $endIrhpApplicationsCmdMessage,
                $endIrhpPermitsCmdMessage,
            ],
            $result->getMessages()
        );
    }

    public function dpHandleCommand()
    {
        return [
            [
                WithdrawableInterface::WITHDRAWN_REASON_BY_USER,
                Command::CONTEXT_SURRENDER,
            ],
            [
                WithdrawableInterface::WITHDRAWN_REASON_BY_USER,
                Command::CONTEXT_REVOKE,
            ],
            [
                WithdrawableInterface::WITHDRAWN_REASON_BY_USER,
                Command::CONTEXT_CNS,
            ],
            [
                WithdrawableInterface::WITHDRAWN_REASON_PERMITS_REVOKED,
                Command::CONTEXT_SURRENDER,
            ],
            [
                WithdrawableInterface::WITHDRAWN_REASON_PERMITS_REVOKED,
                Command::CONTEXT_REVOKE,
            ],
            [
                WithdrawableInterface::WITHDRAWN_REASON_PERMITS_REVOKED,
                Command::CONTEXT_CNS,
            ],
        ];
    }
}
