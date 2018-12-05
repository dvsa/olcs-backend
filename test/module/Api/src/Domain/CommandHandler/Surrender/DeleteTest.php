<?php

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Surrender;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\Surrender\Delete as Sut;
use Dvsa\Olcs\Api\Domain\Exception\NotFoundException;
use Dvsa\Olcs\Transfer\Command\Surrender\Update as Cmd;
use Dvsa\Olcs\Api\Domain\Repository\Surrender as SurrenderRepo;
use Dvsa\Olcs\Api\Entity\Surrender as SurrenderEntity;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Mockery as m;

class DeleteTest extends CommandHandlerTestCase
{

    /** @var Sut */
    protected $sut;

    public function setUp()
    {
        $this->sut = new Sut();

        $this->mockRepo('Surrender', SurrenderRepo::class);

        $this->mockedSmServices = [
            \ZfcRbac\Service\AuthorizationService::class => m::mock(\ZfcRbac\Service\AuthorizationService::class)
        ];
        parent::setUp();
    }

    public function testHandleCommand()
    {
        $data = [
            'licence' => 1
        ];
        $command = Cmd::create($data);

        $surrenderEntity = m::mock(SurrenderEntity::class);
        $this->repoMap['Surrender']
            ->shouldReceive('fetchByLicenceId')
            ->with($data['licence'])
            ->andReturn($surrenderEntity)
            ->once();

        $this->repoMap['Surrender']
            ->shouldReceive('delete')
            ->with(m::type(SurrenderEntity::class))
            ->once();

        $result = $this->sut->handleCommand($command);

        $this->assertSame($data['licence'], $result->getId('id' . $data['licence']));
        $this->assertSame(['surrender for licence Id ' . $data['licence'] . ' deleted'], $result->getMessages());

        $this->assertInstanceOf(Result::class, $result);
    }

    public function testHandleCommandException()
    {
        $data = [
            'licence' => 1
        ];
        $command = Cmd::create($data);

        $this->repoMap['Surrender']
            ->shouldReceive('fetchByLicenceId')
            ->with($data['licence'])
            ->andThrow(NotFoundException::class)
            ->once();

        $result = $this->sut->handleCommand($command);

        $this->assertSame(['surrender for licence Id ' . $data['licence'] . ' not found'], $result->getMessages());

        $this->assertInstanceOf(Result::class, $result);
    }
}
