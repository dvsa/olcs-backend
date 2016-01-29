<?php

/**
 * CreatePeopleTest
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Licence;

use Dvsa\Olcs\Api\Domain\Repository\Licence as  LicenceRepo;
use Dvsa\Olcs\Api\Domain\Repository\OrganisationPerson as  OrganisationPersonRepo;
use Dvsa\Olcs\Api\Domain\Repository\Person as  PersonRepo;
use Dvsa\Olcs\Api\Domain\CommandHandler\Licence\CreatePeople as CommandHandler;
use Dvsa\Olcs\Api\Entity\Licence\Licence as LicenceEntity;
use Dvsa\Olcs\Transfer\Command\Licence\CreatePeople as Command;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;

/**
 * CreatePeopleTest
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class CreatePeopleTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new CommandHandler();
        $this->mockRepo('Licence', LicenceRepo::class);
        $this->mockRepo('OrganisationPerson', OrganisationPersonRepo::class);
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
            'forename' => 'Foo',
            'familyName' => 'Bar',
            'title' => 'title_mr',
            'birthDate' => '1966-05-21',
            'otherName' => 'Jerry'
        ];
        $command = Command::create($data);

        $organisation = new \Dvsa\Olcs\Api\Entity\Organisation\Organisation();
        $organisation->setType($this->refData['org_t_p']);
        $licence = new LicenceEntity($organisation, new \Dvsa\Olcs\Api\Entity\System\RefData());

        $this->repoMap['Licence']->shouldReceive('fetchUsingId')->with($command)->once()->andReturn($licence);

        $this->repoMap['Person']->shouldReceive('save')->once()->andReturnUsing(
            function (\Dvsa\Olcs\Api\Entity\Person\Person $person) use ($data) {
                $this->assertSame($data['forename'], $person->getForename());
                $this->assertSame($data['familyName'], $person->getFamilyName());
                $this->assertSame($this->refData[$data['title']], $person->getTitle());
                $this->assertEquals(new \DateTime($data['birthDate']), $person->getBirthDate());
                $this->assertSame($data['otherName'], $person->getOtherName());
                $person->setId(753);
            }
        );

        $this->repoMap['OrganisationPerson']->shouldReceive('save')->once()->andReturnUsing(
            function (\Dvsa\Olcs\Api\Entity\Organisation\OrganisationPerson $op) use ($data, $organisation) {
                $this->assertSame($organisation, $op->getOrganisation());
                $this->assertSame(753, $op->getPerson()->getId());
                $op->setId(43);
            }
        );

        $response = $this->sut->handleCommand($command);

        $this->assertSame(['OrganisationPerson created'], $response->getMessages());
        $this->assertSame(['organisationPerson' => 43, 'person' => 753], $response->getIds());
    }
}
