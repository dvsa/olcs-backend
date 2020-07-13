<?php

/**
 * Delete Change Of Entity test
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\ChangeOfEntity;

use Mockery as m;
use Dvsa\Olcs\Api\Domain\CommandHandler\ChangeOfEntity\DeleteChangeOfEntity as Sut;
use Dvsa\Olcs\Api\Domain\Repository\Application as ApplicationRepo;
use Dvsa\Olcs\Api\Domain\Repository\ChangeOfEntity as ChangeOfEntityRepo;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Transfer\Command\ChangeOfEntity\DeleteChangeOfEntity as Cmd;
use Dvsa\Olcs\Api\Entity\Organisation\ChangeOfEntity as ChangeOfEntityEntity;

/**
 * Delete Change Of Entity test
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class DeleteChangeOfEntityTest extends CommandHandlerTestCase
{
    public function setUp(): void
    {
        $this->sut = new Sut();
        $this->mockRepo('ChangeOfEntity', ChangeOfEntityRepo::class);

        parent::setUp();
    }

    public function testHandleCommand()
    {
        $command = Cmd::create(
            [
                'id' => 11,
            ]
        );

        /** @var ChangeOfEntityEntity $changeOfEntity */
        $changeOfEntity = m::mock(ChangeOfEntityEntity::class)
            ->makePartial()
            ->setId(11);

        $this->repoMap['ChangeOfEntity']
            ->shouldReceive('fetchUsingId')
            ->with($command)
            ->once()
            ->andReturn($changeOfEntity)
            ->shouldReceive('delete')
            ->with($changeOfEntity)
            ->once();
        $result = $this->sut->handleCommand($command);

        $this->assertInstanceOf('Dvsa\Olcs\Api\Domain\Command\Result', $result);
        $this->assertObjectHasAttribute('ids', $result);
        $this->assertObjectHasAttribute('messages', $result);
        $this->assertContains('ChangeOfEntity Deleted', $result->getMessages());
        $this->assertEquals(11, $result->getId('changeOfEntity'));
    }
}
