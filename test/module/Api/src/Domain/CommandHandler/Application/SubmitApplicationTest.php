<?php

/**
 * Submit Application Test
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Application;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\Command\Task\CreateTask as CreateTaskCmd;
use Dvsa\Olcs\Api\Domain\CommandHandler\Application\SubmitApplication;
use Dvsa\Olcs\Api\Domain\Exception\ValidationException;
use Dvsa\Olcs\Api\Entity\Application\Application as ApplicationEntity;
use Dvsa\Olcs\Api\Entity\Licence\Licence as LicenceEntity;
use Dvsa\Olcs\Api\Entity\System\Category as CategoryEntity;
use Dvsa\Olcs\Transfer\Command\Application\SubmitApplication as Cmd;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Mockery as m;

/**
 * Submit Application Test
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class SubmitApplicationTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new SubmitApplication();
        $this->mockRepo('Application', Application::class);

        $this->refData = [
            ApplicationEntity::APPLICATION_STATUS_NOT_SUBMITTED,
            ApplicationEntity::APPLICATION_STATUS_UNDER_CONSIDERATION,
            LicenceEntity::LICENCE_STATUS_UNDER_CONSIDERATION,
        ];

        $this->references = [
            ApplicationEntity::class => [
                69 => m::mock(ApplicationEntity::class),
            ],
            LicenceEntity::class => [
                7 => m::mock(LicenceEntity::class)
            ],
        ];

        parent::setUp();
    }

    /**
     * @param boolean $isVariation
     * @param array $expected
     * @dataProvider isVariationProvider
     */
    public function testHandleCommand($isVariation, $expected)
    {
        $applicationId = 69;
        $version       = 10;
        $licenceId     = 7;
        $taskId        = 111;

        $command = Cmd::create(
            [
                'id' => $applicationId,
                'version' => $version,
            ]
        );

        /** @var LicenceEntity $licence */
        $licence = $this->mapReference(LicenceEntity::class, $licenceId);

        /** @var ApplicationEntity $application */
        $application = $this->mapReference(ApplicationEntity::class, $applicationId);
        $application->setLicence($licence);
        $application->setStatus($this->mapRefdata(ApplicationEntity::APPLICATION_STATUS_NOT_SUBMITTED));
        $application->setIsVariation($isVariation);
        $application
            ->shouldReceive('setStatus')
            ->with($this->mapRefdata(ApplicationEntity::APPLICATION_STATUS_UNDER_CONSIDERATION))
            ->andReturnSelf()
            ->shouldReceive('getCode')
            ->andReturn('TEST CODE');
            // @TODO assert dates

        // licence status should be updated if application is not a variation
        if ($isVariation) {
            $licence
                ->shouldReceive('setStatus')
                ->never();
        } else {
            $licence
                ->shouldReceive('setStatus')
                ->with($this->mapRefdata(LicenceEntity::LICENCE_STATUS_UNDER_CONSIDERATION))
                ->once()
                ->andReturnSelf();
        }

        $this->repoMap['Application']
            ->shouldReceive('fetchUsingId')
            ->with($command, Query::HYDRATE_OBJECT, $version)
            ->andReturn($application);

        $this->repoMap['Application']
            ->shouldReceive('save')
            ->with($application)
            ->once();

        $expectedTaskData = [
            'category' => CategoryEntity::CATEGORY_APPLICATION,
            'subCategory' => CategoryEntity::TASK_SUB_CATEGORY_APPLICATION_FORMS_DIGITAL,
            'description' => 'TEST CODE Application',
            'actionDate' => date('Y-m-d'), // @TODO mock date
            'assignedToUser' => null,
            'assignedToTeam' => null,
            'isClosed' => false,
            'urgent' => false,
            'application' => $applicationId,
            'licence' => $licenceId,
            'busReg' => null,
            'case' => null,
            'transportManager' => null,
            'irfoOrganisation' => null,
        ];
        $taskResult = new Result();
        $taskResult->addId('task', $taskId);
        $taskResult->addMessage('task created');
        $this->expectedSideEffect(CreateTaskCmd::class, $expectedTaskData, $taskResult);

        $result = $this->sut->handleCommand($command);

        $this->assertEquals($expected, $result->toArray());
    }

    public function isVariationProvider()
    {
        return [
            'new app' => [
                false,
                [
                    'id' => [
                        'application' => 69,
                        'licence' => 7,
                        'task' => 111,
                    ],
                    'messages' => [
                        'Application updated',
                        'Licence updated',
                        'task created',
                    ],
                ],
            ],
            'variation' => [
                true,
                [
                    'id' => [
                        'application' => 69,
                        'task' => 111,
                    ],
                    'messages' => [
                        'Application updated',
                        'task created',
                    ],
                ],
            ],
        ];
    }

    public function testHandleInvalidStatus()
    {
        $applicationId = 69;
        $version = 10;

        $command = Cmd::create(
            [
                'id' => $applicationId,
                'version' => $version,
            ]
        );

        /** @var ApplicationEntity $application */
        $application = $this->mapReference(ApplicationEntity::class, $applicationId);
        $application->setStatus($this->mapRefdata(ApplicationEntity::APPLICATION_STATUS_UNDER_CONSIDERATION));

        $this->repoMap['Application']
            ->shouldReceive('fetchUsingId')
            ->with($command, Query::HYDRATE_OBJECT, $version)
            ->andReturn($application);

        $this->setExpectedException(ValidationException::class);

        $this->sut->handleCommand($command);
    }
}
