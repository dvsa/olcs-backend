<?php

/**
 * Update Addresses test
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Licence;

use Mockery as m;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\Licence\UpdateAddresses;

use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;

use Dvsa\Olcs\Api\Entity\Licence\Licence as LicenceEntity;

use Dvsa\Olcs\Transfer\Command\Licence\UpdateAddresses as Cmd;
use Dvsa\Olcs\Api\Domain\Command\Licence\SaveAddresses;
use Dvsa\Olcs\Api\Domain\Command\Task\CreateTask;

use ZfcRbac\Service\AuthorizationService;
use Dvsa\Olcs\Api\Entity\User\Permission;

use Dvsa\Olcs\Api\Entity\System\Category;

/**
 * Update Addresses test
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class UpdateAddressesTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new UpdateAddresses();
        $this->mockRepo('Licence', Licence::class);

        $this->mockedSmServices[AuthorizationService::class] = m::mock(AuthorizationService::class);

        parent::setUp();
    }

    public function testHandleCommandWithDirtyAddresses()
    {
        $data = [
            'id' => 123,
            'correspondence' => 'corr',
            'contact' => 'contact',
            'correspondenceAddress' => 'c_address',
            'establishment' => 'est',
            'establishmentAddress' => 'e_address',
            'consultant' => 'consultant'
        ];
        $command = Cmd::create($data);

        $this->mockedSmServices[AuthorizationService::class]->shouldReceive('isGranted')
            ->with(Permission::SELFSERVE_USER, null)
            ->andReturn(true);

        $result = new Result();

        $result->setFlag('isDirty', true);

        $this->expectedSideEffect(
            SaveAddresses::class,
            $data,
            $result
        );

        $this->expectedSideEffect(
            CreateTask::class,
            [
                'category' => Category::CATEGORY_APPLICATION,
                'subCategory' => Category::TASK_SUB_CATEGORY_APPLICATION_ADDRESS_CHANGE_DIGITAL,
                'description' => 'Address Change',
                'licence' => 123,
                // @TODO: no...
                'actionDate' => date('Y-m-d H:i:s'),
                'application' => null,
                'assignedToUser' => null,
                'assignedToTeam' => null,
                'isClosed' => false,
                'urgent' => false,
                'busReg' => null,
                'case' => null,
                'transportManager' => null,
                'irfoOrganisation' => null,
            ],
            $result
        );

        $licence = m::mock(LicenceEntity::class)
            ->makePartial()
            ->shouldReceive('getId')
            ->andReturn(123)
            ->getMock();

        $this->repoMap['Licence']->shouldReceive('fetchUsingId')
            ->with($command)
            ->andReturn($licence)
            ->getMock();

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [],
            'messages' => [
            ]
        ];

        $this->assertEquals($expected, $result->toArray());

        $this->assertTrue($result->getFlag('hasChanged'));
    }
}
