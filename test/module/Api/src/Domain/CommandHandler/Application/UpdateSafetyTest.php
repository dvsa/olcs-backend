<?php

/**
 * Update Safety Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Application;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\Command\Application\UpdateApplicationCompletion;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\Exception\ValidationException;
use Mockery as m;
use Dvsa\Olcs\Api\Domain\CommandHandler\Application\UpdateSafety;
use Dvsa\Olcs\Api\Domain\Repository\Application;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Transfer\Command\Application\UpdateSafety as Cmd;
use Dvsa\Olcs\Api\Entity\Application\Application as ApplicationEntity;
use Dvsa\Olcs\Transfer\Command\Licence\UpdateSafety as LicenceUpdateSafety;

/**
 * Update Safety Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class UpdateSafetyTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new UpdateSafety();
        $this->mockRepo('Application', Application::class);

        parent::setUp();
    }

    protected function initReferences()
    {
        $this->refData = [];

        $this->references = [];

        parent::initReferences();
    }

    public function testHandleCommandWithValidationException()
    {
        $data = [
            'id' => 111,
            'version' => 1,
            'safetyConfirmation' => 'N',
            'partial' => false,
            'licence' => [
                'id' => 222,
                'version' => 1,
                'safetyInsVehicles' => 2,
                'safetyInsTrailers' => 3,
                'safetyInsVaries' => 'Y',
                'tachographIns' => 'tach_ext',
                'tachographInsName' => 'Some name'
            ]
        ];
        $command = Cmd::create($data);

        /** @var ApplicationEntity $application */
        $application = m::mock(ApplicationEntity::class)->makePartial();

        $this->repoMap['Application']->shouldReceive('fetchUsingId')
            ->with($command, Query::HYDRATE_OBJECT, 1)
            ->andReturn($application);

        $this->setExpectedException(ValidationException::class);

        $this->sut->handleCommand($command);
    }

    public function testHandleCommand()
    {
        $data = [
            'id' => 111,
            'version' => 1,
            'safetyConfirmation' => 'Y',
            'partial' => false,
            'licence' => [
                'id' => 222,
                'version' => 1,
                'safetyInsVehicles' => 2,
                'safetyInsTrailers' => 3,
                'safetyInsVaries' => 'Y',
                'tachographIns' => 'tach_ext',
                'tachographInsName' => 'Some name'
            ]
        ];
        $command = Cmd::create($data);

        /** @var ApplicationEntity $application */
        $application = m::mock(ApplicationEntity::class)->makePartial();

        $this->repoMap['Application']->shouldReceive('fetchUsingId')
            ->with($command, Query::HYDRATE_OBJECT, 1)
            ->andReturn($application)
            ->shouldReceive('save')
            ->with($application);

        $expectedData = [
            'id' => 222,
            'version' => 1,
            'safetyInsVehicles' => 2,
            'safetyInsTrailers' => 0,
            'safetyInsVaries' => 'Y',
            'tachographIns' => 'tach_ext',
            'tachographInsName' => 'Some name'
        ];
        $result1 = new Result();
        $result1->addMessage('Safety updated');

        $this->expectedSideEffect(LicenceUpdateSafety::class, $expectedData, $result1);

        $expectedData = [
            'id' => 111,
            'section' => 'safety'
        ];
        $result2 = new Result();
        $result2->addMessage('Section updated');

        $this->expectedSideEffect(UpdateApplicationCompletion::class, $expectedData, $result2);

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [],
            'messages' => [
                'Application updated',
                'Safety updated',
                'Section updated'
            ]
        ];

        $this->assertEquals($expected, $result->toArray());
    }
}
