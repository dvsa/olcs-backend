<?php

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\PartialMarkup;

use Mockery as m;
use Dvsa\Olcs\Api\Domain\CommandHandler\PartialMarkup\Update as UpdateHandler;
use Dvsa\Olcs\Api\Domain\Repository\PartialMarkup as PartialMarkupRepo;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Api\Domain\Command\PartialMarkup\Update as UpdateCmd;
use Dvsa\Olcs\Api\Entity\System\PartialMarkup as PartialMarkupEntity;

/**
 * Update PartialMarkup Test
 *
 * @author Andy Newton <andy@vitri.ltd>
 */
class UpdateTest extends CommandHandlerTestCase
{
    public function setUp(): void
    {
        $this->sut = new UpdateHandler();
        $this->mockRepo('PartialMarkup', PartialMarkupRepo::class);
        parent::setUp();
    }

    public function testHandleCommand()
    {
        $id = 232;
        $markup = 'This is some text';

        $cmdData = [
            'id' => $id,
            'markup' => $markup,
        ];

        $command = UpdateCmd::create($cmdData);

        $entity = m::mock(PartialMarkupEntity::class);

        $this->repoMap['PartialMarkup']
            ->shouldReceive('fetchById')
            ->with($id)
            ->once()
            ->andReturn($entity);

        $entity->shouldReceive('update')
            ->globally()
            ->ordered()
            ->once()
            ->with($markup);

        $this->repoMap['PartialMarkup']
            ->shouldReceive('save')
            ->globally()
            ->ordered()
            ->with($entity)
            ->once();

        $entity->shouldReceive('getId')
            ->withNoArgs()
            ->andReturn(232);

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [
                'PartialMarkup' => 232
            ],
            'messages' => [
                'Partial Markup Updated: 232'
            ]
        ];

        $this->assertEquals($expected, $result->toArray());
    }
}
