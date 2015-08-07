<?php

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\OrganisationPerson;

use Dvsa\Olcs\Api\Domain\CommandHandler\OrganisationPerson\Update as CommandHandler;
use Dvsa\Olcs\Transfer\Command\OrganisationPerson\Update as Command;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Mockery as m;
use Dvsa\Olcs\Api\Entity\Organisation\Organisation;
use Dvsa\Olcs\Api\Entity\Person\Person;
use Dvsa\Olcs\Api\Entity\Organisation\OrganisationPerson;

/**
 * OrganisationPersonTest
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class UpdateTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new CommandHandler();
        $this->mockRepo('OrganisationPerson', \Dvsa\Olcs\Api\Domain\Repository\OrganisationPerson::class);
        $this->mockRepo('Person', \Dvsa\Olcs\Api\Domain\Repository\Person::class);

        parent::setUp();
    }

    protected function initReferences()
    {
        $this->refData = ['TITLE'];

        $this->references = [
            Organisation::class => [
                724 => m::mock(Organisation::class)
            ]
        ];

        parent::initReferences();
    }

    public function testHandleCommand()
    {
        $command = Command::create(
            [
                'id' => 724,
                'version' => 33,
                'position' => 'POSITION',
                'person' => [
                    'title' => 'TITLE',
                    'forename' => 'FORENAME',
                    'familyName' => 'FAMILY_NAME',
                    'otherName' => 'OTHER_NAME',
                    'birthDate' => '2015-07-24',
                ],
            ]
        );

        $person = new Person();

        $organisationPerson = new OrganisationPerson();
        $organisationPerson->setId(724);
        $organisationPerson->setPerson($person);

        $this->repoMap['OrganisationPerson']->shouldReceive('fetchUsingId')
            ->with($command, \Doctrine\ORM\Query::HYDRATE_OBJECT, 33)->once()->andReturn($organisationPerson);

        $this->repoMap['OrganisationPerson']
            ->shouldReceive('save')->once()->andReturnUsing(
                function (OrganisationPerson $saveOrganisationPerson) {
                    $this->assertSame('POSITION', $saveOrganisationPerson->getPosition());
                }
            );

        $this->repoMap['Person']
            ->shouldReceive('save')->once()->andReturnUsing(
                function (Person $person) {
                    $this->assertSame($this->refData['TITLE'], $person->getTitle());
                    $this->assertSame('FORENAME', $person->getForename());
                    $this->assertSame('FAMILY_NAME', $person->getFamilyName());
                    $this->assertSame('OTHER_NAME', $person->getOtherName());
                    $this->assertEquals(new \DateTime('2015-07-24'), $person->getBirthDate());
                }
            );

        $response = $this->sut->handleCommand($command);

        $this->assertSame(['organisationPerson' => 724], $response->getIds());
        $this->assertSame(['OrganisationPerson updated'], $response->getMessages());
    }
}
