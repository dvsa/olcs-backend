<?php

/**
 * Update Type Of Licence Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Application;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\Command\Application\CreateApplicationFee as CreateApplicationFeeCommand;
use Dvsa\Olcs\Api\Domain\Command\Application\GenerateLicenceNumber as GenerateLicenceNumberCommand;
use Dvsa\Olcs\Api\Domain\Command\Application\ResetApplication as ResetApplicationCommand;
use Dvsa\Olcs\Api\Domain\Command\Application\UpdateApplicationCompletion as UpdateApplicationCompletionCommand;
use Dvsa\Olcs\Api\Domain\Command\Licence\CancelLicenceFees;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\Application\UpdateTypeOfLicence;
use Dvsa\Olcs\Api\Domain\Repository\Application;
use Dvsa\Olcs\Api\Entity\Application\Application as ApplicationEntity;
use Dvsa\Olcs\Api\Entity\Fee\Fee as FeeEntity;
use Dvsa\Olcs\Api\Entity\Fee\FeeType as FeeTypeEntity;
use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Dvsa\Olcs\Transfer\Command\Application\UpdateTypeOfLicence as Cmd;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Mockery as m;

/**
 * Update Type Of Licence Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class UpdateTypeOfLicenceTest extends CommandHandlerTestCase
{
    public function setUp(): void
    {
        $this->sut = new UpdateTypeOfLicence();
        $this->mockRepo('Application', Application::class);

        parent::setUp();
    }

    protected function initReferences()
    {
        $this->refData = [
            Licence::LICENCE_CATEGORY_PSV,
            Licence::LICENCE_CATEGORY_GOODS_VEHICLE,
            Licence::LICENCE_TYPE_STANDARD_NATIONAL,
            Licence::LICENCE_TYPE_STANDARD_INTERNATIONAL,
            Licence::LICENCE_TYPE_SPECIAL_RESTRICTED
        ];

        parent::initReferences();
    }

    public function testHandleCommandWithoutChanges()
    {
        // Params
        $command = $this->getCommand(
            'Y',
            Licence::LICENCE_TYPE_STANDARD_NATIONAL,
            Licence::LICENCE_CATEGORY_GOODS_VEHICLE
        );

        // Mocks
        $application = $this->getApplication(
            'Y',
            Licence::LICENCE_TYPE_STANDARD_NATIONAL,
            Licence::LICENCE_CATEGORY_GOODS_VEHICLE
        );

        // Expectations
        $this->repoMap['Application']->shouldReceive('fetchUsingId')
            ->once()
            ->with($command, Query::HYDRATE_OBJECT, 1)
            ->andReturn($application);

        // Assertions
        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [],
            'messages' => ['No updates required']
        ];

        $this->assertInstanceOf(Result::class, $result);
        $this->assertEquals($expected, $result->toArray());
    }

    /**
     * @dataProvider requireReset
     */
    public function testHandleCommandWithReset($command, $application, $resetData)
    {
        // Expectations
        $this->repoMap['Application']->shouldReceive('fetchUsingId')
            ->once()
            ->with($command, Query::HYDRATE_OBJECT, 1)
            ->andReturn($application);

        $resetResult = new Result();
        $this->expectedSideEffect(ResetApplicationCommand::class, $resetData, $resetResult);

        // Assertions
        $result = $this->sut->handleCommand($command);

        $this->assertSame($resetResult, $result);
    }

    public function testHandleCommandFirstTime()
    {
        // Params
        $command = $this->getCommand('Y', Licence::LICENCE_TYPE_STANDARD_NATIONAL, Licence::LICENCE_CATEGORY_PSV);

        // Mocks
        $application = $this->getApplication(null, null, null);

        // Expectations
        $application->shouldReceive('updateTypeOfLicence')
            ->with(
                'Y',
                $this->mapRefData(Licence::LICENCE_CATEGORY_PSV),
                $this->mapRefData(Licence::LICENCE_TYPE_STANDARD_NATIONAL)
            );

        $this->repoMap['Application']->shouldReceive('fetchUsingId')
            ->once()
            ->with($command, Query::HYDRATE_OBJECT, 1)
            ->andReturn($application)
            ->shouldReceive('save')
            ->once()
            ->with($application);

        $result1 = new Result();
        $result1->addId('fee', 222);
        $this->expectedSideEffect(
            CreateApplicationFeeCommand::class,
            ['id' => 111, 'feeTypeFeeType' => null, 'description' => null],
            $result1
        );

        $result2 = new Result();
        $result2->addId('licNo', 333);
        $this->expectedSideEffect(GenerateLicenceNumberCommand::class, ['id' => 111], $result2);

        $result3 = new Result();
        $result3->addMessage('section1 updated');
        $result3->addMessage('section2 updated');
        $this->expectedSideEffect(
            UpdateApplicationCompletionCommand::class,
            ['id' => 111, 'section' => 'typeOfLicence'],
            $result3
        );

        // Assertions
        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [
                'fee' => 222,
                'licNo' => 333
            ],
            'messages' => [
                'section1 updated',
                'section2 updated',
                'Application saved successfully'
            ]
        ];

        $this->assertEquals($expected, $result->toArray());
    }

    public function testHandleCommandWithAllowedUpdate()
    {
        // Params
        $command = $this->getCommand(
            'Y',
            Licence::LICENCE_TYPE_STANDARD_INTERNATIONAL
        );

        $application = $this->getApplication(
            'Y',
            Licence::LICENCE_TYPE_STANDARD_NATIONAL,
            Licence::LICENCE_CATEGORY_GOODS_VEHICLE
        );

        // Expectations
        $application->shouldReceive('updateTypeOfLicence')
            ->once()
            ->with(
                'Y',
                $this->mapRefData(Licence::LICENCE_CATEGORY_GOODS_VEHICLE),
                $this->mapRefData(Licence::LICENCE_TYPE_STANDARD_INTERNATIONAL)
            )
            ->shouldReceive('getLicence')
            ->andReturn(
                m::mock(Licence::class)
                    ->shouldReceive('getId')
                    ->andReturn(222)
                    ->getMock()
            );

        $this->repoMap['Application']->shouldReceive('fetchUsingId')
            ->once()
            ->with($command, Query::HYDRATE_OBJECT, 1)
            ->andReturn($application)
            ->shouldReceive('save')
            ->once()
            ->with($application);

        $result1 = new Result();
        $result1->addMessage('5 fee(s) cancelled');
        $this->expectedSideEffect(CancelLicenceFees::class, ['id' => 222], $result1);

        $result2 = new Result();
        $result2->addId('fee', 222);
        $this->expectedSideEffect(
            CreateApplicationFeeCommand::class,
            ['id' => 111, 'feeTypeFeeType' => null, 'description' => null],
            $result2
        );

        $result3 = new Result();
        $result3->addMessage('section1 updated');
        $result3->addMessage('section2 updated');
        $this->expectedSideEffect(
            UpdateApplicationCompletionCommand::class,
            ['id' => 111, 'section' => 'typeOfLicence'],
            $result3
        );

        // Assertions
        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [
                'fee' => 222
            ],
            'messages' => [
                '5 fee(s) cancelled',
                'section1 updated',
                'section2 updated',
                'Application saved successfully'
            ]
        ];

        $this->assertEquals($expected, $result->toArray());
    }

    public function testHandleCommandWithPartPaidApplicationFee()
    {
        // Params
        $command = $this->getCommand('N', Licence::LICENCE_TYPE_STANDARD_INTERNATIONAL, Licence::LICENCE_CATEGORY_PSV);

        $application = $this->getApplication(
            'N',
            Licence::LICENCE_TYPE_STANDARD_NATIONAL,
            Licence::LICENCE_CATEGORY_PSV
        );

        // Expectations
        $application->shouldReceive('updateTypeOfLicence')
            ->once()
            ->with(
                'N',
                $this->mapRefData(Licence::LICENCE_CATEGORY_PSV),
                $this->mapRefData(Licence::LICENCE_TYPE_STANDARD_INTERNATIONAL)
            )
            ->shouldReceive('getLicence')
            ->andReturn(
                m::mock(Licence::class)
                ->shouldReceive('getId')
                ->andReturn(222)
                ->getMock()
            );

        $this->repoMap['Application']->shouldReceive('fetchUsingId')
            ->once()
            ->with($command, Query::HYDRATE_OBJECT, 1)
            ->andReturn($application)
            ->shouldReceive('save')
            ->once()
            ->with($application);

        $appFee = m::mock(FeeEntity::class);
        $appFee
            ->shouldReceive('getFeeType->getFeeType->getId')
            ->andReturn(FeeTypeEntity::FEE_TYPE_APP);
        $appFee
            ->shouldReceive('isNewApplicationFee')
            ->andReturn(true)
            ->shouldReceive('isPaid')
            ->andReturn(false)
            ->shouldReceive('isPartPaid')
            ->andReturn(true);
        $application->setFees(new ArrayCollection([$appFee]));

        $result = new Result();
        $result->addMessage('section1 updated');
        $result->addMessage('section2 updated');
        $this->expectedSideEffect(
            UpdateApplicationCompletionCommand::class,
            ['id' => 111, 'section' => 'typeOfLicence'],
            $result
        );

        // Assertions
        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [],
            'messages' => [
                'section1 updated',
                'section2 updated',
                'Application saved successfully'
            ]
        ];

        $this->assertEquals($expected, $result->toArray());
    }

    public function requireReset()
    {
        $this->initReferences();
        return [
            'niFlag changed' => [
                $this->getCommand('Y', Licence::LICENCE_TYPE_STANDARD_NATIONAL, Licence::LICENCE_CATEGORY_PSV),
                $this->getApplication('N', Licence::LICENCE_TYPE_STANDARD_NATIONAL, Licence::LICENCE_CATEGORY_PSV),
                [
                    'id' => 111,
                    'niFlag' => 'Y',
                    'operatorType' => Licence::LICENCE_CATEGORY_PSV,
                    'licenceType' => Licence::LICENCE_TYPE_STANDARD_NATIONAL,
                    'confirm' => false
                ]
            ],
            'operatorType changed' => [
                $this->getCommand(
                    'Y',
                    Licence::LICENCE_TYPE_STANDARD_NATIONAL,
                    Licence::LICENCE_CATEGORY_GOODS_VEHICLE,
                    true
                ),
                $this->getApplication('Y', Licence::LICENCE_TYPE_STANDARD_NATIONAL, Licence::LICENCE_CATEGORY_PSV),
                [
                    'id' => 111,
                    'niFlag' => 'Y',
                    'operatorType' => Licence::LICENCE_CATEGORY_GOODS_VEHICLE,
                    'licenceType' => Licence::LICENCE_TYPE_STANDARD_NATIONAL,
                    'confirm' => true
                ]
            ],
            'to SR' => [
                $this->getCommand('Y', Licence::LICENCE_TYPE_SPECIAL_RESTRICTED, Licence::LICENCE_CATEGORY_PSV),
                $this->getApplication('Y', Licence::LICENCE_TYPE_STANDARD_NATIONAL, Licence::LICENCE_CATEGORY_PSV),
                [
                    'id' => 111,
                    'niFlag' => 'Y',
                    'operatorType' => Licence::LICENCE_CATEGORY_PSV,
                    'licenceType' => Licence::LICENCE_TYPE_SPECIAL_RESTRICTED,
                    'confirm' => false
                ]
            ],
            'from SR' => [
                $this->getCommand('Y', Licence::LICENCE_TYPE_STANDARD_NATIONAL, Licence::LICENCE_CATEGORY_PSV),
                $this->getApplication('Y', Licence::LICENCE_TYPE_SPECIAL_RESTRICTED, Licence::LICENCE_CATEGORY_PSV),
                [
                    'id' => 111,
                    'niFlag' => 'Y',
                    'operatorType' => Licence::LICENCE_CATEGORY_PSV,
                    'licenceType' => Licence::LICENCE_TYPE_STANDARD_NATIONAL,
                    'confirm' => false
                ]
            ]
        ];
    }

    protected function getCommand($niFlag, $licenceType, $operatorType = null, $confirm = false)
    {
        $data = [
            'id' => 111,
            'version' => 1,
            'niFlag' => $niFlag,
            'operatorType' => $operatorType,
            'licenceType' => $licenceType,
            'confirm' => $confirm
        ];

        return Cmd::create($data);
    }

    protected function getApplication($niFlag, $licenceType, $operatorType)
    {
        $application = m::mock(ApplicationEntity::class)->makePartial();
        $application->setId(111);
        $application->setNiFlag($niFlag);
        $application->setLicenceType($this->mapRefData($licenceType));
        $application->setGoodsOrPsv($this->mapRefData($operatorType));
        $application->setFees([]);

        return $application;
    }

    public function testHandleCommandWithAllowedUpdateGb()
    {
        // Params
        $command = $this->getCommand(
            'N',
            Licence::LICENCE_TYPE_STANDARD_INTERNATIONAL,
            Licence::LICENCE_CATEGORY_GOODS_VEHICLE
        );

        $application = $this->getApplication(
            'N',
            Licence::LICENCE_TYPE_STANDARD_NATIONAL,
            Licence::LICENCE_CATEGORY_GOODS_VEHICLE
        );

        // Expectations
        $application->shouldReceive('updateTypeOfLicence')
            ->once()
            ->with(
                'N',
                $this->mapRefData(Licence::LICENCE_CATEGORY_GOODS_VEHICLE),
                $this->mapRefData(Licence::LICENCE_TYPE_STANDARD_INTERNATIONAL)
            )
            ->shouldReceive('getLicence')
            ->andReturn(
                m::mock(Licence::class)
                    ->shouldReceive('getId')
                    ->andReturn(222)
                    ->getMock()
            );

        $this->repoMap['Application']->shouldReceive('fetchUsingId')
            ->once()
            ->with($command, Query::HYDRATE_OBJECT, 1)
            ->andReturn($application)
            ->shouldReceive('save')
            ->once()
            ->with($application);

        $result1 = new Result();
        $result1->addMessage('5 fee(s) cancelled');
        $this->expectedSideEffect(CancelLicenceFees::class, ['id' => 222], $result1);

        $result2 = new Result();
        $result2->addId('fee', 222);
        $this->expectedSideEffect(
            CreateApplicationFeeCommand::class,
            ['id' => 111, 'feeTypeFeeType' => null, 'description' => null],
            $result2
        );

        $result3 = new Result();
        $result3->addMessage('section1 updated');
        $result3->addMessage('section2 updated');
        $this->expectedSideEffect(
            UpdateApplicationCompletionCommand::class,
            ['id' => 111, 'section' => 'typeOfLicence'],
            $result3
        );

        // Assertions
        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [
                'fee' => 222
            ],
            'messages' => [
                '5 fee(s) cancelled',
                'section1 updated',
                'section2 updated',
                'Application saved successfully'
            ]
        ];

        $this->assertEquals($expected, $result->toArray());
    }
}
