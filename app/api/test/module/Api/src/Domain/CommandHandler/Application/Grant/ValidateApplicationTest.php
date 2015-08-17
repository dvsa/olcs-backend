<?php

/**
 * Validate Application Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Application\Grant;

use Dvsa\Olcs\Api\Domain\Command\Application\Grant\CommonGrant;
use Dvsa\Olcs\Api\Domain\Command\Application\Grant\CopyApplicationDataToLicence;
use Dvsa\Olcs\Api\Domain\Command\Application\Grant\CreateDiscRecords;
use Dvsa\Olcs\Api\Domain\Command\Application\Grant\ProcessApplicationOperatingCentres;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\Application\Grant\ValidateApplication;
use Dvsa\Olcs\Api\Entity\Application\Application as ApplicationEntity;
use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Dvsa\Olcs\Transfer\Command\Application\CreateSnapshot;
use Mockery as m;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Api\Domain\Command\Application\Grant\ValidateApplication as Cmd;

/**
 * Validate Application Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class ValidateApplicationTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new ValidateApplication();
        $this->mockRepo('Application', \Dvsa\Olcs\Api\Domain\Repository\Application::class);

        parent::setUp();
    }

    public function testHandleCommand()
    {
        $data = [
            'id' => 111
        ];

        $command = Cmd::create($data);

        /** @var Licence $licence */
        $licence = m::mock(Licence::class)->makePartial();
        $licence->setTotAuthVehicles(10);

        /** @var ApplicationEntity $application */
        $application = m::mock(ApplicationEntity::class)->makePartial();
        $application->setLicence($licence);
        $application->setId(111);

        $this->repoMap['Application']->shouldReceive('fetchUsingId')
            ->with($command)
            ->andReturn($application)
            ->shouldReceive('save')
            ->once()
            ->with($application);

        $result1 = new Result();
        $result1->addMessage('CreateSnapshot');
        $this->expectedSideEffect(CreateSnapshot::class, ['id' => 111, 'event' => CreateSnapshot::ON_GRANT], $result1);

        $result2 = new Result();
        $result2->addMessage('CopyApplicationDataToLicence');
        $this->expectedSideEffect(CopyApplicationDataToLicence::class, $data, $result2);

        $result3 = new Result();
        $result3->addMessage('ProcessApplicationOperatingCentres');
        $this->expectedSideEffect(ProcessApplicationOperatingCentres::class, $data, $result3);

        $result4 = new Result();
        $result4->addMessage('CommonGrant');
        $this->expectedSideEffect(CommonGrant::class, $data, $result4);

        $result5 = new Result();
        $result5->addMessage('CreateDiscRecords');
        $discData = $data;
        $discData['currentTotAuth'] = 10;
        $this->expectedSideEffect(CreateDiscRecords::class, $discData, $result5);

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [],
            'messages' => [
                'CreateSnapshot',
                'CopyApplicationDataToLicence',
                'ProcessApplicationOperatingCentres',
                'CommonGrant',
                'CreateDiscRecords'
            ]
        ];

        $this->assertEquals($expected, $result->toArray());
    }
}
