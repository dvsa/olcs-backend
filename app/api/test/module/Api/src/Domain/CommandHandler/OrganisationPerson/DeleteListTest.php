<?php

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\OrganisationPerson;

use Dvsa\Olcs\Api\Domain\CommandHandler\OrganisationPerson\DeleteList as CommandHandler;
use Dvsa\Olcs\Transfer\Command\OrganisationPerson\DeleteList as Command;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Mockery as m;

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
        $this->mockRepo('Person', \Dvsa\Olcs\Api\Domain\Repository\Person::class);
        $this->mockRepo(
            'ApplicationOrganisationPerson',
            \Dvsa\Olcs\Api\Domain\Repository\ApplicationOrganisationPerson::class
        );

        parent::setUp();
    }

    public function testHandleCommand()
    {
        $command = Command::create(['ids' => [4323, 12373]]);

        $person1 = (new \Dvsa\Olcs\Api\Entity\Person\Person())->setId(4);
        $op1 = m::mock();
        $op1->shouldReceive('getPerson')->with()->atLeast(1)->andReturn($person1);

        $person2 = (new \Dvsa\Olcs\Api\Entity\Person\Person())->setId(5);
        $op2 = m::mock();
        $op2->shouldReceive('getPerson')->with()->atLeast(1)->andReturn($person2);

        $this->repoMap['OrganisationPerson']->shouldReceive('fetchById')->with(4323)->once()->andReturn($op1);
        $this->repoMap['OrganisationPerson']->shouldReceive('delete')->with($op1)->once()->andReturn();
        $this->repoMap['OrganisationPerson']->shouldReceive('fetchListForPerson')->with(4)->once()->andReturn([]);
        $this->repoMap['Person']->shouldReceive('delete')->with($person1)->once();
        $this->repoMap['ApplicationOrganisationPerson']->shouldReceive('deleteForPerson')->with($person1)->once();

        $this->repoMap['OrganisationPerson']->shouldReceive('fetchById')->with(12373)->once()->andReturn($op2);
        $this->repoMap['OrganisationPerson']->shouldReceive('delete')->with($op2)->once()->andReturn();
        $this->repoMap['OrganisationPerson']->shouldReceive('fetchListForPerson')->with(5)->once()->andReturn(['FOO']);

        $response = $this->sut->handleCommand($command);

        $this->assertSame(
            [
                'OrganisationPerson ID 4323 deleted',
                'Person ID 4 deleted',
                'OrganisationPerson ID 12373 deleted'
            ],
            $response->getMessages()
        );
    }
}
