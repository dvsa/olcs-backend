<?php

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Licence;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\Command as DomainCmd;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\Licence\UpdateCompanySubsidiary;
use Dvsa\Olcs\Api\Domain\Repository;
use Dvsa\Olcs\Api\Entity;
use Dvsa\Olcs\Api\Entity\System\Category;
use Dvsa\Olcs\Api\Entity\User\Permission;
use Dvsa\Olcs\Transfer\Command as TransferCmd;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Mockery as m;
use ZfcRbac\Service\AuthorizationService;

/**
 * @covers Dvsa\Olcs\Api\Domain\CommandHandler\Licence\UpdateCompanySubsidiary
 */
class UpdateCompanySubsidiaryTest extends CommandHandlerTestCase
{
    const LICENCE_ID = 1111;
    const TASK_ID = 877;
    const VERSION = 99;

    /** @var  UpdateCompanySubsidiary|m\MockInterface */
    protected $sut;
    /** @var  m\MockInterface */
    private $mockAuthSrv;

    public function setUp()
    {
        $this->sut = m::mock(UpdateCompanySubsidiary::class . '[update]')
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
    public function testHandleCommand($hasChanged, $isGranted, $expectTask)
    {
        $data = [
            'licence' => self::LICENCE_ID,
            'name' => 'unit_Name',
            'version' => self::VERSION,
        ];
        $command = TransferCmd\Licence\UpdateCompanySubsidiary::create($data);

        //  mock is granted
        $this->mockIsGranted(Permission::SELFSERVE_USER, $isGranted);

        //  mock update result
        $result = new Result();
        $result->setFlag('hasChanged', $hasChanged);

        $this->sut->shouldReceive('update')->once()->with($command)->andReturn($result);

        //  mock create task
        if ($expectTask === true) {
            $expectedData = [
                'category' => Category::CATEGORY_APPLICATION,
                'subCategory' => Category::TASK_SUB_CATEGORY_APPLICATION_SUBSIDIARY_DIGITAL,
                'description' => 'Subsidiary company updated - unit_Name',
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
        static::assertEquals($hasChanged, $actual->getFlag('hasChanged'));

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
                'hasChanged' => false,
                'isGranted' => true,
                'expectTask' => false,
            ],
            [
                'hasChanged' => true,
                'isGranted' => false,
                'expectTask' => false,
            ],
            [
                'hasChanged' => true,
                'isGranted' => true,
                'expectTask' => true,
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
