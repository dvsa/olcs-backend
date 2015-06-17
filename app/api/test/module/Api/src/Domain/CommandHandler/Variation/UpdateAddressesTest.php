<?php

/**
 * Update Addresses test
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Variation;

use Mockery as m;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\Variation\UpdateAddresses;

use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;

use Dvsa\Olcs\Api\Entity\Application\Application as ApplicationEntity;
use Dvsa\Olcs\Api\Entity\Licence\Licence as LicenceEntity;

use Dvsa\Olcs\Transfer\Command\Variation\UpdateAddresses as Cmd;
use Dvsa\Olcs\Api\Domain\Command\Application\UpdateApplicationCompletion as UpdateApplicationCompletionCommand;
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
        $this->mockRepo('Application', Application::class);

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

        // licence ID overrides application ID
        $addressData = array_merge($data, ['id' => 456]);

        $this->expectedSideEffect(
            SaveAddresses::class,
            $addressData,
            $result
        );

        $this->expectedSideEffect(
            UpdateApplicationCompletionCommand::class,
            [
                'id' => 123,
                'section' => 'addresses'
            ],
            $result
        );

        $this->expectedSideEffect(
            CreateTask::class,
            [
                'category' => Category::CATEGORY_APPLICATION,
                'subCategory' => Category::TASK_SUB_CATEGORY_APPLICATION_ADDRESS_CHANGE_DIGITAL,
                'description' => 'Address Change',
                'licence' => 456,
                // @TODO: no...
                'actionDate' => date('Y-m-d H:i:s'),
                'application' => null,
                'assignedToUser' => null,
                'assignedToTeam' => null,
                'isClosed' => false,
                'urgent' => false
            ],
            $result
        );

        $application = m::mock(ApplicationEntity::class)
            ->makePartial()
            ->shouldReceive('getId')
            ->andReturn(123)
            ->shouldReceive('getLicence')
            ->andReturn(
                m::mock(LicenceEntity::class)
                ->shouldReceive('getId')
                ->andReturn(456)
                ->getMock()
            )
            ->getMock();

        $this->repoMap['Application']->shouldReceive('fetchUsingId')
            ->with($command)
            ->andReturn($application)
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
