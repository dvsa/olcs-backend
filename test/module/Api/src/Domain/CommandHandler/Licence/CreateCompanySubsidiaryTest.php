<?php

/**
 * Create Company Subsidiary Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Licence;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\Command\Task\CreateTask;
use Dvsa\Olcs\Api\Domain\Repository\Licence;
use Dvsa\Olcs\Api\Domain\Repository\CompanySubsidiary;
use Dvsa\Olcs\Api\Domain\CommandHandler\Licence\CreateCompanySubsidiary;
use Dvsa\Olcs\Transfer\Command\Licence\CreateCompanySubsidiary as Cmd;
use Mockery as m;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use ZfcRbac\Service\AuthorizationService;
use Dvsa\Olcs\Api\Entity\Licence\Licence as LicenceEntity;
use Dvsa\Olcs\Api\Entity\Organisation\CompanySubsidiary as CompanySubsidiaryEntity;
use Dvsa\Olcs\Api\Entity\User\Permission;
use Dvsa\Olcs\Api\Entity\System\Category;

/**
 * Create Company Subsidiary Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class CreateCompanySubsidiaryTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new CreateCompanySubsidiary();
        $this->mockRepo('Licence', Licence::class);
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
            'name' => 'Foo',
            'companyNo' => '12345678'
        ];

        $command = Cmd::create($data);

        /** @var LicenceEntity $licence */
        $licence = m::mock(LicenceEntity::class)->makePartial();

        $this->repoMap['Licence']->shouldReceive('fetchById')
            ->with(111)
            ->andReturn($licence);

        /** @var CompanySubsidiaryEntity $savedCompanySubsidiary */
        $savedCompanySubsidiary = null;

        $this->repoMap['CompanySubsidiary']->shouldReceive('save')
            ->once()
            ->with(m::type(CompanySubsidiaryEntity::class))
            ->andReturnUsing(
                function (CompanySubsidiaryEntity $companySubsidiary) use (&$savedCompanySubsidiary) {
                    $companySubsidiary->setId(222);
                    $savedCompanySubsidiary = $companySubsidiary;
                }
            );

        $this->mockedSmServices[AuthorizationService::class]->shouldReceive('isGranted')
            ->with(Permission::SELFSERVE_USER, null)
            ->andReturn(true);

        $expectedData = [
            'category' => Category::CATEGORY_APPLICATION,
            'subCategory' => Category::TASK_SUB_CATEGORY_APPLICATION_SUBSIDIARY_DIGITAL,
            'description' => 'Subsidiary company added - Foo',
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
                'companySubsidiary' => 222,
                'task' => 123
            ],
            'messages' => [
                'Company Subsidiary created',
                'Task created'
            ]
        ];

        $this->assertEquals($expected, $result->toArray());

        $this->assertEquals('Foo', $savedCompanySubsidiary->getName());
        $this->assertEquals('12345678', $savedCompanySubsidiary->getCompanyNo());
        $this->assertSame($licence, $savedCompanySubsidiary->getLicence());
    }

    public function testHandleCommandInternal()
    {
        $data = [
            'licence' => 111,
            'name' => 'Foo',
            'companyNo' => '12345678'
        ];

        $command = Cmd::create($data);

        /** @var LicenceEntity $licence */
        $licence = m::mock(LicenceEntity::class)->makePartial();

        $this->repoMap['Licence']->shouldReceive('fetchById')
            ->with(111)
            ->andReturn($licence);

        /** @var CompanySubsidiaryEntity $savedCompanySubsidiary */
        $savedCompanySubsidiary = null;

        $this->repoMap['CompanySubsidiary']->shouldReceive('save')
            ->once()
            ->with(m::type(CompanySubsidiaryEntity::class))
            ->andReturnUsing(
                function (CompanySubsidiaryEntity $companySubsidiary) use (&$savedCompanySubsidiary) {
                    $companySubsidiary->setId(222);
                    $savedCompanySubsidiary = $companySubsidiary;
                }
            );

        $this->mockedSmServices[AuthorizationService::class]->shouldReceive('isGranted')
            ->with(Permission::SELFSERVE_USER, null)
            ->andReturn(false);

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [
                'companySubsidiary' => 222
            ],
            'messages' => [
                'Company Subsidiary created'
            ]
        ];

        $this->assertEquals($expected, $result->toArray());

        $this->assertEquals('Foo', $savedCompanySubsidiary->getName());
        $this->assertEquals('12345678', $savedCompanySubsidiary->getCompanyNo());
        $this->assertSame($licence, $savedCompanySubsidiary->getLicence());
    }
}
