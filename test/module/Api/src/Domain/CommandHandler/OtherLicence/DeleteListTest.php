<?php

/**
 * DeleteListTest
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\OtherLicence;

use Dvsa\Olcs\Api\Domain\CommandHandler\OtherLicence\DeleteList as CommandHandler;
use Dvsa\Olcs\Api\Domain\Repository\OtherLicence;
use Dvsa\Olcs\Transfer\Command\OtherLicence\DeleteList as Command;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;

/**
 * DeleteListTest
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class DeleteListTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new CommandHandler();
        $this->mockRepo('OtherLicence', OtherLicence::class);

        parent::setUp();
    }

    public function testHandleCommand()
    {
        $command = Command::create(['ids' => [4323, 12373]]);

        $ol1 = 'OL1';
        $ol2 = 'OL2';

        $this->repoMap['OtherLicence']->shouldReceive('fetchById')->with(4323)->once()->andReturn($ol1);
        $this->repoMap['OtherLicence']->shouldReceive('delete')->with($ol1)->once()->andReturn();
        $this->repoMap['OtherLicence']->shouldReceive('fetchById')->with(12373)->once()->andReturn($ol2);
        $this->repoMap['OtherLicence']->shouldReceive('delete')->with($ol2)->once()->andReturn();

        $response = $this->sut->handleCommand($command);

        $this->assertSame(
            ['Other Licence ID 4323 deleted', 'Other Licence ID 12373 deleted'],
            $response->getMessages()
        );
    }
}
