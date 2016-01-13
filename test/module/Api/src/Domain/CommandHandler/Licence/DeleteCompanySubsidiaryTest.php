<?php

/**
 * Delete Company Subsidiary Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Licence;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\Command\Task\CreateTask;
use Dvsa\Olcs\Api\Domain\Repository\CompanySubsidiary;
use Dvsa\Olcs\Api\Domain\CommandHandler\Licence\DeleteCompanySubsidiary;
use Dvsa\Olcs\Transfer\Command\Licence\DeleteCompanySubsidiary as Cmd;
use Mockery as m;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use ZfcRbac\Service\AuthorizationService;
use Dvsa\Olcs\Api\Entity\Organisation\CompanySubsidiary as CompanySubsidiaryEntity;
use Dvsa\Olcs\Api\Entity\User\Permission;
use Dvsa\Olcs\Api\Entity\System\Category;

/**
 * Delete Company Subsidiary Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class DeleteCompanySubsidiaryTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new DeleteCompanySubsidiary();
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

    public function testHandleCommand()
    {
        $data = [
            'licence' => 111,
            'ids' => [11, 22]
        ];

        $command = Cmd::create($data);

        /** @var CompanySubsidiaryEntity $companySubsidiary11 */
        $companySubsidiary11 = m::mock(CompanySubsidiaryEntity::class)->makePartial();
        $companySubsidiary11->setName('Foo');

        /** @var CompanySubsidiaryEntity $companySubsidiary22 */
        $companySubsidiary22 = m::mock(CompanySubsidiaryEntity::class)->makePartial();
        $companySubsidiary22->setName('Bar');

        $this->repoMap['CompanySubsidiary']->shouldReceive('fetchById')
            ->once()
            ->with(11)
            ->andReturn($companySubsidiary11)
            ->shouldReceive('fetchById')
            ->once()
            ->with(22)
            ->andReturn($companySubsidiary22)
            ->shouldReceive('delete')
            ->once()
            ->with($companySubsidiary11)
            ->shouldReceive('delete')
            ->once()
            ->with($companySubsidiary22);

        $this->mockedSmServices[AuthorizationService::class]->shouldReceive('isGranted')
            ->with(Permission::SELFSERVE_USER, null)
            ->andReturn(true);

        $expectedData1 = [
            'category' => Category::CATEGORY_APPLICATION,
            'subCategory' => Category::TASK_SUB_CATEGORY_APPLICATION_SUBSIDIARY_DIGITAL,
            'description' => 'Subsidiary company deleted - Foo',
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

        $expectedData2 = [
            'category' => Category::CATEGORY_APPLICATION,
            'subCategory' => Category::TASK_SUB_CATEGORY_APPLICATION_SUBSIDIARY_DIGITAL,
            'description' => 'Subsidiary company deleted - Bar',
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

        $this->expectedSideEffect(CreateTask::class, $expectedData1, $result1);
        $this->expectedSideEffect(CreateTask::class, $expectedData2, $result1);

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [
                'task' => 123
            ],
            'messages' => [
                'Task created',
                'Task created',
                '2 Company Subsidiaries removed'
            ]
        ];

        $this->assertEquals($expected, $result->toArray());
    }

    public function testHandleCommandInternal()
    {
        $data = [
            'licence' => 111,
            'ids' => [11, 22]
        ];

        $command = Cmd::create($data);

        /** @var CompanySubsidiaryEntity $companySubsidiary11 */
        $companySubsidiary11 = m::mock(CompanySubsidiaryEntity::class)->makePartial();
        $companySubsidiary11->setName('Foo');

        /** @var CompanySubsidiaryEntity $companySubsidiary22 */
        $companySubsidiary22 = m::mock(CompanySubsidiaryEntity::class)->makePartial();
        $companySubsidiary22->setName('Bar');

        $this->repoMap['CompanySubsidiary']->shouldReceive('fetchById')
            ->once()
            ->with(11)
            ->andReturn($companySubsidiary11)
            ->shouldReceive('fetchById')
            ->once()
            ->with(22)
            ->andReturn($companySubsidiary22)
            ->shouldReceive('delete')
            ->once()
            ->with($companySubsidiary11)
            ->shouldReceive('delete')
            ->once()
            ->with($companySubsidiary22);

        $this->mockedSmServices[AuthorizationService::class]->shouldReceive('isGranted')
            ->with(Permission::SELFSERVE_USER, null)
            ->andReturn(false);

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [],
            'messages' => [
                '2 Company Subsidiaries removed'
            ]
        ];

        $this->assertEquals($expected, $result->toArray());
    }
}
