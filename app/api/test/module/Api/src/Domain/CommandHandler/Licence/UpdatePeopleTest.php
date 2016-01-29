<?php

/**
 * UpdatePeopleTest
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Licence;

use Dvsa\Olcs\Api\Domain\Repository\Person as  PersonRepo;
use Dvsa\Olcs\Api\Domain\CommandHandler\Licence\UpdatePeople as CommandHandler;
use Dvsa\Olcs\Transfer\Command\Licence\UpdatePeople as Command;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;

/**
 * UpdatePeopleTest
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class UpdatePeopleTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new CommandHandler();
        $this->mockRepo('Person', PersonRepo::class);

        parent::setUp();
    }

    protected function initReferences()
    {
        $this->refData = [
            'title_mr', 'org_t_llp', 'org_t_p'
        ];

        $this->references = [];

        parent::initReferences();
    }

    public function testHandleCommand()
    {
        $data = [
            'id' => 52,
            'person' => 79,
            'version' => 122,
            'forename' => 'Foo',
            'familyName' => 'Bar',
            'title' => 'title_mr',
            'birthDate' => '1966-05-21',
            'otherName' => 'Jerry'
        ];
        $command = Command::create($data);

        $organisation = new \Dvsa\Olcs\Api\Entity\Organisation\Organisation();
        $organisation->setType($this->refData['org_t_p']);

        $person = new \Dvsa\Olcs\Api\Entity\Person\Person();
        $person->setId(79);
        $person->setForename('FRED');

        $this->repoMap['Person']->shouldReceive('fetchById')->with(79, \Doctrine\ORM\Query::HYDRATE_OBJECT, 122)
            ->once()->andReturn($person);

        $this->repoMap['Person']->shouldReceive('save')->once()->andReturnUsing(
            function (\Dvsa\Olcs\Api\Entity\Person\Person $person) use ($data) {
                $this->assertSame($data['forename'], $person->getForename());
                $this->assertSame($data['familyName'], $person->getFamilyName());
                $this->assertSame($this->refData[$data['title']], $person->getTitle());
                $this->assertEquals(new \DateTime($data['birthDate']), $person->getBirthDate());
                $this->assertSame($data['otherName'], $person->getOtherName());
            }
        );

        $response = $this->sut->handleCommand($command);

        $this->assertSame(['Person ID 79 updated'], $response->getMessages());
        $this->assertSame(['person' => 79], $response->getIds());
    }
}
