<?php

/**
 * PopulateFromCompaniesHouseTest
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\OrganisationPerson;

use Dvsa\Olcs\Api\Domain\CommandHandler\OrganisationPerson\PopulateFromCompaniesHouse as CommandHandler;
use Dvsa\Olcs\Api\Domain\Repository\OrganisationPerson as OrganisationPersonRepo;
use Dvsa\Olcs\Api\Domain\Repository\Organisation as OrganisationRepo;
use Dvsa\Olcs\Api\Domain\Repository\Person as PersonRepo;
use Dvsa\Olcs\Transfer\Command\OrganisationPerson\PopulateFromCompaniesHouse as Command;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Mockery as m;

/**
 * PopulateFromCompaniesHouseTest
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class PopulateFromCompaniesHouseTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new CommandHandler();
        $this->mockRepo('OrganisationPerson', OrganisationPerson::class);
        $this->mockRepo('Organisation', Organisation::class);
        $this->mockRepo('Person', Person::class);

        $this->mockCh = m::mock(\Dvsa\Olcs\Api\Service\CompaniesHouseService::class);

        $this->mockedSmServices = [
            'CompaniesHouseService' => $this->mockCh
        ];

        parent::setUp();
    }

    protected function initReferences()
    {
        $this->refData = [
            'org_t_st', 'org_t_rc', 'title_mr'
        ];

        $this->references = [
        ];

        parent::initReferences();
    }

    public function testHandleCommandNotLplOrLtd()
    {
        $command = Command::create(['id' => 64]);

        $organisation = new \Dvsa\Olcs\Api\Entity\Organisation\Organisation();
        $organisation->setType($this->refData['org_t_st']);

        $this->repoMap['Organisation']->shouldReceive('fetchUsingId')->with($command)->once()
            ->andReturn($organisation);

        $response = $this->sut->handleCommand($command);

        $this->assertSame([], $response->getIds());
        $this->assertSame([], $response->getMessages());
    }

    public function testHandleCommandNoCompanyNumber()
    {
        $command = Command::create(['id' => 64]);

        $organisation = new \Dvsa\Olcs\Api\Entity\Organisation\Organisation();
        $organisation->setType($this->refData['org_t_rc']);

        $this->repoMap['Organisation']->shouldReceive('fetchUsingId')->with($command)->once()
            ->andReturn($organisation);

        $response = $this->sut->handleCommand($command);

        $this->assertSame([], $response->getIds());
        $this->assertSame([], $response->getMessages());
    }

    public function testHandleCommand()
    {
        $command = Command::create(['id' => 64]);

        $organisation = new \Dvsa\Olcs\Api\Entity\Organisation\Organisation();
        $organisation->setType($this->refData['org_t_rc']);
        $organisation->setCompanyOrLlpNo('87654321');

        $this->repoMap['Organisation']->shouldReceive('fetchUsingId')->with($command)->once()
            ->andReturn($organisation);

        $this->mockCh->shouldReceive('getList')->with(
            [
                'type' => 'currentCompanyOfficers',
                'value' => '87654321',
            ]
        )->once()->andReturn(
            [
                'Results' => [
                    ['forename' => 'Andy', 'familyName' => 'Adams', 'title' => 'sir', 'birthDate' => '2002-10-12'],
                    ['forename' => 'Barry', 'familyName' => 'Buns', 'title' => 'mr', 'birthDate' => '1965-02-04'],
                ]
            ]
        );

        $this->repoMap['Person']->shouldReceive('save')->once()->andReturnUsing(
            function (\Dvsa\Olcs\Api\Entity\Person\Person $person) {
                $this->assertSame('Andy', $person->getForename());
                $this->assertSame('Adams', $person->getFamilyName());
                $this->assertNull($person->getTitle());
                $this->assertEquals(new \DateTime('2002-10-12'), $person->getBirthDate());
                $person->setId(32);
            }
        );
        $this->repoMap['OrganisationPerson']->shouldReceive('save')->once()->andReturnUsing(
            function (\Dvsa\Olcs\Api\Entity\Organisation\OrganisationPerson $op) use ($organisation) {
                $this->assertSame($organisation, $op->getOrganisation());
                $this->assertSame(32, $op->getPerson()->getId());
            }
        );

        $this->repoMap['Person']->shouldReceive('save')->once()->andReturnUsing(
            function (\Dvsa\Olcs\Api\Entity\Person\Person $person) {
                $this->assertSame('Barry', $person->getForename());
                $this->assertSame('Buns', $person->getFamilyName());
                $this->assertSame($this->refData['title_mr'], $person->getTitle());
                $this->assertEquals(new \DateTime('1965-02-04'), $person->getBirthDate());
                $person->setId(326);
            }
        );
        $this->repoMap['OrganisationPerson']->shouldReceive('save')->once()->andReturnUsing(
            function (\Dvsa\Olcs\Api\Entity\Organisation\OrganisationPerson $op) use ($organisation) {
                $this->assertSame($organisation, $op->getOrganisation());
                $this->assertSame(326, $op->getPerson()->getId());
            }
        );

        $response = $this->sut->handleCommand($command);

        $this->assertSame([], $response->getIds());
        $this->assertSame(['Added 2 Person(s) to the Organisation'], $response->getMessages());
    }
}
