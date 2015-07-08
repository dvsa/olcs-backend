<?php

/**
 * UpdateProhibitionNote Test
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Cases;

use Doctrine\ORM\Query;
use Mockery as m;
use Dvsa\Olcs\Api\Domain\CommandHandler\Cases\UpdateProhibitionNote;
use Dvsa\Olcs\Api\Domain\Repository\Cases as CasesRepo;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Transfer\Command\Cases\UpdateProhibitionNote as Cmd;
use Dvsa\Olcs\Api\Entity\Cases\Cases as CasesEntity;

/**
 * UpdateProhibitionNote Test
 */
class UpdateProhibitionNoteTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new UpdateProhibitionNote();
        $this->mockRepo('Cases', CasesRepo::class);

        parent::setUp();
    }

    public function testHandleCommand()
    {
        $command = Cmd::Create(
            [
                'id' => 99,
                'prohibitionNote ' => ''
            ]
        );

        /** @var CasesEntity $cases */
        $cases = m::mock(CasesEntity::class)->makePartial();
        $cases->shouldReceive('updateProhibitionNote')
            ->once();

        $this->repoMap['Cases']->shouldReceive('fetchUsingId')
            ->with($command, Query::HYDRATE_OBJECT, $command->getVersion())
            ->andReturn($cases)
            ->shouldReceive('save')
            ->with(m::type(CasesEntity::class))
            ->once();

        $result = $this->sut->handleCommand($command);

        $this->assertInstanceOf('Dvsa\Olcs\Api\Domain\Command\Result', $result);
    }
}
