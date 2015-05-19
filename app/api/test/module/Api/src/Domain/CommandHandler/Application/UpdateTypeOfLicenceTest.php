<?php

/**
 * Update Type Of Licence Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Application;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Mockery as m;
use Dvsa\Olcs\Api\Domain\CommandHandler\Application\UpdateTypeOfLicence;
use Dvsa\Olcs\Api\Domain\Repository\Application;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Transfer\Command\Application\UpdateTypeOfLicence as Cmd;
use Dvsa\Olcs\Api\Entity\Application\Application as ApplicationEntity;
use Dvsa\Olcs\Api\Domain\Command\Application\ResetApplication as ResetApplicationCommand;
use Dvsa\Olcs\Api\Domain\Command\Application\CreateApplicationFee as CreateApplicationFeeCommand;
use Dvsa\Olcs\Api\Domain\Command\Application\GenerateLicenceNumber as GenerateLicenceNumberCommand;
use Dvsa\Olcs\Api\Domain\Command\Application\UpdateApplicationCompletion as UpdateApplicationCompletionCommand;
use Dvsa\Olcs\Api\Domain\Command\Licence\CancelLicenceFees;

/**
 * Update Type Of Licence Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class UpdateTypeOfLicenceTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->initRefdata();

        $this->sut = new UpdateTypeOfLicence();
        $this->repoMap['Application'] = m::mock(Application::class);
        $this->repoMap['Application']->shouldReceive('getRefdataReference')
            ->andReturnUsing([$this, 'mapRefData']);

        parent::setUp();
    }

    public function initRefdata()
    {
        $this->refData = [
            'lcat_psv' => m::mock(),
            'lcat_gv' => m::mock(),
            'ltyp_sn' => m::mock(),
            'ltyp_si' => m::mock(),
            'ltyp_sr' => m::mock(),
            'ltyp_r' => m::mock()
        ];
    }

    public function testHandleCommandWithoutChanges()
    {
        // Params
        $command = $this->getCommand('Y', 'ltyp_sn', 'lcat_gv');

        // Mocks
        $application = $this->getApplication('Y', 'ltyp_sn', 'lcat_gv');

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
        $command = $this->getCommand('Y', 'ltyp_sn', 'lcat_psv');

        // Mocks
        $application = $this->getApplication(null, null, null);

        // Expectations
        $application->shouldReceive('updateTypeOfLicence')
            ->with('Y', $this->mapRefData('lcat_psv'), $this->mapRefData('ltyp_sn'));

        $this->repoMap['Application']->shouldReceive('fetchUsingId')
            ->once()
            ->with($command, Query::HYDRATE_OBJECT, 1)
            ->andReturn($application)
            ->shouldReceive('beginTransaction')
            ->once()
            ->shouldReceive('save')
            ->once()
            ->with($application)
            ->shouldReceive('commit')
            ->once();

        $result1 = new Result();
        $result1->addId('fee', 222);
        $this->expectedSideEffect(CreateApplicationFeeCommand::class, ['id' => 111], $result1);

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
        $command = $this->getCommand('Y', 'ltyp_si', 'lcat_psv');

        $application = $this->getApplication('Y', 'ltyp_sn', 'lcat_psv');

        // Expectations
        $application->shouldReceive('updateTypeOfLicence')
            ->once()
            ->with('Y', $this->mapRefData('lcat_psv'), $this->mapRefData('ltyp_si'))
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
            ->shouldReceive('beginTransaction')
            ->once()
            ->shouldReceive('save')
            ->once()
            ->with($application)
            ->shouldReceive('commit')
            ->once();

        $result1 = new Result();
        $result1->addMessage('5 fee(s) cancelled');
        $this->expectedSideEffect(CancelLicenceFees::class, ['id' => 222], $result1);

        $result2 = new Result();
        $result2->addId('fee', 222);
        $this->expectedSideEffect(CreateApplicationFeeCommand::class, ['id' => 111], $result2);

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

    public function testHandleCommandWithException()
    {
        // Params
        $command = $this->getCommand('Y', 'ltyp_si', 'lcat_psv');

        $application = $this->getApplication('Y', 'ltyp_sn', 'lcat_psv');

        // Expectations
        $application->shouldReceive('updateTypeOfLicence')
            ->once()
            ->with('Y', $this->mapRefData('lcat_psv'), $this->mapRefData('ltyp_si'))
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
            ->shouldReceive('beginTransaction')
            ->once()
            ->shouldReceive('save')
            ->once()
            ->with($application)
            ->andThrow('\Exception')
            ->shouldReceive('rollback')
            ->once();

        $this->setExpectedException('\Exception');

        // Assertions
        $this->sut->handleCommand($command);
    }

    public function requireReset()
    {
        $this->initRefdata();
        return [
            'niFlag changed' => [
                $this->getCommand('Y', 'ltyp_sn', 'lcat_psv'),
                $this->getApplication('N', 'ltyp_sn', 'lcat_psv'),
                [
                    'id' => 111,
                    'niFlag' => 'Y',
                    'operatorType' => 'lcat_psv',
                    'licenceType' => 'ltyp_sn',
                    'confirm' => false
                ]
            ],
            'operatorType changed' => [
                $this->getCommand('Y', 'ltyp_sn', 'lcat_gv', true),
                $this->getApplication('Y', 'ltyp_sn', 'lcat_psv'),
                [
                    'id' => 111,
                    'niFlag' => 'Y',
                    'operatorType' => 'lcat_gv',
                    'licenceType' => 'ltyp_sn',
                    'confirm' => true
                ]
            ],
            'to SR' => [
                $this->getCommand('Y', 'ltyp_sr', 'lcat_psv'),
                $this->getApplication('Y', 'ltyp_sn', 'lcat_psv'),
                [
                    'id' => 111,
                    'niFlag' => 'Y',
                    'operatorType' => 'lcat_psv',
                    'licenceType' => 'ltyp_sr',
                    'confirm' => false
                ]
            ],
            'from SR' => [
                $this->getCommand('Y', 'ltyp_sn', 'lcat_psv'),
                $this->getApplication('Y', 'ltyp_sr', 'lcat_psv'),
                [
                    'id' => 111,
                    'niFlag' => 'Y',
                    'operatorType' => 'lcat_psv',
                    'licenceType' => 'ltyp_sn',
                    'confirm' => false
                ]
            ]
        ];
    }

    protected function getCommand($niFlag, $licenceType, $operatorType, $confirm = false)
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

        return $application;
    }
}
