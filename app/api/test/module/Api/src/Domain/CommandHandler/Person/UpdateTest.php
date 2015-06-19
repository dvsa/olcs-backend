<?php

/**
 * UpdateTest
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Fee;

use Dvsa\Olcs\Api\Domain\CommandHandler\Person\Update as CommandHandler;
use Dvsa\Olcs\Api\Domain\Repository\Person;
use Dvsa\Olcs\Api\Entity\Person\Person as PersonEntity;
use Dvsa\Olcs\Transfer\Command\Person\Update as Command;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;

/**
 * UpdateTest
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class UpdateTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new CommandHandler();
        $this->mockRepo('Person', Person::class);

        parent::setUp();
    }

    public function testHandleCommand()
    {
        $command = Command::create(['id' => 863, 'dob' => '1966-02-29']);

        $person = new PersonEntity();
        $person->setId(863);

        $this->repoMap['Person']
            ->shouldReceive('fetchUsingId')->with($command)->once()->andReturn($person)
            ->shouldReceive('save')->with($person)->once()->andReturn();

        $response = $this->sut->handleCommand($command);

        $this->assertSame(['person' => 863], $response->getIds());
        $this->assertSame(['Person ID 863 Updated.'], $response->getMessages());
        $this->assertEquals(new \DateTime('1966-02-29'), $person->getBirthDate());
    }
}
