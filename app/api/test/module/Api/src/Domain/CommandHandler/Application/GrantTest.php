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
        $application->shouldReceive('getS4s')->andReturn(new \Doctrine\Common\Collections\ArrayCollection());
        $application->shouldReceive('getOverrideOoo')->andReturn('Y');

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
        $application->shouldReceive('getS4s')->andReturn(new \Doctrine\Common\Collections\ArrayCollection());
        $application->shouldReceive('getOverrideOoo')->andReturn('Y');

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
        $application->shouldReceive('getS4s')->andReturn(new \Doctrine\Common\Collections\ArrayCollection());
        $application->shouldReceive('getOverrideOoo')->andReturn('Y');

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

    public function testHandleCommandS4Validation()
    {
        $data = ['id' => 111];

        $command = Cmd::create($data);

        $s4 = m::mock(\Dvsa\Olcs\Api\Entity\Application\S4::class)->makePartial();

        /* @var $application ApplicationEntity */
        $application = m::mock(ApplicationEntity::class)->makePartial();
        $application->setId(111);
        $application->setS4s(new \Doctrine\Common\Collections\ArrayCollection());
        $application->addS4s($s4);
        $application->shouldReceive('getOverrideOoo')->andReturn('Y');

        $this->repoMap['Application']->shouldReceive('fetchUsingId')->with($command)->andReturn($application);

        try {
            $this->sut->handleCommand($command);
        } catch (\Dvsa\Olcs\Api\Domain\Exception\ValidationException $e) {
            $this->assertArrayNotHasKey('oood', $e->getMessages());
            $this->assertArrayNotHasKey('oord', $e->getMessages());
            $this->assertArrayHasKey('s4', $e->getMessages());
            $this->assertArrayHasKey('APP-GRA-S4-EMPTY', $e->getMessages()['s4']);
        }
    }

    public function testHandleCommandOppositionUnknown()
    {
        $data = ['id' => 111];

        $command = Cmd::create($data);

        /** @var ApplicationEntity $application */
        $application = m::mock(ApplicationEntity::class)->makePartial();
        $application->setId(111);
        $application->shouldReceive('getS4s')->andReturn(new \Doctrine\Common\Collections\ArrayCollection());
        $application->shouldReceive('getOutOfOppositionDate')->andReturn(ApplicationEntity::UNKNOWN);
        $application->shouldReceive('getOutOfRepresentationDate')->andReturn(ApplicationEntity::UNKNOWN);

        $this->repoMap['Application']->shouldReceive('fetchUsingId')->with($command)->andReturn($application);

        try {
            $this->sut->handleCommand($command);
        } catch (\Dvsa\Olcs\Api\Domain\Exception\ValidationException $e) {
            $this->assertArrayNotHasKey('s4', $e->getMessages());
            $this->assertArrayHasKey('oood', $e->getMessages());
            $this->assertArrayHasKey('APP-GRA-OOOD-UNKNOWN', $e->getMessages()['oood']);
            $this->assertArrayHasKey('oord', $e->getMessages());
            $this->assertArrayHasKey('APP-GRA-OORD-UNKNOWN', $e->getMessages()['oord']);
        }
    }

    public function testHandleCommandOppositionNotPassed()
    {
        $data = ['id' => 111];

        $command = Cmd::create($data);

        /** @var ApplicationEntity $application */
        $application = m::mock(ApplicationEntity::class)->makePartial();
        $application->setId(111);
        $application->shouldReceive('getS4s')->andReturn(new \Doctrine\Common\Collections\ArrayCollection());
        $application->shouldReceive('getOutOfOppositionDate')->andReturn(new \DateTime('2093-12-02'));
        $application->shouldReceive('getOutOfRepresentationDate')->andReturn(new \DateTime('2056-03-23'));

        $this->repoMap['Application']->shouldReceive('fetchUsingId')->with($command)->andReturn($application);

        try {
            $this->sut->handleCommand($command);
        } catch (\Dvsa\Olcs\Api\Domain\Exception\ValidationException $e) {
            $this->assertArrayNotHasKey('s4', $e->getMessages());
            $this->assertArrayHasKey('oood', $e->getMessages());
            $this->assertArrayHasKey('APP-GRA-OOOD-NOT-PASSED', $e->getMessages()['oood']);
            $this->assertArrayHasKey('oord', $e->getMessages());
            $this->assertArrayHasKey('APP-GRA-OORD-NOT-PASSED', $e->getMessages()['oord']);
        }
    }
}
