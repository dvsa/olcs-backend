<?php

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\OrganisationPerson;

use Dvsa\Olcs\Api\Domain\CommandHandler\OrganisationPerson\Create as CommandHandler;
use Dvsa\Olcs\Transfer\Command\OrganisationPerson\Create as Command;
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
class CreateTest extends CommandHandlerTestCase
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
                'organisation' => 724,
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

        $this->repoMap['Person']
            ->shouldReceive('save')->once()->andReturnUsing(
                function (Person $person) {
                    $person->setId(648);
                    $this->assertSame($this->refData['TITLE'], $person->getTitle());
                    $this->assertSame('FORENAME', $person->getForename());
                    $this->assertSame('FAMILY_NAME', $person->getFamilyName());
                    $this->assertSame('OTHER_NAME', $person->getOtherName());
                    $this->assertEquals(new \DateTime('2015-07-24'), $person->getBirthDate());
                }
            );

        $this->repoMap['OrganisationPerson']
            ->shouldReceive('save')->once()->andReturnUsing(
                function (OrganisationPerson $organisationPerson) {
                    $organisationPerson->setId(72);
                    $this->assertSame('POSITION', $organisationPerson->getPosition());
                    $this->assertSame(
                        $this->references[Organisation::class][724],
                        $organisationPerson->getOrganisation()
                    );
                    $this->assertSame(648, $organisationPerson->getPerson()->getId());
                }
            );

        $response = $this->sut->handleCommand($command);

        $this->assertSame(['organisationPerson' => 72, 'person' => 648], $response->getIds());
        $this->assertSame(['Organisation Person ID 72 created'], $response->getMessages());
    }
}
