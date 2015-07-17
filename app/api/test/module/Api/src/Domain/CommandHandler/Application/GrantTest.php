<?php

/**
 * Grant Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Application;

use Dvsa\Olcs\Api\Domain\Command\Application\GrantGoods;
use Dvsa\Olcs\Api\Domain\Command\Application\GrantPsv;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\Application\Grant;
use Dvsa\Olcs\Api\Domain\Exception\ValidationException;
use Dvsa\Olcs\Api\Entity\Application\Application as ApplicationEntity;
use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Dvsa\Olcs\Transfer\Command\Application\CreateSnapshot;
use Dvsa\Olcs\Transfer\Command\InspectionRequest\CreateFromGrant;
use Mockery as m;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Transfer\Command\Application\Grant as Cmd;

/**
 * Grant Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class GrantTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new Grant();
        $this->mockRepo('Application', \Dvsa\Olcs\Api\Domain\Repository\Application::class);

        parent::setUp();
    }

    public function testHandleCommandWithException()
    {
        $this->setExpectedException(ValidationException::class);

        $data = [
            'shouldCreateInspectionRequest' => 'Y',
            'dueDate' => null
        ];

        $command = Cmd::create($data);

        $this->sut->handleCommand($command);
    }

    public function testHandleCommandGoods()
    {
        $data = [
            'shouldCreateInspectionRequest' => 'N',
            'dueDate' => null,
            'id' => 111,
            'notes' => null
        ];

        $command = Cmd::create($data);

        /** @var ApplicationEntity $application */
        $application = m::mock(ApplicationEntity::class)->makePartial();
        $application->shouldReceive('isGoods')
            ->andReturn(true);

        $this->repoMap['Application']->shouldReceive('fetchUsingId')
            ->with($command)
            ->andReturn($application);

        $result1 = new Result();
        $result1->addMessage('GrantGoods');
        $this->expectedSideEffect(GrantGoods::class, $data, $result1);

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [],
            'messages' => [
                'GrantGoods'
            ]
        ];

        $this->assertEquals($expected, $result->toArray());
    }

    public function testHandleCommandPsv()
    {
        $data = [
            'shouldCreateInspectionRequest' => 'N',
            'dueDate' => null,
            'id' => 111,
            'notes' => null
        ];

        $command = Cmd::create($data);

        /** @var ApplicationEntity $application */
        $application = m::mock(ApplicationEntity::class)->makePartial();
        $application->shouldReceive('isGoods')
            ->andReturn(false);

        $this->repoMap['Application']->shouldReceive('fetchUsingId')
            ->with($command)
            ->andReturn($application);

        $result1 = new Result();
        $result1->addMessage('GrantPsv');
        $this->expectedSideEffect(GrantPsv::class, $data, $result1);

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [],
            'messages' => [
                'GrantPsv'
            ]
        ];

        $this->assertEquals($expected, $result->toArray());
    }

    public function testHandleCommandPsvWithInspectionRequest()
    {
        $data = [
            'shouldCreateInspectionRequest' => 'Y',
            'dueDate' => 3,
            'id' => 111,
            'notes' => 'Notes go here'
        ];

        $command = Cmd::create($data);

        /** @var ApplicationEntity $application */
        $application = m::mock(ApplicationEntity::class)->makePartial();
        $application->setId(111);
        $application->shouldReceive('isGoods')
            ->andReturn(false);

        $this->repoMap['Application']->shouldReceive('fetchUsingId')
            ->with($command)
            ->andReturn($application);

        $result1 = new Result();
        $result1->addMessage('GrantPsv');
        $this->expectedSideEffect(GrantPsv::class, $data, $result1);

        $result2 = new Result();
        $result2->addMessage('CreateFromGrant');
        $data = [
            'application' => 111,
            'duePeriod' => 3,
            'caseworkerNotes' => 'Notes go here'
        ];
        $this->expectedSideEffect(CreateFromGrant::class, $data, $result2);

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [],
            'messages' => [
                'GrantPsv',
                'CreateFromGrant'
            ]
        ];

        $this->assertEquals($expected, $result->toArray());
    }
}
