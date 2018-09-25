<?php

namespace Dvsa\OlcsTest\Cli\Domain\CommandHandler\Permits;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Api\Domain\Repository\IrhpCandidatePermit as IrhpCandidatePermitRepo;
use Dvsa\Olcs\Cli\Domain\Command\Permits\ResetScoring as ResetScoringCommand;
use Dvsa\Olcs\Cli\Domain\CommandHandler\Permits\ResetScoring as ResetScoringHandler;
use Mockery as m;

/**
 * Reset scoring test
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class ResetScoringTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new ResetScoringHandler();
        $this->mockRepo('IrhpCandidatePermit', IrhpCandidatePermitRepo::class);

        parent::setUp();
    }

    /**
     * tests handleCommand
     */
    public function testHandleCommand()
    {
        $stockId = 8;

        $this->repoMap['IrhpCandidatePermit']->shouldReceive('resetScoring')
            ->with($stockId)
            ->once();

        $this->sut->handleCommand(
            ResetScoringCommand::create(['stockId' => $stockId])
        );
    }
}
