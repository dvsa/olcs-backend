<?php

namespace Dvsa\OlcsTest\Cli\Domain\CommandHandler\Permits;

use Dvsa\Olcs\Cli\Domain\Command\Permits\MarkExpiredPermits as MarkExpiredPermitsCommand;
use Dvsa\Olcs\Cli\Domain\CommandHandler\Permits\MarkExpiredPermits;
use Dvsa\Olcs\Api\Domain\Repository\IrhpPermit as IrhpPermitRepo;
use Dvsa\Olcs\Api\Domain\Repository\IrhpApplication as IrhpApplicationRepo;
use Dvsa\Olcs\Api\Domain\Repository\EcmtPermitApplication as EcmtPermitApplicationRepo;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;

/**
 * MarkExpiredPermits test
 */
class MarkExpiredPermitsTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new MarkExpiredPermits();
        $this->mockRepo('IrhpPermit', IrhpPermitRepo::class);
        $this->mockRepo('IrhpApplication', IrhpApplicationRepo::class);
        $this->mockRepo('EcmtPermitApplication', EcmtPermitApplicationRepo::class);

        parent::setUp();
    }

    public function testHandleCommand()
    {
        $this->repoMap['IrhpPermit']->shouldReceive('markAsExpired')->withNoArgs()->once();
        $this->repoMap['IrhpApplication']->shouldReceive('markAsExpired')->withNoArgs()->once();
        $this->repoMap['EcmtPermitApplication']->shouldReceive('markAsExpired')->withNoArgs()->once();

        $result = $this->sut->handleCommand(MarkExpiredPermitsCommand::create([]));

        $this->assertEquals(
            ['Expired permits have been marked'],
            $result->getMessages()
        );
    }
}
