<?php

/**
 * Create Variation Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Licence;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\Command\Application\CreateFee;
use Dvsa\Olcs\Api\Domain\Command\Application\UpdateApplicationCompletion;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\Licence\CreateVariation;
use Dvsa\Olcs\Api\Entity\Application\Application as ApplicationEntity;
use Dvsa\Olcs\Api\Entity\Fee\FeeType;
use Dvsa\Olcs\Api\Entity\Licence\Licence as LicenceEntity;
use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Dvsa\Olcs\Api\Entity\User\Permission;
use Mockery as m;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use ZfcRbac\Service\AuthorizationService;

/**
 * Create Variation Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class CreateVariationTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new CreateVariation();
        $this->mockRepo('Licence', \Dvsa\Olcs\Api\Domain\Repository\Licence::class);
        $this->mockRepo('Application', \Dvsa\Olcs\Api\Domain\Repository\Application::class);

        $this->mockedSmServices[AuthorizationService::class] = m::mock(AuthorizationService::class);

        parent::setUp();
    }

    protected function initReferences()
    {
        $this->refData = [
            ApplicationEntity::APPLICATION_STATUS_UNDER_CONSIDERATION,
            ApplicationEntity::APPLICATION_STATUS_NOT_SUBMITTED,
            LicenceEntity::LICENCE_TYPE_STANDARD_NATIONAL,
            LicenceEntity::LICENCE_TYPE_STANDARD_INTERNATIONAL,
            LicenceEntity::LICENCE_STATUS_VALID,
            ApplicationEntity::APPLIED_VIA_PHONE,
            ApplicationEntity::APPLIED_VIA_SELFSERVE,
        ];

        parent::initReferences();
    }

    public function testHandleCommandInternalFull()
    {
        $data = [
            'id' => 111,
            'licenceType' => LicenceEntity::LICENCE_TYPE_STANDARD_NATIONAL,
            'receivedDate' => '2015-01-01',
            'feeRequired' => 'Y',
            'appliedVia' => ApplicationEntity::APPLIED_VIA_PHONE
        ];

        $this->mockedSmServices[AuthorizationService::class]->shouldReceive('isGranted')
            ->with(Permission::INTERNAL_USER, null)
            ->andReturn(true)
            ->shouldReceive('isGranted')
            ->once()
            ->with(Permission::SELFSERVE_USER, null)
            ->andReturn(false);

        $command = \Dvsa\Olcs\Transfer\Command\Licence\CreateVariation::create($data);

        $licence = m::mock(LicenceEntity::class)->makePartial();
        $licence->setLicenceType($this->refData[LicenceEntity::LICENCE_TYPE_STANDARD_INTERNATIONAL]);

        $licence->shouldReceive('canHaveVariation')->andReturn(true);

        $this->repoMap['Licence']->shouldReceive('fetchUsingId')
            ->with($command)
            ->andReturn($licence);

        $this->repoMap['Application']->shouldReceive('save')
            ->with(m::type(ApplicationEntity::class))
            ->andReturnUsing(
                function (ApplicationEntity $app) {
                    $app->setId(222);
                    $this->assertEquals('2015-01-01', $app->getReceivedDate()->format('Y-m-d'));
                    $this->assertEquals('2015-03-05', $app->getTargetCompletionDate()->format('Y-m-d'));
                    $this->assertSame(
                        $this->refData[ApplicationEntity::APPLICATION_STATUS_UNDER_CONSIDERATION],
                        $app->getStatus()
                    );
                }
            );

        $data = ['id' => 222, 'section' => 'typeOfLicence'];
        $result1 = new Result();
        $result1->addMessage('UpdateApplicationCompletion');
        $this->expectedSideEffect(UpdateApplicationCompletion::class, $data, $result1);

        $data = ['id' => 222, 'feeTypeFeeType' => FeeType::FEE_TYPE_VAR, 'task' => null];
        $result2 = new Result();
        $result2->addMessage('CreateFee');
        $this->expectedSideEffect(CreateFee::class, $data, $result2);

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [
                'application' => 222
            ],
            'messages' => [
                'Variation created',
                'UpdateApplicationCompletion',
                'CreateFee'
            ]
        ];

        $this->assertEquals($expected, $result->toArray());
    }

    public function testHandleCommandInternalNoFee()
    {
        $data = [
            'id' => 111,
            'licenceType' => LicenceEntity::LICENCE_TYPE_STANDARD_NATIONAL,
            'receivedDate' => '2015-01-01',
            'feeRequired' => 'N',
            'appliedVia' => ApplicationEntity::APPLIED_VIA_PHONE
        ];

        $this->mockedSmServices[AuthorizationService::class]->shouldReceive('isGranted')
            ->with(Permission::INTERNAL_USER, null)
            ->andReturn(true)
            ->shouldReceive('isGranted')
            ->once()
            ->with(Permission::SELFSERVE_USER, null)
            ->andReturn(false);

        $command = \Dvsa\Olcs\Transfer\Command\Licence\CreateVariation::create($data);

        /** @var LicenceEntity $licence */
        $licence = m::mock(LicenceEntity::class)->makePartial();
        $licence->setLicenceType($this->refData[LicenceEntity::LICENCE_TYPE_STANDARD_INTERNATIONAL]);

        $licence->shouldReceive('canHaveVariation')->andReturn(true);

        $this->repoMap['Licence']->shouldReceive('fetchUsingId')
            ->with($command)
            ->andReturn($licence);

        $this->repoMap['Application']->shouldReceive('save')
            ->with(m::type(ApplicationEntity::class))
            ->andReturnUsing(
                function (ApplicationEntity $app) {
                    $app->setId(222);
                    $this->assertEquals('2015-01-01', $app->getReceivedDate()->format('Y-m-d'));
                    $this->assertSame(
                        $this->refData[ApplicationEntity::APPLICATION_STATUS_UNDER_CONSIDERATION],
                        $app->getStatus()
                    );
                }
            );

        $data = ['id' => 222, 'section' => 'typeOfLicence'];
        $result1 = new Result();
        $result1->addMessage('UpdateApplicationCompletion');
        $this->expectedSideEffect(UpdateApplicationCompletion::class, $data, $result1);

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [
                'application' => 222
            ],
            'messages' => [
                'Variation created',
                'UpdateApplicationCompletion'
            ]
        ];

        $this->assertEquals($expected, $result->toArray());
    }

    public function testHandleCommandInternalNoLicenceType()
    {
        $data = [
            'id' => 111,
            'receivedDate' => '2015-01-01',
            'feeRequired' => 'N',
            'appliedVia' => ApplicationEntity::APPLIED_VIA_PHONE
        ];

        $this->mockedSmServices[AuthorizationService::class]->shouldReceive('isGranted')
            ->with(Permission::INTERNAL_USER, null)
            ->andReturn(true)
            ->shouldReceive('isGranted')
            ->once()
            ->with(Permission::SELFSERVE_USER, null)
            ->andReturn(false);

        $command = \Dvsa\Olcs\Transfer\Command\Licence\CreateVariation::create($data);

        /** @var LicenceEntity $licence */
        $licence = m::mock(LicenceEntity::class)->makePartial();
        $licence->setLicenceType($this->refData[LicenceEntity::LICENCE_TYPE_STANDARD_INTERNATIONAL]);

        $licence->shouldReceive('canHaveVariation')->andReturn(true);

        $this->repoMap['Licence']->shouldReceive('fetchUsingId')
            ->with($command)
            ->andReturn($licence);

        $this->repoMap['Application']->shouldReceive('save')
            ->with(m::type(ApplicationEntity::class))
            ->andReturnUsing(
                function (ApplicationEntity $app) {
                    $app->setId(222);
                    $this->assertEquals('2015-01-01', $app->getReceivedDate()->format('Y-m-d'));
                    $this->assertSame(
                        $this->refData[ApplicationEntity::APPLICATION_STATUS_UNDER_CONSIDERATION],
                        $app->getStatus()
                    );
                }
            );

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [
                'application' => 222
            ],
            'messages' => [
                'Variation created'
            ]
        ];

        $this->assertEquals($expected, $result->toArray());
    }

    public function testHandleCommandInternalNoReceivedDate()
    {
        $data = [
            'id' => 111,
            'feeRequired' => 'N',
            'appliedVia' => ApplicationEntity::APPLIED_VIA_PHONE
        ];

        $this->mockedSmServices[AuthorizationService::class]->shouldReceive('isGranted')
            ->with(Permission::INTERNAL_USER, null)
            ->andReturn(true)
            ->shouldReceive('isGranted')
            ->once()
            ->with(Permission::SELFSERVE_USER, null)
            ->andReturn(false);

        $command = \Dvsa\Olcs\Transfer\Command\Licence\CreateVariation::create($data);

        /** @var LicenceEntity $licence */
        $licence = m::mock(LicenceEntity::class)->makePartial();
        $licence->setLicenceType($this->refData[LicenceEntity::LICENCE_TYPE_STANDARD_INTERNATIONAL]);

        $licence->shouldReceive('canHaveVariation')->andReturn(true);

        $this->repoMap['Licence']->shouldReceive('fetchUsingId')
            ->with($command)
            ->andReturn($licence);

        $this->repoMap['Application']->shouldReceive('save')
            ->with(m::type(ApplicationEntity::class))
            ->andReturnUsing(
                function (ApplicationEntity $app) {
                    $app->setId(222);
                    $this->assertNull($app->getReceivedDate());
                    $this->assertSame(
                        $this->refData[ApplicationEntity::APPLICATION_STATUS_UNDER_CONSIDERATION],
                        $app->getStatus()
                    );
                }
            );

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [
                'application' => 222
            ],
            'messages' => [
                'Variation created'
            ]
        ];

        $this->assertEquals($expected, $result->toArray());
    }

    public function testHandleCommandSelfserveNoReceivedDate()
    {
        $data = [
            'id' => 111,
            'feeRequired' => 'N'
        ];

        $this->mockedSmServices[AuthorizationService::class]->shouldReceive('isGranted')
            ->with(Permission::INTERNAL_USER, null)
            ->andReturn(false)
            ->shouldReceive('isGranted')
            ->once()
            ->with(Permission::SELFSERVE_USER, null)
            ->andReturn(true);

        $command = \Dvsa\Olcs\Transfer\Command\Licence\CreateVariation::create($data);

        /** @var LicenceEntity $licence */
        $licence = m::mock(LicenceEntity::class)->makePartial();
        $licence->setLicenceType($this->refData[LicenceEntity::LICENCE_TYPE_STANDARD_INTERNATIONAL]);

        $licence->shouldReceive('canHaveVariation')->andReturn(true);

        $this->repoMap['Licence']->shouldReceive('fetchUsingId')
            ->with($command)
            ->andReturn($licence);

        $this->repoMap['Application']->shouldReceive('save')
            ->with(m::type(ApplicationEntity::class))
            ->andReturnUsing(
                function (ApplicationEntity $app) {
                    $app->setId(222);
                    $this->assertNull($app->getReceivedDate());
                    $this->assertSame(
                        $this->refData[ApplicationEntity::APPLICATION_STATUS_NOT_SUBMITTED],
                        $app->getStatus()
                    );
                }
            );

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [
                'application' => 222
            ],
            'messages' => [
                'Variation created'
            ]
        ];

        $this->assertEquals($expected, $result->toArray());
    }

    public function testHandleCommandVariationException()
    {
        $this->setExpectedException(\Dvsa\Olcs\Api\Domain\Exception\ForbiddenException::class);

        $data = [
            'id' => 111,
            'feeRequired' => 'N'
        ];

        $this->mockedSmServices[AuthorizationService::class]->shouldReceive('isGranted')
            ->with(Permission::INTERNAL_USER, null)
            ->andReturn(false);

        $command = \Dvsa\Olcs\Transfer\Command\Licence\CreateVariation::create($data);

        /** @var LicenceEntity $licence */
        $licence = m::mock(LicenceEntity::class)->makePartial();
        $licence->setLicenceType($this->refData[LicenceEntity::LICENCE_TYPE_STANDARD_INTERNATIONAL]);

        $licence->shouldReceive('canHaveVariation')->andReturn(false);

        $this->repoMap['Licence']->shouldReceive('fetchUsingId')
            ->with($command)
            ->andReturn($licence);

        $this->sut->handleCommand($command);
    }
}
