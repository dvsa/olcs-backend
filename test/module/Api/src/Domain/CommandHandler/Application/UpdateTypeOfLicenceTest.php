<?php

/**
 * Update Type Of Licence Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Application;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Mockery as m;
use Dvsa\Olcs\Api\Domain\CommandHandler\Application\UpdateTypeOfLicence;
use Dvsa\Olcs\Api\Domain\Repository\Application;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Transfer\Command\Application\UpdateTypeOfLicence as Cmd;
use Dvsa\Olcs\Api\Entity\Application\Application as ApplicationEntity;
use Dvsa\Olcs\Api\Domain\Command\Application\ResetApplication as ResetApplicationCommand;

/**
 * Update Type Of Licence Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class UpdateTypeOfLicenceTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new UpdateTypeOfLicence();
        $this->repoMap['Application'] = m::mock(Application::class);

        parent::setUp();
    }

    public function testHandleCommandWithoutChanges()
    {
        // Params
        $command = $this->getCommand('Y', 'lcat_gv', 'ltyp_sn');

        // Mocks
        $operatorType = m::mock();
        $licenceType = m::mock();

        $application = $this->getApplication('Y', $licenceType, $operatorType);

        // Expectations
        $this->repoMap['Application']->shouldReceive('fetchUsingId')
            ->with($command, Query::HYDRATE_OBJECT, 1)
            ->andReturn($application)

            ->shouldReceive('getRefdataReference')
            ->with('lcat_gv')
            ->andReturn($operatorType)

            ->shouldReceive('getRefdataReference')
            ->with('ltyp_sn')
            ->andReturn($licenceType);

        // Assertions
        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [],
            'messages' => ['No updates required']
        ];

        $this->assertInstanceOf(Result::class, $result);
        $this->assertEquals($expected, $result->toArray());
    }

    public function testHandleCommandWithReset()
    {
        // Params
        $command = $this->getCommand('Y', 'lcat_psv', 'ltyp_sn');

        // Mocks
        $gvOperatorType = m::mock();
        $psvOperatorType = m::mock();
        $licenceType = m::mock();

        $application = $this->getApplication('Y', $licenceType, $gvOperatorType);

        // Expectations
        $this->repoMap['Application']->shouldReceive('fetchUsingId')
            ->with($command, Query::HYDRATE_OBJECT, 1)
            ->andReturn($application)

            ->shouldReceive('getRefdataReference')
            ->with('lcat_psv')
            ->andReturn($psvOperatorType)

            ->shouldReceive('getRefdataReference')
            ->with('ltyp_sn')
            ->andReturn($licenceType);

        $resetResult = new Result();
        $resetData = [
            'id' => 111,
            'niFlag' => 'Y',
            'operatorType' => 'lcat_psv',
            'licenceType' => 'ltyp_sn',
            'confirm' => false
        ];
        $this->expectedSideEffect(ResetApplicationCommand::class, $resetData, $resetResult);

        // Assertions
        $result = $this->sut->handleCommand($command);

        $this->assertSame($resetResult, $result);
    }

    protected function getCommand($niFlag, $licenceType, $operatorType)
    {
        $data = [
            'id' => 111,
            'version' => 1,
            'niFlag' => $niFlag,
            'operatorType' => $licenceType,
            'licenceType' => $operatorType
        ];

        return Cmd::create($data);
    }

    protected function getApplication($niFlag, $licenceType, $operatorType)
    {
        $application = m::mock(ApplicationEntity::class)->makePartial();
        $application->setNiFlag($niFlag);
        $application->setLicenceType($licenceType);
        $application->setGoodsOrPsv($operatorType);

        return $application;
    }
}
