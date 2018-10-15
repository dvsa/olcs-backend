<?php

namespace Dvsa\OlcsTest\Cli\Domain\CommandHandler\Permits;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Cli\Domain\Command\Permits\UploadScoringLog
    as UploadScoringResultCommand;
use Dvsa\Olcs\Cli\Domain\CommandHandler\Permits\UploadScoringResult
    as UploadScoringResultCommandHandler;
use Mockery as m;

/**
 * Upload Scoring Result test
 *
 * @author Jason de jonge <jason.de-jonge@capgemini.com>
 */
class UpliadScoringResultTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new UploadScoringResultCommandHandler();

        parent::setUp();
    }

    /**
     * tests handleCommand
     */
    public function testHandleCommand()
    {
        $csvContent = 'test';


        $this->sut->handleCommand(
            UploadScoringResultCommand::create(['csvContent' => $logContent])
        );
    }
}
