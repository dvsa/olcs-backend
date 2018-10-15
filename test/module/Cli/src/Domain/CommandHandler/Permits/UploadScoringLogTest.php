<?php

namespace Dvsa\OlcsTest\Cli\Domain\CommandHandler\Permits;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Cli\Domain\Command\Permits\UploadScoringLog
    as UploadScoringLogCommand;
use Dvsa\Olcs\Cli\Domain\CommandHandler\Permits\UploadScoringLog
    as UploadScoringLogCommandHandler;
use Mockery as m;

/**
 * Upload Scoring Log test
 *
 * @author Jason de jonge <jason.de-jonge@capgemini.com>
 */
class UploadScoringLogTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new UploadScoringLogCommandHandler();

        parent::setUp();
    }

    /**
     * tests handleCommand
     */
    public function testHandleCommand()
    {
        $logContent = 'test';


        $this->sut->handleCommand(
            UploadScoringLogCommand::create(['logContent' => $logContent])
        );
    }
}
