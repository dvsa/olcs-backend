<?php

/**
 * Transactioning Command Handler Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler;

use Dvsa\Olcs\Api\Domain\CommandHandler\CommandHandlerInterface;
use Dvsa\Olcs\Api\Domain\Repository\TransactionManagerInterface;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Mockery as m;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactioningCommandHandler;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * Transactioning Command Handler Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class TransactioningCommandHandlerTest extends MockeryTestCase
{
    private $wrapped;

    private $repo;

    /**
     * @var TransactioningCommandHandler
     */
    private $sut;

    public function setUp()
    {
        $this->wrapped = m::mock(CommandHandlerInterface::class);
        $this->repo = m::mock(TransactionManagerInterface::class);

        $this->sut = new TransactioningCommandHandler($this->wrapped, $this->repo);
    }

    public function testGetWrapped()
    {
        $this->assertSame($this->wrapped, $this->sut->getWrapped());
    }

    public function testHandleCommand()
    {
        $command = m::mock(CommandInterface::class);

        $this->repo->shouldReceive('beginTransaction')->once();

        $this->wrapped->shouldReceive('handleCommand')->once()->with($command)->andReturn(['result']);

        $this->repo->shouldReceive('commit')->once();

        $this->assertEquals(['result'], $this->sut->handleCommand($command));
    }

    public function testHandleCommandException()
    {
        $this->setExpectedException(\Exception::class);

        $command = m::mock(CommandInterface::class);

        $this->repo->shouldReceive('beginTransaction')->once();

        $this->wrapped->shouldReceive('handleCommand')->once()->with($command)->andReturn(['result']);

        $this->repo->shouldReceive('commit')->once()->andThrow(new \Exception());

        $this->repo->shouldReceive('rollback')->once();

        $this->sut->handleCommand($command);
    }

    public function testIsEnabled()
    {
        $isEnabled = true;
        $this->wrapped->shouldReceive('isEnabled')->once()->andReturn($isEnabled);
        $this->assertEquals($isEnabled, $this->sut->isEnabled());
    }
}
