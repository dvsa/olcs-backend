<?php

/**
 * Delete Workshop Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Workshop;

use Dvsa\Olcs\Api\Domain\Repository\Workshop;
use Mockery as m;
use Dvsa\Olcs\Api\Domain\CommandHandler\Workshop\DeleteWorkshop;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Transfer\Command\Workshop\DeleteWorkshop as Cmd;

/**
 * Delete Workshop Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class DeleteWorkshopTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new DeleteWorkshop();
        $this->mockRepo('Workshop', Workshop::class);

        parent::setUp();
    }

    protected function initReferences()
    {
        $this->refData = [];

        $this->references = [];

        parent::initReferences();
    }

    public function testHandleCommand()
    {
        $data = [
            'ids' => [111,222]
        ];

        $command = Cmd::create($data);

        $this->repoMap['Workshop']->shouldReceive('fetchById')
            ->with(111)
            ->once()
            ->andReturn('Entity1')
            ->shouldReceive('fetchById')
            ->with(222)
            ->once()
            ->andReturn('Entity2')
            ->shouldReceive('delete')
            ->with('Entity1')
            ->once()
            ->shouldReceive('delete')
            ->with('Entity2')
            ->once();

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [],
            'messages' => [
                '2 Workshop(s) removed'
            ]
        ];

        $this->assertEquals($expected, $result->toArray());
    }
}
