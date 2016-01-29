<?php

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\OrganisationPerson;

use Dvsa\Olcs\Api\Domain\CommandHandler\OrganisationPerson\DeleteList as CommandHandler;
use Dvsa\Olcs\Transfer\Command\OrganisationPerson\DeleteList as Command;
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
        $this->mockRepo('OrganisationPerson', \Dvsa\Olcs\Api\Domain\Repository\OrganisationPerson::class);

        parent::setUp();
    }

    public function testHandleCommand()
    {
        $command = Command::create(['ids' => [4323, 12373]]);

        $op1 = 'OL1';
        $op2 = 'OL2';

        $this->repoMap['OrganisationPerson']->shouldReceive('fetchById')->with(4323)->once()->andReturn($op1);
        $this->repoMap['OrganisationPerson']->shouldReceive('delete')->with($op1)->once()->andReturn();
        $this->repoMap['OrganisationPerson']->shouldReceive('fetchById')->with(12373)->once()->andReturn($op2);
        $this->repoMap['OrganisationPerson']->shouldReceive('delete')->with($op2)->once()->andReturn();

        $response = $this->sut->handleCommand($command);

        $this->assertSame(
            ['OrganisationPerson ID 4323 deleted', 'OrganisationPerson ID 12373 deleted'],
            $response->getMessages()
        );
    }
}
