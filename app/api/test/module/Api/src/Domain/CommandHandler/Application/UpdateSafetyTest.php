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

/**
 * Update Safety Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class UpdateSafetyTest extends CommandHandlerTestCase
{
    public function setUp(): void
    {
        $this->sut = new UpdateSafety();
        $this->mockRepo('Application', Application::class);
        $this->mockRepo('Licence', \Dvsa\Olcs\Api\Entity\Licence\Licence::class);

        parent::setUp();
    }

    protected function initReferences()
    {
        $this->refData = ['tach_ext'];

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

        $this->expectException(ValidationException::class);

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

        $licence = new \Dvsa\Olcs\Api\Entity\Licence\Licence(
            new \Dvsa\Olcs\Api\Entity\Organisation\Organisation(),
            new \Dvsa\Olcs\Api\Entity\System\RefData()
        );

        /** @var ApplicationEntity $application */
        $application = m::mock(ApplicationEntity::class)->makePartial();
        $application->setLicence($licence);
        $application->setTotAuthTrailers(12);

        $this->repoMap['Application']->shouldReceive('fetchUsingId')
            ->with($command, Query::HYDRATE_OBJECT, 1)
            ->andReturn($application)
            ->shouldReceive('save')
            ->with($application);

        $this->repoMap['Licence']->shouldReceive('save')->with($licence)->once();

        $expectedData = [
            'id' => 111,
            'section' => 'safety'
        ];
        $result2 = new Result();
        $result2->addMessage('Section updated');

        $this->expectedSideEffect(UpdateApplicationCompletion::class, $expectedData, $result2);

        $result = $this->sut->handleCommand($command);

        $this->assertSame($licence->getSafetyInsVehicles(), 2);
        $this->assertSame($licence->getSafetyInsTrailers(), 3);
        $this->assertSame($licence->getTachographIns(), $this->refData['tach_ext']);
        $this->assertSame($licence->getTachographInsName(), 'Some name');
        $this->assertSame($licence->getSafetyInsVaries(), 'Y');

        $expected = [
            'id' => [],
            'messages' => [
                'Application updated',
                'Section updated'
            ]
        ];

        $this->assertEquals($expected, $result->toArray());
    }

    public function testHandleCommandNoTotAuthTrailers()
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

        $licence = new \Dvsa\Olcs\Api\Entity\Licence\Licence(
            new \Dvsa\Olcs\Api\Entity\Organisation\Organisation(),
            new \Dvsa\Olcs\Api\Entity\System\RefData()
        );

        /** @var ApplicationEntity $application */
        $application = m::mock(ApplicationEntity::class)->makePartial();
        $application->setLicence($licence);
        $application->setTotAuthTrailers(0);

        $this->repoMap['Application']->shouldReceive('fetchUsingId')
            ->with($command, Query::HYDRATE_OBJECT, 1)
            ->andReturn($application)
            ->shouldReceive('save')
            ->with($application);

        $this->repoMap['Licence']->shouldReceive('save')->with($licence)->once();

        $expectedData = [
            'id' => 111,
            'section' => 'safety'
        ];
        $result2 = new Result();
        $result2->addMessage('Section updated');

        $this->expectedSideEffect(UpdateApplicationCompletion::class, $expectedData, $result2);

        $result = $this->sut->handleCommand($command);

        $this->assertSame($licence->getSafetyInsVehicles(), 2);
        $this->assertSame($licence->getSafetyInsTrailers(), 0);
        $this->assertSame($licence->getTachographIns(), $this->refData['tach_ext']);
        $this->assertSame($licence->getTachographInsName(), 'Some name');
        $this->assertSame($licence->getSafetyInsVaries(), 'Y');

        $expected = [
            'id' => [],
            'messages' => [
                'Application updated',
                'Section updated'
            ]
        ];

        $this->assertEquals($expected, $result->toArray());
    }
}
