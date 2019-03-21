<?php

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Surrender;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\Surrender\Create as Sut;
use Dvsa\Olcs\Api\Domain\Exception\ForbiddenException;
use Dvsa\Olcs\Api\Domain\Exception\NotFoundException;
use Dvsa\Olcs\Api\Domain\Repository\Query\Licence as LicenceRepo;
use Dvsa\Olcs\Transfer\Command\Surrender\Create as Cmd;
use Dvsa\Olcs\Api\Domain\Repository\Surrender as SurrenderRepo;
use Dvsa\Olcs\Api\Entity\Surrender as SurrenderEntity;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Mockery as m;

class CreateTest extends CommandHandlerTestCase
{
    const LIC_ID = 111;

    /** @var Sut */
    protected $sut;

    public function setUp()
    {
        $this->sut = new Sut();

        $this->mockRepo('Surrender', SurrenderRepo::class);
        $this->mockRepo('Licence', LicenceRepo::class);
        $this->mockedSmServices = [
            \ZfcRbac\Service\AuthorizationService::class => m::mock(\ZfcRbac\Service\AuthorizationService::class)
        ];
        parent::setUp();
    }

    public function testHandleCommandWithNoSurrender()
    {
        $command = $this->createCommand();

        $licence = $this->getTestingLicence();

        $this->repoMap['Licence']
            ->shouldReceive('fetchById')
            ->with(self::LIC_ID)
            ->once()
            ->andReturn($licence);

        $this->repoMap['Surrender']
            ->shouldReceive('fetchOneByLicenceId')
            ->once()
            ->andThrow(NotFoundException::class);

        $this->repoMap['Surrender']
            ->shouldReceive('save')
            ->with(m::type(SurrenderEntity::class))
            ->once();

        $result = $this->sut->handleCommand($command);

        $this->assertSame(['Surrender successfully created.'], $result->getMessages());

        $this->assertInstanceOf(Result::class, $result);
    }

    public function testHandleCommandWithWithdrawnSurrender()
    {
        $command = $this->createCommand();

        $surrenderEntity = m::mock(SurrenderEntity::class);
        $surrenderEntity->shouldReceive('getStatus->getId')->andReturn('surr_sts_withdrawn');
        $surrenderEntity->shouldReceive('getID')->andReturn('1');
        $surrenderEntity->shouldReceive('setStatus')->once();

        $this->repoMap['Surrender']
            ->shouldReceive('fetchOneByLicenceId')
            ->once()
            ->andReturn($surrenderEntity);

        $this->repoMap['Surrender']
            ->shouldReceive('save')
            ->with($surrenderEntity)
            ->once();

        $result = $this->sut->handleCommand($command);

        $this->assertSame(['Surrender successfully restarted after being withdrawn.'], $result->getMessages());

        $this->assertInstanceOf(Result::class, $result);
    }

    public function testHandleCommandWithIncorrectSurrenderStatus()
    {
        $command = $this->createCommand();

        $surrenderEntity = m::mock(SurrenderEntity::class);
        $surrenderEntity->shouldReceive('getStatus->getId')->andReturn('surr_sts_start');

        $this->repoMap['Surrender']
            ->shouldReceive('fetchOneByLicenceId')
            ->once()
            ->andReturn($surrenderEntity);

        $this->expectException(ForbiddenException::class);
        $this->sut->handleCommand($command);
    }

    protected function createCommand()
    {
        $data = [
            'id' => self::LIC_ID,
        ];

        return Cmd::create($data);
    }
}
