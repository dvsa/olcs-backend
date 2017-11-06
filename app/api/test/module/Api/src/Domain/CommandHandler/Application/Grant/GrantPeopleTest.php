<?php

/**
 * Grant People Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Application\Grant;

use Dvsa\Olcs\Api\Domain\Command\Task\CreateTask;
use Dvsa\Olcs\Api\Entity\System\Category;
use Doctrine\Common\Collections\ArrayCollection;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\Application\Grant\GrantPeople;
use Dvsa\Olcs\Api\Entity\Application\Application as ApplicationEntity;
use Dvsa\Olcs\Api\Entity\Application\ApplicationOrganisationPerson;
use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Dvsa\Olcs\Api\Entity\Organisation\Organisation;
use Dvsa\Olcs\Api\Entity\Organisation\OrganisationPerson;
use Dvsa\Olcs\Api\Entity\Person\Person;
use Mockery as m;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Api\Domain\Command\Application\Grant\GrantPeople as GrantPeopleCmd;

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

        $aop1Saved = false;

        $aops = new ArrayCollection();
        $aops->add($aop1);

        $application = $this->createMockApplication($aops);

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
                function (OrganisationPerson $orgPerson) use ($aop1, &$aop1Saved) {
                    $this->assertNotSame($aop1, $orgPerson);
                    $aop1Saved = true;
                }
            );

        $this->repoMap['OrganisationPerson']->shouldReceive('fetchCountForOrganisation')
            ->once()
            ->with('TEST_ORGANISATION_ID')
            ->andReturnUsing(
                function () use (&$aop1Saved) {
                    $this->assertTrue($aop1Saved);
                    return 1;
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

        $aop1Saved = false;

        $aops = new ArrayCollection();
        $aops->add($aop1);

        $application = $this->createMockApplication($aops);

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
                function (OrganisationPerson $orgPerson) use ($aop1, &$aop1Saved) {
                    $this->assertNotSame($aop1, $orgPerson);
                    $aop1Saved = true;
                }
            )
            ->shouldReceive('delete')
            ->once()
            ->with($orgPersonRecord);

        $this->repoMap['OrganisationPerson']->shouldReceive('fetchCountForOrganisation')
            ->once()
            ->with('TEST_ORGANISATION_ID')
            ->andReturnUsing(
                function () use (&$aop1Saved) {
                    $this->assertTrue($aop1Saved);
                    return 1;
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

        $aop1Saved = false;

        $aops = new ArrayCollection();
        $aops->add($aop1);
        $application = $this->createMockApplication($aops);


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
            ->with($orgPersonRecord)
            ->andReturnUsing(
                function () use (&$aop1Saved) {
                    $aop1Saved = true;
                }
            );

        $this->repoMap['OrganisationPerson']->shouldReceive('fetchCountForOrganisation')
            ->once()
            ->with('TEST_ORGANISATION_ID')
            ->andReturnUsing(
                function () use (&$aop1Saved) {
                    $this->assertTrue($aop1Saved);
                    return 1;
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

    public function testGrantWhenNoMorePeople()
    {
        $data = [
            'id' => 111
        ];

        $command = GrantPeopleCmd::create($data);

        $person2 = new Person();

        /** @var Organisation|m\Mock $org1 */
        $org1 = m::mock(Organisation::class)->makePartial();

        /** @var ApplicationOrganisationPerson $aop1 */
        $aop1 = m::mock(ApplicationOrganisationPerson::class)->makePartial();
        $aop1->setAction('D');
        $aop1->setPerson($person2);
        $aop1->setOrganisation($org1);

        $aop1Saved = false;

        $aops = new ArrayCollection();
        $aops->add($aop1);
        $application = $this->createMockApplication($aops);


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
            ->with($orgPersonRecord)
            ->andReturnUsing(
                function () use (&$aop1Saved) {
                    $aop1Saved = true;
                }
            );

        $this->repoMap['OrganisationPerson']->shouldReceive('fetchCountForOrganisation')
            ->once()
            ->with('TEST_ORGANISATION_ID')
            ->andReturnUsing(
                function () use (&$aop1Saved) {
                    $this->assertTrue($aop1Saved);
                    return 0;
                }
            );

        $this->expectedSideEffect(
            CreateTask::class,
            [
                'category' => Category::CATEGORY_APPLICATION,
                'subCategory' => Category::TASK_SUB_CATEGORY_DIRECTOR_CHANGE_DIGITAL,
                'description' => 'Last person removed',
                'licence' => 'TEST_LICENCE_ID',
            ],
            new Result()
        );

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [],
            'messages' => [
                'Organisation person records have been copied',
                'Task created as there are no people in the organisation',
            ]
        ];

        $this->assertEquals($expected, $result->toArray());
    }

    private function createMockApplication($applicationOrganisationPersons)
    {
        /** @var Organisation|m\Mock $organisation */
        $organisation = m::mock(Organisation::class)->makePartial();
        $organisation->setId('TEST_ORGANISATION_ID');

        /** @var Licence|m\Mock $licence */
        $licence = m::mock(Licence::class)->makePartial();
        $licence->setOrganisation($organisation);
        $licence->setId('TEST_LICENCE_ID');

        /** @var ApplicationEntity $application */
        $application = m::mock(ApplicationEntity::class)->makePartial();
        $application->setApplicationOrganisationPersons($applicationOrganisationPersons);
        $application->setLicence($licence);
        return $application;
    }
}
