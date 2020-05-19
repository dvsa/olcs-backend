<?php

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Surrender;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\Surrender\Delete as Sut;
use Dvsa\Olcs\Api\Domain\Exception\NotFoundException;
use Dvsa\Olcs\Api\Domain\Repository\Document as DocumentRepo;
use Dvsa\Olcs\Api\Entity\Doc\Document as DocumentEntity;
use Dvsa\Olcs\Api\Service\Document\Document;
use Dvsa\Olcs\Transfer\Command\Surrender\Delete as Cmd;
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
        $this->mockRepo('Document', DocumentRepo::class);

        $this->mockedSmServices = [
            \ZfcRbac\Service\AuthorizationService::class => m::mock(\ZfcRbac\Service\AuthorizationService::class)
        ];
        parent::setUp();
    }

    public function testHandleCommand()
    {
        $data = [
            'id' => 1
        ];
        $surrenderId = 999;
        $documentId1 = 777;
        $documentId2 = 888;

        $command = Cmd::create($data);

        $surrenderEntity = m::mock(SurrenderEntity::class);
        $surrenderEntity->shouldReceive('getId')->andReturn($surrenderId);

        $documentEntity1 = m::mock(DocumentEntity::class);
        $documentEntity1->shouldReceive('getId')->andReturn($documentId1);

        $documentEntity2 = m::mock(DocumentEntity::class);
        $documentEntity2->shouldReceive('getId')->andReturn($documentId2);

        $this->repoMap['Surrender']
            ->shouldReceive('fetchOneByLicenceId')
            ->with($data['id'])
            ->andReturn($surrenderEntity)
            ->once();

        $this->repoMap['Surrender']
            ->shouldReceive('delete')
            ->with(m::type(SurrenderEntity::class))
            ->once();

        $this->repoMap['Document']
            ->shouldReceive('fetchListForSurrender')
            ->with($surrenderId)
            ->andReturn([$documentEntity1, $documentEntity2])
            ->once();

        $this->repoMap['Document']
            ->shouldReceive('hardDelete')
            ->with($documentEntity1)
            ->once();
        $this->repoMap['Document']
            ->shouldReceive('hardDelete')
            ->with($documentEntity2)
            ->once();

        $result = $this->sut->handleCommand($command);

        $this->assertSame($data['id'], $result->getId('id' . $data['id']));
        $this->assertSame(['surrender for licence Id ' . $data['id'] . ' deleted'], $result->getMessages());
        $this->assertSame([$documentId1, $documentId2], $result->getId('documents'));

        $this->assertInstanceOf(Result::class, $result);
    }

    public function testHandleCommandException()
    {
        $data = [
            'id' => 1
        ];
        $command = Cmd::create($data);

        $this->repoMap['Surrender']
            ->shouldReceive('fetchOneByLicenceId')
            ->with($data['id'])
            ->andThrow(NotFoundException::class)
            ->once();

        $result = $this->sut->handleCommand($command);

        $this->assertSame(['surrender for licence Id ' . $data['id'] . ' not found'], $result->getMessages());

        $this->assertInstanceOf(Result::class, $result);
    }
}
