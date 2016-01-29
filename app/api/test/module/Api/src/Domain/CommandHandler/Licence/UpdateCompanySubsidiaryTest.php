<?php

/**
 * Update Company Subsidiary Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Licence;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\Command\Task\CreateTask;
use Dvsa\Olcs\Api\Domain\Repository\CompanySubsidiary;
use Dvsa\Olcs\Api\Domain\CommandHandler\Licence\UpdateCompanySubsidiary;
use Dvsa\Olcs\Transfer\Command\Licence\UpdateCompanySubsidiary as Cmd;
use Mockery as m;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use ZfcRbac\Service\AuthorizationService;
use Dvsa\Olcs\Api\Entity\Organisation\CompanySubsidiary as CompanySubsidiaryEntity;
use Dvsa\Olcs\Api\Entity\User\Permission;
use Dvsa\Olcs\Api\Entity\System\Category;

/**
 * Update Company Subsidiary Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class UpdateCompanySubsidiaryTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new UpdateCompanySubsidiary();
        $this->mockRepo('CompanySubsidiary', CompanySubsidiary::class);

        $this->mockedSmServices[AuthorizationService::class] = m::mock(AuthorizationService::class);

        parent::setUp();
    }

    protected function initReferences()
    {
        $this->refData = [

        ];

        parent::initReferences();
    }

    public function testHandleCommandWithoutChange()
    {
        $data = [
            'id' => 11,
            'version' => 1,
            'licence' => 111,
            'name' => 'Foo',
            'companyNo' => '12345678'
        ];

        $command = Cmd::create($data);

        /** @var CompanySubsidiaryEntity $companySubsidiary */
        $companySubsidiary = m::mock(CompanySubsidiaryEntity::class)->makePartial();
        $companySubsidiary->setName('Foo');
        $companySubsidiary->setCompanyNo('12345678');

        $this->repoMap['CompanySubsidiary']->shouldReceive('fetchUsingId')
            ->once()
            ->with($command, Query::HYDRATE_OBJECT, 1)
            ->andReturn($companySubsidiary);

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [],
            'messages' => [
                'Company Subsidiary unchanged'
            ]
        ];

        $this->assertEquals($expected, $result->toArray());
        $this->assertFalse($result->getFlag('hasChanged'));
    }

    public function testHandleCommand()
    {
        $data = [
            'id' => 11,
            'version' => 1,
            'licence' => 111,
            'name' => 'Foo ltd',
            'companyNo' => '12345678'
        ];

        $command = Cmd::create($data);

        /** @var CompanySubsidiaryEntity $companySubsidiary */
        $companySubsidiary = m::mock(CompanySubsidiaryEntity::class)->makePartial();
        $companySubsidiary->setName('Foo');
        $companySubsidiary->setCompanyNo('12345678');

        $this->repoMap['CompanySubsidiary']->shouldReceive('fetchUsingId')
            ->once()
            ->with($command, Query::HYDRATE_OBJECT, 1)
            ->andReturn($companySubsidiary)
            ->shouldReceive('save')
            ->once()
            ->with($companySubsidiary);

        $this->mockedSmServices[AuthorizationService::class]->shouldReceive('isGranted')
            ->with(Permission::SELFSERVE_USER, null)
            ->andReturn(true);

        $expectedData = [
            'category' => Category::CATEGORY_APPLICATION,
            'subCategory' => Category::TASK_SUB_CATEGORY_APPLICATION_SUBSIDIARY_DIGITAL,
            'description' => 'Subsidiary company updated - Foo ltd',
            'licence' => 111,
            'actionDate' => null,
            'assignedToUser' => null,
            'assignedToTeam' => null,
            'isClosed' => false,
            'urgent' => false,
            'application' => null,
            'busReg' => null,
            'case' => null,
            'transportManager' => null,
            'irfoOrganisation' => null,
        ];

        $result1 = new Result();
        $result1->addId('task', 123);
        $result1->addMessage('Task created');

        $this->expectedSideEffect(CreateTask::class, $expectedData, $result1);

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [
                'task' => 123
            ],
            'messages' => [
                'Company Subsidiary updated',
                'Task created'
            ]
        ];

        $this->assertEquals($expected, $result->toArray());
        $this->assertTrue($result->getFlag('hasChanged'));
    }

    public function testHandleCommandInternal()
    {
        $data = [
            'id' => 11,
            'version' => 1,
            'licence' => 111,
            'name' => 'Foo ltd',
            'companyNo' => '12345678'
        ];

        $command = Cmd::create($data);

        /** @var CompanySubsidiaryEntity $companySubsidiary */
        $companySubsidiary = m::mock(CompanySubsidiaryEntity::class)->makePartial();
        $companySubsidiary->setName('Foo');
        $companySubsidiary->setCompanyNo('12345678');

        $this->mockedSmServices[AuthorizationService::class]->shouldReceive('isGranted')
            ->with(Permission::SELFSERVE_USER, null)
            ->andReturn(false);

        $this->repoMap['CompanySubsidiary']->shouldReceive('fetchUsingId')
            ->once()
            ->with($command, Query::HYDRATE_OBJECT, 1)
            ->andReturn($companySubsidiary)
            ->shouldReceive('save')
            ->once()
            ->with($companySubsidiary);

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [],
            'messages' => [
                'Company Subsidiary updated'
            ]
        ];

        $this->assertEquals($expected, $result->toArray());
        $this->assertTrue($result->getFlag('hasChanged'));

        $this->assertEquals('Foo ltd', $companySubsidiary->getName());
    }
}
