<?php

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Licence;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\Command as DomainCmd;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\Licence\CreateCompanySubsidiary;
use Dvsa\Olcs\Api\Domain\Repository;
use Dvsa\Olcs\Api\Entity\System\Category;
use Dvsa\Olcs\Api\Entity\User\Permission;
use Dvsa\Olcs\Transfer\Command as TransferCmd;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Mockery as m;
use ZfcRbac\Service\AuthorizationService;

/**
 * @covers Dvsa\Olcs\Api\Domain\CommandHandler\Licence\CreateCompanySubsidiary
 */
class CreateCompanySubsidiaryTest extends CommandHandlerTestCase
{
    const LICENCE_ID = 1111;
    const TASK_ID = 877;

    /** @var  CreateCompanySubsidiary|m\MockInterface */
    protected $sut;
    /** @var  m\MockInterface */
    private $mockAuthSrv;

    public function setUp()
    {
        $this->sut = m::mock(CreateCompanySubsidiary::class . '[create]')
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();

        $this->mockRepo('CompanySubsidiary', Repository\CompanySubsidiary::class);

        //  mock services
        $this->mockAuthSrv = m::mock(AuthorizationService::class);
        $this->mockedSmServices[AuthorizationService::class] = $this->mockAuthSrv;

        parent::setUp();
    }

    /**
     * @dataProvider dpTestHandleCommand
     */
    public function testHandleCommand($isGranted, $expectTask)
    {
        $data = [
            'licence' => self::LICENCE_ID,
            'name' => 'unit_Name',
        ];
        $command = TransferCmd\Licence\CreateCompanySubsidiary::create($data);

        //  mock is granted
        $this->mockIsGranted(Permission::SELFSERVE_USER, $isGranted);

        //  mock create result
        $this->sut->shouldReceive('create')
            ->once()
            ->with($command, self::LICENCE_ID)
            ->andReturn(new Result());

        //  mock create task
        if ($expectTask === true) {
            $expectedData = [
                'category' => Category::CATEGORY_APPLICATION,
                'subCategory' => Category::TASK_SUB_CATEGORY_APPLICATION_SUBSIDIARY_DIGITAL,
                'description' => 'Subsidiary company added - unit_Name',
                'licence' => self::LICENCE_ID,
            ];

            $resultTask = new Result();
            $resultTask->addId('task', self::TASK_ID);
            $resultTask->addMessage('Task created');

            $this->expectedSideEffect(DomainCmd\Task\CreateTask::class, $expectedData, $resultTask);
        } else {
            $this->sut->shouldReceive('handleSideEffect')->never();
        }

        //  call
        $actual = $this->sut->handleCommand($command);

        static::assertInstanceOf(Result::class, $actual);

        if ($expectTask === true) {
            $expected = [
                'id' => [
                    'task' => self::TASK_ID,
                ],
                'messages' => [
                    'Task created',
                ],
            ];
            static::assertEquals($expected, $actual->toArray());
        }
    }

    public function dpTestHandleCommand()
    {
        return [
            [
                'isGranted' => true,
                'expectTask' => true,
            ],
            [
                'isGranted' => false,
                'expectTask' => false,
            ],
        ];
    }

    private function mockIsGranted($permission, $result)
    {
        $this->mockAuthSrv
            ->shouldReceive('isGranted')
            ->with($permission, null)
            ->andReturn($result);
    }
}
