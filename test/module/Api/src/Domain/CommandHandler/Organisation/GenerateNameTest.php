<?php

/**
 * GenerateOrganisatioNnameTest
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Application;

use Dvsa\Olcs\Api\Domain\Command\Application\GrantGoods;
use Dvsa\Olcs\Api\Domain\Command\Application\GrantPsv;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\Application\GenerateName as CommandHandler;
use Dvsa\Olcs\Api\Domain\Exception\ValidationException;
use Dvsa\Olcs\Api\Entity\Application\Application as ApplicationEntity;
use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Dvsa\Olcs\Transfer\Command\Application\CreateSnapshot;
use Dvsa\Olcs\Transfer\Command\InspectionRequest\CreateFromGrant;
use Mockery as m;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Transfer\Command\Application\GenerateOrganisationName as Cmd;

/**
 * Grant Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class GenerateNameTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new CommandHandler();
        $this->mockRepo('Application', \Dvsa\Olcs\Api\Domain\Repository\Application::class);
        $this->mockRepo('Organisation', \Dvsa\Olcs\Api\Domain\Repository\Organisation::class);

        parent::setUp();
    }

    protected function initReferences()
    {
        $this->refData = ['org_t_llp', 'org_t_st', 'org_t_p', 'org_t_rc'];

        $this->references = [
            \Dvsa\Olcs\Api\Entity\Person\Person::class => [
                234 => m::mock(\Dvsa\Olcs\Api\Entity\Person\Person::class)
            ]
        ];

        parent::initReferences();
    }

    public function testHandleCommandVaritaion()
    {
        $command = Cmd::create(['id' => 234]);

        $organisation = new \Dvsa\Olcs\Api\Entity\Organisation\Organisation();
        $licence = new Licence($organisation, new \Dvsa\Olcs\Api\Entity\System\RefData());
        $application = new ApplicationEntity($licence, new \Dvsa\Olcs\Api\Entity\System\RefData(), 1);

        $this->repoMap['Application']->shouldReceive('fetchUsingId')->with($command)->once()->andReturn($application);

        $this->setExpectedException(ValidationException::class);

        $this->sut->handleCommand($command);
    }


    public function testHandleCommandLlp()
    {
        $command = Cmd::create(['id' => 234]);

        $organisation = new \Dvsa\Olcs\Api\Entity\Organisation\Organisation();
        $organisation->setType($this->refData['org_t_llp']);
        $licence = new Licence($organisation, new \Dvsa\Olcs\Api\Entity\System\RefData());
        $application = new ApplicationEntity($licence, new \Dvsa\Olcs\Api\Entity\System\RefData(), 0);

        $this->repoMap['Application']->shouldReceive('fetchUsingId')->with($command)->once()->andReturn($application);

        $this->setExpectedException(ValidationException::class);

        $this->sut->handleCommand($command);
    }

    public function testHandleCommandRc()
    {
        $command = Cmd::create(['id' => 234]);

        $organisation = new \Dvsa\Olcs\Api\Entity\Organisation\Organisation();
        $organisation->setType($this->refData['org_t_rc']);
        $licence = new Licence($organisation, new \Dvsa\Olcs\Api\Entity\System\RefData());
        $application = new ApplicationEntity($licence, new \Dvsa\Olcs\Api\Entity\System\RefData(), 0);

        $this->repoMap['Application']->shouldReceive('fetchUsingId')->with($command)->once()->andReturn($application);

        $this->setExpectedException(ValidationException::class);

        $this->sut->handleCommand($command);
    }

    public function testHandleCommandStNoPerson()
    {
        $command = Cmd::create(['id' => 234]);

        $organisation = new \Dvsa\Olcs\Api\Entity\Organisation\Organisation();
        $organisation->setType($this->refData['org_t_st']);
        $licence = new Licence($organisation, new \Dvsa\Olcs\Api\Entity\System\RefData());
        $application = new ApplicationEntity($licence, new \Dvsa\Olcs\Api\Entity\System\RefData(), 0);

        $this->repoMap['Application']->shouldReceive('fetchUsingId')->with($command)->once()->andReturn($application);

        $response = $this->sut->handleCommand($command);

        $this->assertSame(['Unable to generate name'], $response->getMessages());
    }

    public function testHandleCommandSt()
    {
        $command = Cmd::create(['id' => 234]);

        $organisation = new \Dvsa\Olcs\Api\Entity\Organisation\Organisation();
        $organisation->setType($this->refData['org_t_st']);
        $licence = new Licence($organisation, new \Dvsa\Olcs\Api\Entity\System\RefData());
        $application = new ApplicationEntity($licence, new \Dvsa\Olcs\Api\Entity\System\RefData(), 0);
        $person = new \Dvsa\Olcs\Api\Entity\Person\Person();
        $person->setForename('Fred');
        $person->setFamilyName('Smith');
        $organisationPerson = new \Dvsa\Olcs\Api\Entity\Organisation\OrganisationPerson();
        $organisationPerson->setPerson($person);
        $organisation->getOrganisationPersons()->add($organisationPerson);

        $this->repoMap['Application']->shouldReceive('fetchUsingId')->with($command)->once()->andReturn($application);

        $this->repoMap['Organisation']->shouldReceive('save')->once()->andReturnUsing(
            function (\Dvsa\Olcs\Api\Entity\Organisation\Organisation $savedOrganisation) {
                $this->assertSame('Fred Smith', $savedOrganisation->getName());
            }
        );

        $response = $this->sut->handleCommand($command);

        $this->assertSame(['Name succesfully generated'], $response->getMessages());
    }

    public function testHandleCommandPartnershipNoPeople()
    {
        $command = Cmd::create(['id' => 234]);

        $organisation = new \Dvsa\Olcs\Api\Entity\Organisation\Organisation();
        $organisation->setType($this->refData['org_t_p']);
        $licence = new Licence($organisation, new \Dvsa\Olcs\Api\Entity\System\RefData());
        $application = new ApplicationEntity($licence, new \Dvsa\Olcs\Api\Entity\System\RefData(), 0);

        $this->repoMap['Application']->shouldReceive('fetchUsingId')->with($command)->once()->andReturn($application);

        $response = $this->sut->handleCommand($command);

        $this->assertSame(['Unable to generate name'], $response->getMessages());
    }

    public function testHandleCommandPartnershipOnePerson()
    {
        $command = Cmd::create(['id' => 234]);

        $organisation = new \Dvsa\Olcs\Api\Entity\Organisation\Organisation();
        $organisation->setType($this->refData['org_t_p']);
        $licence = new Licence($organisation, new \Dvsa\Olcs\Api\Entity\System\RefData());
        $application = new ApplicationEntity($licence, new \Dvsa\Olcs\Api\Entity\System\RefData(), 0);
        $person = new \Dvsa\Olcs\Api\Entity\Person\Person();
        $person->setForename('Fred');
        $person->setFamilyName('Smith');
        $organisationPerson = new \Dvsa\Olcs\Api\Entity\Organisation\OrganisationPerson();
        $organisationPerson->setPerson($person);
        $organisation->getOrganisationPersons()->add($organisationPerson);

        $this->repoMap['Application']->shouldReceive('fetchUsingId')->with($command)->once()->andReturn($application);

        $this->repoMap['Organisation']->shouldReceive('save')->once()->andReturnUsing(
            function (\Dvsa\Olcs\Api\Entity\Organisation\Organisation $savedOrganisation) {
                $this->assertSame('Fred Smith', $savedOrganisation->getName());
            }
        );

        $response = $this->sut->handleCommand($command);

        $this->assertSame(['Name succesfully generated'], $response->getMessages());
    }

    public function testHandleCommandPartnershipTwoPeople()
    {
        $command = Cmd::create(['id' => 234]);

        $organisation = new \Dvsa\Olcs\Api\Entity\Organisation\Organisation();
        $organisation->setType($this->refData['org_t_p']);
        $licence = new Licence($organisation, new \Dvsa\Olcs\Api\Entity\System\RefData());
        $application = new ApplicationEntity($licence, new \Dvsa\Olcs\Api\Entity\System\RefData(), 0);

        $person = (new \Dvsa\Olcs\Api\Entity\Person\Person())->setForename('Fred')->setFamilyName('Smith');
        $organisationPerson = (new \Dvsa\Olcs\Api\Entity\Organisation\OrganisationPerson())->setPerson($person);
        $organisation->getOrganisationPersons()->add($organisationPerson);
        $person2 = (new \Dvsa\Olcs\Api\Entity\Person\Person())->setForename('Jon')->setFamilyName('Jones');
        $organisationPerson2 = (new \Dvsa\Olcs\Api\Entity\Organisation\OrganisationPerson())->setPerson($person2);
        $organisation->getOrganisationPersons()->add($organisationPerson2);

        $this->repoMap['Application']->shouldReceive('fetchUsingId')->with($command)->once()->andReturn($application);

        $this->repoMap['Organisation']->shouldReceive('save')->once()->andReturnUsing(
            function (\Dvsa\Olcs\Api\Entity\Organisation\Organisation $savedOrganisation) {
                $this->assertSame('Fred Smith & Jon Jones', $savedOrganisation->getName());
            }
        );

        $response = $this->sut->handleCommand($command);

        $this->assertSame(['Name succesfully generated'], $response->getMessages());
    }

    public function testHandleCommandPartnershipThreePeople()
    {
        $command = Cmd::create(['id' => 234]);

        $organisation = new \Dvsa\Olcs\Api\Entity\Organisation\Organisation();
        $organisation->setType($this->refData['org_t_p']);
        $licence = new Licence($organisation, new \Dvsa\Olcs\Api\Entity\System\RefData());
        $application = new ApplicationEntity($licence, new \Dvsa\Olcs\Api\Entity\System\RefData(), 0);

        $person = (new \Dvsa\Olcs\Api\Entity\Person\Person())->setForename('Fred')->setFamilyName('Smith');
        $organisationPerson = (new \Dvsa\Olcs\Api\Entity\Organisation\OrganisationPerson())->setPerson($person);
        $organisation->getOrganisationPersons()->add($organisationPerson);
        $person2 = (new \Dvsa\Olcs\Api\Entity\Person\Person())->setForename('Jon')->setFamilyName('Jones');
        $organisationPerson2 = (new \Dvsa\Olcs\Api\Entity\Organisation\OrganisationPerson())->setPerson($person2);
        $organisation->getOrganisationPersons()->add($organisationPerson2);
        $person3 = (new \Dvsa\Olcs\Api\Entity\Person\Person())->setForename('Mary')->setFamilyName('Moon');
        $organisationPerson3 = (new \Dvsa\Olcs\Api\Entity\Organisation\OrganisationPerson())->setPerson($person3);
        $organisation->getOrganisationPersons()->add($organisationPerson3);

        $this->repoMap['Application']->shouldReceive('fetchUsingId')->with($command)->once()->andReturn($application);

        $this->repoMap['Organisation']->shouldReceive('save')->once()->andReturnUsing(
            function (\Dvsa\Olcs\Api\Entity\Organisation\Organisation $savedOrganisation) {
                $this->assertSame('Fred Smith & Partners', $savedOrganisation->getName());
            }
        );

        $response = $this->sut->handleCommand($command);

        $this->assertSame(['Name succesfully generated'], $response->getMessages());
    }
}
