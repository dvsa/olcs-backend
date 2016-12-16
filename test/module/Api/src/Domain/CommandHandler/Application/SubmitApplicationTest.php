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
use Dvsa\Olcs\Api\Domain\Util\DateTime\DateTime;
use Dvsa\Olcs\Api\Entity\Application\Application as ApplicationEntity;
use Dvsa\Olcs\Api\Entity\Licence\Licence as LicenceEntity;
use Dvsa\Olcs\Api\Entity\System\Category as CategoryEntity;
use Dvsa\Olcs\Api\Entity\System\RefData;
use Dvsa\Olcs\Api\Entity\Application\ApplicationCompletion;
use Dvsa\Olcs\Transfer\Command\Application\CreateSnapshot;
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
            LicenceEntity::LICENCE_TYPE_STANDARD_NATIONAL,
            LicenceEntity::LICENCE_TYPE_SPECIAL_RESTRICTED,
            LicenceEntity::LICENCE_CATEGORY_PSV,
            \Dvsa\Olcs\Api\Entity\Application\S4::STATUS_APPROVED,
        ];

        $this->references = [
            ApplicationEntity::class => [
                69 => m::mock(ApplicationEntity::class),
            ],
            LicenceEntity::class => [
                7 => m::mock(LicenceEntity::class)
            ],
        ];
        $this->mockedSmServices = [
            \ZfcRbac\Service\AuthorizationService::class => m::mock(\ZfcRbac\Service\AuthorizationService::class)
        ];

        parent::setUp();
    }

    /**
     * @param boolean $isVariation
     * @param array $expected
     * @param bool $isInternalUser
     * @dataProvider isVariationProvider
     */
    public function testHandleCommand($isVariation, $expected, $isInternalUser)
    {
        $this->setupIsInternalUser($isInternalUser);

        $applicationId = 69;
        $version       = 10;
        $licenceId     = 7;
        $taskId        = 111;
        $now           = new DateTime();

        $command = Cmd::create(
            [
                'id' => $applicationId,
                'version' => $version,
            ]
        );

        $trafficArea = new \Dvsa\Olcs\Api\Entity\TrafficArea\TrafficArea();
        $trafficArea->setId('TA');

        /** @var LicenceEntity $licence */
        $licence = $this->mapReference(LicenceEntity::class, $licenceId);
        $licence->setLicenceType(new RefData());
        $licence->setTrafficArea($trafficArea);
        $licence->setOperatingCentres(new \Doctrine\Common\Collections\ArrayCollection());

        /* @var $application ApplicationEntity */
        $application = $this->mapReference(ApplicationEntity::class, $applicationId);
        $application->setLicence($licence);
        $application->setStatus($this->mapRefdata(ApplicationEntity::APPLICATION_STATUS_NOT_SUBMITTED));
        $application->setIsVariation($isVariation);
        $application->setLicenceType($this->mapRefdata(LicenceEntity::LICENCE_TYPE_STANDARD_INTERNATIONAL));
        $application->setS4s(new \Doctrine\Common\Collections\ArrayCollection());
        $application->setOperatingCentres(new \Doctrine\Common\Collections\ArrayCollection());
        $application->setApplicationCompletion(new ApplicationCompletion($application));

        $expectedTargetCompletionDate = clone $now;
        $expectedTargetCompletionDate->modify('+7 week');
        $application
            ->shouldReceive('setStatus')
            ->with($this->mapRefdata(ApplicationEntity::APPLICATION_STATUS_UNDER_CONSIDERATION))
            ->andReturnSelf()
            ->shouldReceive('getCode')
            ->andReturn('TEST CODE');

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

            if (!$isInternalUser) {
                $this->expectedSideEffect(
                    \Dvsa\Olcs\Transfer\Command\Publication\Application::class,
                    ['id' => 69, 'trafficArea' => 'TA'],
                    new Result()
                );
                $this->expectedSideEffect(
                    \Dvsa\Olcs\Api\Domain\Command\Application\CreateTexTask::class,
                    ['id' => 69],
                    new Result()
                );
            }
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
            'actionDate' => $now->format('Y-m-d'),
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

        $result1 = new Result();
        $result1->addMessage('Snapshot created');
        $this->expectedSideEffect(CreateSnapshot::class, ['id' => 69, 'event' => CreateSnapshot::ON_SUBMIT], $result1);

        $result = $this->sut->handleCommand($command);

        $this->assertEquals($expectedTargetCompletionDate, $application->getTargetCompletionDate());
        $this->assertEquals($now, $application->getReceivedDate());

        $this->assertEquals($expected, $result->toArray());
    }

    public function testHandleCommandVariationPsv()
    {
        $this->setupIsInternalUser(false);

        $applicationId = 69;
        $version       = 10;
        $licenceId     = 7;
        $taskId        = 111;
        $now           = new DateTime();

        $command = Cmd::create(
            [
                'id' => $applicationId,
                'version' => $version,
            ]
        );

        $trafficArea = new \Dvsa\Olcs\Api\Entity\TrafficArea\TrafficArea();
        $trafficArea->setId('TA');

        /** @var LicenceEntity $licence */
        $licence = $this->mapReference(LicenceEntity::class, $licenceId);
        $licence->setLicenceType(new RefData());
        $licence->setTrafficArea($trafficArea);
        $licence->setOperatingCentres(new \Doctrine\Common\Collections\ArrayCollection());

        /* @var $application ApplicationEntity */
        $application = $this->mapReference(ApplicationEntity::class, $applicationId);
        $application->setLicence($licence);
        $application->setStatus($this->mapRefdata(ApplicationEntity::APPLICATION_STATUS_NOT_SUBMITTED));
        $application->setIsVariation(true);
        $application->setLicenceType($this->mapRefdata(LicenceEntity::LICENCE_TYPE_STANDARD_INTERNATIONAL));
        $application->setS4s(new \Doctrine\Common\Collections\ArrayCollection());
        $application->setOperatingCentres(new \Doctrine\Common\Collections\ArrayCollection());
        $application->setGoodsOrPsv(new RefData(LicenceEntity::LICENCE_CATEGORY_PSV));
        $application->setApplicationCompletion(new ApplicationCompletion($application));

        $expectedTargetCompletionDate = clone $now;
        $expectedTargetCompletionDate->modify('+7 week');
        $application
            ->shouldReceive('setStatus')
            ->with($this->mapRefdata(ApplicationEntity::APPLICATION_STATUS_UNDER_CONSIDERATION))
            ->andReturnSelf()
            ->shouldReceive('getCode')
            ->andReturn('TEST CODE');

        // licence status should be updated if application is not a variation
        $licence
            ->shouldReceive('setStatus')
            ->never();

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
            'actionDate' => $now->format('Y-m-d'),
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

        $result1 = new Result();
        $result1->addMessage('Snapshot created');
        $this->expectedSideEffect(CreateSnapshot::class, ['id' => 69, 'event' => CreateSnapshot::ON_SUBMIT], $result1);

        $result = $this->sut->handleCommand($command);

        $this->assertEquals($expectedTargetCompletionDate, $application->getTargetCompletionDate());
        $this->assertEquals($now, $application->getReceivedDate());
        $expected = [
            'id' => [
                'application' => 69,
                'task' => 111,
            ],
            'messages' => [
                'Snapshot created',
                'Application updated',
                'task created'
            ],
        ];

        $this->assertEquals($expected, $result->toArray());
    }

    public function testHandleCommandSpecialRestricted()
    {
        $this->setupIsInternalUser(false);

        $applicationId = 69;
        $version       = 10;
        $licenceId     = 7;
        $taskId        = 111;
        $now           = new DateTime();

        $expected = [
            'id' => [
                'application' => 69,
                'licence' => 7,
                'task' => 111,
            ],
            'messages' => [
                'Snapshot created',
                'Application updated',
                'Licence updated',
                'task created',
            ],
        ];

        $command = Cmd::create(
            [
                'id' => $applicationId,
                'version' => $version,
            ]
        );

        $trafficArea = new \Dvsa\Olcs\Api\Entity\TrafficArea\TrafficArea();
        $trafficArea->setId('TA');

        /** @var LicenceEntity $licence */
        $licence = $this->mapReference(LicenceEntity::class, $licenceId);
        $licence->setLicenceType(new RefData());
        $licence->setTrafficArea($trafficArea);
        $licence->setOperatingCentres(new \Doctrine\Common\Collections\ArrayCollection());

        /** @var ApplicationEntity $application */
        $application = $this->mapReference(ApplicationEntity::class, $applicationId);
        $application->setIsVariation(false);
        $application->setLicence($licence);
        $application->setStatus($this->mapRefdata(ApplicationEntity::APPLICATION_STATUS_NOT_SUBMITTED));
        $application->setLicenceType($this->mapRefdata(LicenceEntity::LICENCE_TYPE_SPECIAL_RESTRICTED));
        $application->setS4s(new \Doctrine\Common\Collections\ArrayCollection());
        $application->setOperatingCentres(new \Doctrine\Common\Collections\ArrayCollection());
        $application->setGoodsOrPsv($this->mapRefdata(LicenceEntity::LICENCE_CATEGORY_PSV));

        $expectedTargetCompletionDate = clone $now;
        $expectedTargetCompletionDate->modify('+7 week');
        $application
            ->shouldReceive('setStatus')
            ->with($this->mapRefdata(ApplicationEntity::APPLICATION_STATUS_UNDER_CONSIDERATION))
            ->andReturnSelf()
            ->shouldReceive('getCode')
            ->andReturn('TEST CODE');

        // licence status should be updated if application is not a variation
        $licence
            ->shouldReceive('setStatus')
            ->with($this->mapRefdata(LicenceEntity::LICENCE_STATUS_UNDER_CONSIDERATION))
            ->once()
            ->andReturnSelf();

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
            'actionDate' => $now->format('Y-m-d'),
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

        $result1 = new Result();
        $result1->addMessage('Snapshot created');
        $this->expectedSideEffect(CreateSnapshot::class, ['id' => 69, 'event' => CreateSnapshot::ON_SUBMIT], $result1);

        $result = $this->sut->handleCommand($command);

        $this->assertEquals($expectedTargetCompletionDate, $application->getTargetCompletionDate());
        $this->assertEquals($now, $application->getReceivedDate());

        $this->assertEquals($expected, $result->toArray());
    }

    /**
     * @param boolean $isVariation
     * @param array $expected
     * @dataProvider isVariationProvider
     */
    public function testHandleCommandWithS4($isVariation, $expected)
    {
        $this->setupIsInternalUser(false);

        $applicationId = 69;
        $version       = 10;
        $licenceId     = 7;
        $taskId        = 111;
        $now           = new DateTime();

        $command = Cmd::create(
            [
                'id' => $applicationId,
                'version' => $version,
            ]
        );

        $trafficArea = new \Dvsa\Olcs\Api\Entity\TrafficArea\TrafficArea();
        $trafficArea->setId('TA');

        /** @var LicenceEntity $licence */
        $licence = $this->mapReference(LicenceEntity::class, $licenceId);
        $licence->setTrafficArea($trafficArea);

        /** @var ApplicationEntity $application */
        $application = $this->mapReference(ApplicationEntity::class, $applicationId);
        $application->setLicence($licence);
        $application->setStatus($this->mapRefdata(ApplicationEntity::APPLICATION_STATUS_NOT_SUBMITTED));
        $application->setIsVariation($isVariation);
        $application->setLicenceType($this->mapRefdata(LicenceEntity::LICENCE_TYPE_STANDARD_NATIONAL));
        $application->setApplicationCompletion(new ApplicationCompletion($application));

        $s4 = new \Dvsa\Olcs\Api\Entity\Application\S4($application, $licence);
        $s4->setOutcome($this->mapRefdata(\Dvsa\Olcs\Api\Entity\Application\S4::STATUS_APPROVED));
        $application->setS4s(new \Doctrine\Common\Collections\ArrayCollection([$s4]));

        $expectedTargetCompletionDate = clone $now;
        $expectedTargetCompletionDate->modify('+7 week');
        $application
            ->shouldReceive('setStatus')
            ->with($this->mapRefdata(ApplicationEntity::APPLICATION_STATUS_UNDER_CONSIDERATION))
            ->andReturnSelf()
            ->shouldReceive('getCode')
            ->andReturn('TEST CODE');

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
            'actionDate' => $now->format('Y-m-d'),
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

        $result1 = new Result();
        $result1->addMessage('Snapshot created');
        $this->expectedSideEffect(CreateSnapshot::class, ['id' => 69, 'event' => CreateSnapshot::ON_SUBMIT], $result1);

        $result = $this->sut->handleCommand($command);

        $this->assertEquals($expectedTargetCompletionDate, $application->getTargetCompletionDate());
        $this->assertEquals($now, $application->getReceivedDate());

        $this->assertEquals($expected, $result->toArray());
    }

    /**
     * @dataProvider dataProviderApplicationCompletion
     */
    public function testVariationTasksCreated(
        $applicationCompletion,
        $isLtd,
        $expectedDescription,
        $expectedSubCategory
    ) {
        $this->setupIsInternalUser(false);

        $applicationId = 69;
        $version       = 10;
        $licenceId     = 7;
        $taskId        = 111;
        $now           = new DateTime();

        $command = Cmd::create(
            [
                'id' => $applicationId,
                'version' => $version,
            ]
        );

        $trafficArea = new \Dvsa\Olcs\Api\Entity\TrafficArea\TrafficArea();
        $trafficArea->setId('TA');

        /** @var LicenceEntity $licence */
        $licence = $this->mapReference(LicenceEntity::class, $licenceId);
        $licence->setTrafficArea($trafficArea);
        $licence->shouldReceive('getOrganisation->isLtd')->with()->andReturn($isLtd);

        /** @var ApplicationEntity $application */
        $application = $this->mapReference(ApplicationEntity::class, $applicationId);
        $application->setLicence($licence);
        $application->setStatus($this->mapRefdata(ApplicationEntity::APPLICATION_STATUS_NOT_SUBMITTED));
        $application->setIsVariation(true);
        $application->setLicenceType($this->mapRefdata(LicenceEntity::LICENCE_TYPE_STANDARD_NATIONAL));
        $application->shouldReceive('getVariationCompletion')->with()->andReturn($applicationCompletion);

        $s4 = new \Dvsa\Olcs\Api\Entity\Application\S4($application, $licence);
        $s4->setOutcome($this->mapRefdata(\Dvsa\Olcs\Api\Entity\Application\S4::STATUS_APPROVED));
        $application->setS4s(new \Doctrine\Common\Collections\ArrayCollection([$s4]));

        $expectedTargetCompletionDate = clone $now;
        $expectedTargetCompletionDate->modify('+7 week');
        $application
            ->shouldReceive('setStatus')
            ->with($this->mapRefdata(ApplicationEntity::APPLICATION_STATUS_UNDER_CONSIDERATION))
            ->andReturnSelf()
            ->shouldReceive('getCode')
            ->andReturn('TEST CODE');

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
            'subCategory' => $expectedSubCategory,
            'description' => $expectedDescription,
            'actionDate' => $now->format('Y-m-d'),
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

        $result1 = new Result();
        $result1->addMessage('Snapshot created');
        $this->expectedSideEffect(CreateSnapshot::class, ['id' => 69, 'event' => CreateSnapshot::ON_SUBMIT], $result1);

        $result = $this->sut->handleCommand($command);

        $this->assertEquals($expectedTargetCompletionDate, $application->getTargetCompletionDate());
        $this->assertEquals($now, $application->getReceivedDate());
    }

    public function dataProviderApplicationCompletion()
    {
        return [
            'peopleOnlyLtd' => [
                [
                    ApplicationCompletion::SECTION_PEOPLE => ApplicationCompletion::STATUS_COMPLETE,
                    ApplicationCompletion::SECTION_TRANSPORT_MANAGER => ApplicationCompletion::STATUS_NOT_STARTED,
                    ApplicationCompletion::SECTION_FINANCIAL_HISTORY => ApplicationCompletion::STATUS_COMPLETE,
                ],
                true,
                'Director change application',
                \Dvsa\Olcs\Api\Entity\System\Category::TASK_SUB_CATEGORY_DIRECTOR_CHANGE_DIGITAL
            ],
            'peopleOnlyLtdFail' => [
                [
                    ApplicationCompletion::SECTION_TRANSPORT_MANAGER => ApplicationCompletion::STATUS_COMPLETE,
                    ApplicationCompletion::SECTION_PEOPLE => ApplicationCompletion::STATUS_COMPLETE,
                    ApplicationCompletion::SECTION_FINANCIAL_HISTORY => ApplicationCompletion::STATUS_COMPLETE,
                ],
                true,
                'TEST CODE Application',
                \Dvsa\Olcs\Api\Entity\System\Category::TASK_SUB_CATEGORY_APPLICATION_FORMS_DIGITAL
            ],
            'peopleOnlyOther' => [
                [
                    ApplicationCompletion::SECTION_PEOPLE => ApplicationCompletion::STATUS_COMPLETE,
                    ApplicationCompletion::SECTION_TRANSPORT_MANAGER => ApplicationCompletion::STATUS_NOT_STARTED,
                    ApplicationCompletion::SECTION_FINANCIAL_HISTORY => ApplicationCompletion::STATUS_COMPLETE,
                    ApplicationCompletion::SECTION_DECLARATION_INTERNAL => ApplicationCompletion::STATUS_COMPLETE,
                    ApplicationCompletion::SECTION_DECLARATION => ApplicationCompletion::STATUS_COMPLETE,
                    ApplicationCompletion::SECTION_CONVICTIONS_AND_PENALTIES => ApplicationCompletion::STATUS_COMPLETE,
                ],
                false,
                'Partner change application',
                \Dvsa\Olcs\Api\Entity\System\Category::TASK_SUB_CATEGORY_PARTNER_CHANGE_DIGITAL
            ],
            'peopleOnlyOtherFail' => [
                [
                    ApplicationCompletion::SECTION_PEOPLE => ApplicationCompletion::STATUS_COMPLETE,
                    ApplicationCompletion::SECTION_TRANSPORT_MANAGER => ApplicationCompletion::STATUS_COMPLETE,
                    ApplicationCompletion::SECTION_FINANCIAL_HISTORY => ApplicationCompletion::STATUS_COMPLETE,
                    ApplicationCompletion::SECTION_DECLARATION_INTERNAL => ApplicationCompletion::STATUS_COMPLETE,
                    ApplicationCompletion::SECTION_DECLARATION => ApplicationCompletion::STATUS_COMPLETE,
                    ApplicationCompletion::SECTION_CONVICTIONS_AND_PENALTIES => ApplicationCompletion::STATUS_COMPLETE,
                ],
                true,
                'TEST CODE Application',
                \Dvsa\Olcs\Api\Entity\System\Category::TASK_SUB_CATEGORY_APPLICATION_FORMS_DIGITAL
            ],
            'tmOnly' => [
                [
                    ApplicationCompletion::SECTION_TRANSPORT_MANAGER => ApplicationCompletion::STATUS_COMPLETE,
                    ApplicationCompletion::SECTION_FINANCIAL_HISTORY => ApplicationCompletion::STATUS_COMPLETE,
                ],
                true,
                'TM change variation',
                \Dvsa\Olcs\Api\Entity\System\Category::TASK_SUB_CATEGORY_APPLICATION_TM1_DIGITAL
            ],
            'tmOnlyFail' => [
                [
                    ApplicationCompletion::SECTION_PEOPLE => ApplicationCompletion::STATUS_COMPLETE,
                    ApplicationCompletion::SECTION_TRANSPORT_MANAGER => ApplicationCompletion::STATUS_COMPLETE,
                    ApplicationCompletion::SECTION_FINANCIAL_HISTORY => ApplicationCompletion::STATUS_COMPLETE,
                    ApplicationCompletion::SECTION_DECLARATION_INTERNAL => ApplicationCompletion::STATUS_COMPLETE,
                    ApplicationCompletion::SECTION_DECLARATION => ApplicationCompletion::STATUS_COMPLETE,
                    ApplicationCompletion::SECTION_CONVICTIONS_AND_PENALTIES => ApplicationCompletion::STATUS_COMPLETE,
                ],
                true,
                'TEST CODE Application',
                \Dvsa\Olcs\Api\Entity\System\Category::TASK_SUB_CATEGORY_APPLICATION_FORMS_DIGITAL
            ],
        ];
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
                        'Snapshot created',
                        'Application updated',
                        'Licence updated',
                        'task created',
                    ],
                ],
                LicenceEntity::LICENCE_TYPE_STANDARD_NATIONAL,
                false
            ],
            'variation' => [
                true,
                [
                    'id' => [
                        'application' => 69,
                        'task' => 111,
                    ],
                    'messages' => [
                        'Snapshot created',
                        'Application updated',
                        'task created'
                    ],
                ],
                LicenceEntity::LICENCE_TYPE_STANDARD_NATIONAL,
                false
            ],
            'new app internal' => [
                false,
                [
                    'id' => [
                        'application' => 69,
                        'licence' => 7,
                        'task' => 111,
                    ],
                    'messages' => [
                        'Snapshot created',
                        'Application updated',
                        'Licence updated',
                        'task created',
                    ],
                ],
                LicenceEntity::LICENCE_TYPE_STANDARD_NATIONAL,
                true
            ]
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
