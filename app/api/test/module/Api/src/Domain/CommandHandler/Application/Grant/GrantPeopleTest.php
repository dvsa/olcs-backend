<?php

/**
 * Grant People Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Application\Grant;

use Doctrine\Common\Collections\ArrayCollection;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\Application\Grant\GrantPeople;
use Dvsa\Olcs\Api\Entity\Application\Application as ApplicationEntity;
use Dvsa\Olcs\Api\Entity\Application\ApplicationOrganisationPerson;
use Dvsa\Olcs\Api\Entity\Organisation\Organisation;
use Dvsa\Olcs\Api\Entity\Organisation\OrganisationPerson;
use Dvsa\Olcs\Api\Entity\Person\Person;
use Mockery as m;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Api\Domain\Command\Application\Grant\GrantPeople as GrantPeopleCmd;
use ZfcRbac\Service\AuthorizationService;

/**
 * Grant People Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class GrantPeopleTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new GrantPeople();
        $this->mockRepo('Application', \Dvsa\Olcs\Api\Domain\Repository\Application::class);
        $this->mockRepo('Person', \Dvsa\Olcs\Api\Domain\Repository\Person::class);
        $this->mockRepo('OrganisationPerson', \Dvsa\Olcs\Api\Domain\Repository\OrganisationPerson::class);

        parent::setUp();
    }

    protected function initReferences()
    {
        $this->refData = [];

        $this->references = [];

        parent::initReferences();
    }

    public function testHandleCommandWithoutRecords()
    {
        $data = [
            'id' => 111
        ];

        $command = GrantPeopleCmd::create($data);

        /** @var ApplicationEntity $application */
        $application = m::mock(ApplicationEntity::class)->makePartial();
        $application->setApplicationOrganisationPersons(new ArrayCollection());

        $this->repoMap['Application']->shouldReceive('fetchUsingId')
            ->with($command)
            ->andReturn($application);

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [],
            'messages' => []
        ];

        $this->assertEquals($expected, $result->toArray());
    }

    public function testHandleCommandWithoutAdd()
    {
        $data = [
            'id' => 111
        ];

        $command = GrantPeopleCmd::create($data);

        $person1 = new Person();

        /** @var ApplicationOrganisationPerson $aop1 */
        $aop1 = m::mock(ApplicationOrganisationPerson::class)->makePartial();
        $aop1->setAction('A');
        $aop1->setPerson($person1);

        $aops = new ArrayCollection();
        $aops->add($aop1);

        /** @var ApplicationEntity $application */
        $application = m::mock(ApplicationEntity::class)->makePartial();
        $application->setApplicationOrganisationPersons($aops);

        $this->repoMap['Application']->shouldReceive('fetchUsingId')
            ->with($command)
            ->andReturn($application);

        $savedPerson = null;

        $this->repoMap['Person']->shouldReceive('save')
            ->once()
            ->andReturnUsing(
                function (Person $person) use ($person1, &$savedPerson) {
                    $this->assertNotSame($person1, $person);
                    $savedPerson = $person;
                }
            );

        $this->repoMap['OrganisationPerson']->shouldReceive('save')
            ->once()
            ->andReturnUsing(
                function (OrganisationPerson $orgPerson) use ($aop1) {
                    $this->assertNotSame($aop1, $orgPerson);
                }
            );

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [],
            'messages' => [
                'Organisation person records have been copied'
            ]
        ];

        $this->assertEquals($expected, $result->toArray());
    }

    public function testHandleCommandWithoutUpdate()
    {
        $data = [
            'id' => 111
        ];

        $command = GrantPeopleCmd::create($data);

        $person1 = new Person();
        $person2 = new Person();

        $org1 = m::mock(Organisation::class)->makePartial();

        /** @var ApplicationOrganisationPerson $aop1 */
        $aop1 = m::mock(ApplicationOrganisationPerson::class)->makePartial();
        $aop1->setAction('U');
        $aop1->setPerson($person1);
        $aop1->setOriginalPerson($person2);
        $aop1->setOrganisation($org1);

        $aops = new ArrayCollection();
        $aops->add($aop1);

        /** @var ApplicationEntity $application */
        $application = m::mock(ApplicationEntity::class)->makePartial();
        $application->setApplicationOrganisationPersons($aops);

        $this->repoMap['Application']->shouldReceive('fetchUsingId')
            ->with($command)
            ->andReturn($application);

        $savedPerson = null;

        $this->repoMap['Person']->shouldReceive('save')
            ->once()
            ->andReturnUsing(
                function (Person $person) use ($person1, &$savedPerson) {
                    $this->assertNotSame($person1, $person);
                    $savedPerson = $person;
                }
            );

        $orgPersonRecord = m::mock(OrganisationPerson::class)->makePartial();

        $orgPersonRecords = [
            $orgPersonRecord
        ];

        $this->repoMap['OrganisationPerson']->shouldReceive('fetchByOrgAndPerson')
            ->with($org1, $person2)
            ->andReturn($orgPersonRecords)
            ->shouldReceive('save')
            ->once()
            ->andReturnUsing(
                function (OrganisationPerson $orgPerson) use ($aop1) {
                    $this->assertNotSame($aop1, $orgPerson);
                }
            )
            ->shouldReceive('delete')
            ->once()
            ->with($orgPersonRecord);

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [],
            'messages' => [
                'Organisation person records have been copied'
            ]
        ];

        $this->assertEquals($expected, $result->toArray());
    }

    public function testHandleCommandWithoutDelete()
    {
        $data = [
            'id' => 111
        ];

        $command = GrantPeopleCmd::create($data);

        $person2 = new Person();

        $org1 = m::mock(Organisation::class)->makePartial();

        /** @var ApplicationOrganisationPerson $aop1 */
        $aop1 = m::mock(ApplicationOrganisationPerson::class)->makePartial();
        $aop1->setAction('D');
        $aop1->setPerson($person2);
        $aop1->setOrganisation($org1);

        $aops = new ArrayCollection();
        $aops->add($aop1);

        /** @var ApplicationEntity $application */
        $application = m::mock(ApplicationEntity::class)->makePartial();
        $application->setApplicationOrganisationPersons($aops);

        $this->repoMap['Application']->shouldReceive('fetchUsingId')
            ->with($command)
            ->andReturn($application);

        $orgPersonRecord = m::mock(OrganisationPerson::class)->makePartial();

        $orgPersonRecords = [
            $orgPersonRecord
        ];

        $this->repoMap['OrganisationPerson']->shouldReceive('fetchByOrgAndPerson')
            ->with($org1, $person2)
            ->andReturn($orgPersonRecords)
            ->shouldReceive('delete')
            ->once()
            ->with($orgPersonRecord);

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [],
            'messages' => [
                'Organisation person records have been copied'
            ]
        ];

        $this->assertEquals($expected, $result->toArray());
    }
}
