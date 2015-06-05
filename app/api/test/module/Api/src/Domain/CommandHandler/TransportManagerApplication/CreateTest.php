<?php

/**
 * CreateTest
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Fee;

use Dvsa\Olcs\Api\Domain\CommandHandler\TransportManagerApplication\Create as CommandHandler;
use Dvsa\Olcs\Transfer\Command\TransportManagerApplication\Create as Command;

use Mockery as m;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;

/**
 * CreateTest
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class CreateTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new CommandHandler();
        $this->mockRepo('TransportManagerApplication', \Dvsa\Olcs\Api\Domain\Repository\TransportManagerApplication::class);
        $this->mockRepo('TransportManager', \Dvsa\Olcs\Api\Domain\Repository\TransportManager::class);
        $this->mockRepo('User', \Dvsa\Olcs\Api\Domain\Repository\User::class);

        parent::setUp();
    }

    protected function initReferences()
    {
//        $this->refData = [
//            FeeEntity::STATUS_CANCELLED
//        ];

        parent::initReferences();
    }

    public function testHandleCommand()
    {
        $command = Command::create(['id' => 863]);

        $this->markTestSkipped("@todo");
    }
}
