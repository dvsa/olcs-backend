<?php
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\IrhpApplication;

use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Api\Domain\CommandHandler\IrhpApplication\Update;
use Dvsa\Olcs\Api\Entity\Permits\IrhpApplication;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Mockery as m;

/**
 * Update IRHP Application Test
 *
 * @author Tonci Vidovic <tonci.vidovic@capgemini.com>
 */

class UpdateTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->mockRepo('IrhpApplication', IrhpApplication::class);
        $this->sut = new Update();

        parent::setUp();
    }

    public function testHandleCommand()
    {
        $irhpApplicationId = 1;

        $irhpApplication = m::mock(IrhpApplication::class);
        $irhpApplication->shouldReceive('getId')
            ->andReturn($irhpApplicationId);
        $irhpApplication->shouldReceive('setCheckedAnswers')
            ->with(true)
            ->once()
            ->ordered()
            ->globally();

        $this->repoMap['IrhpApplication']->shouldReceive('fetchById')
            ->with($irhpApplicationId)
            ->andReturn($irhpApplication);
        $this->repoMap['IrhpApplication']->shouldReceive('save')
            ->with($irhpApplication)
            ->once()
            ->ordered()
            ->globally();

        $command = m::mock(CommandInterface::class);
        $command->shouldReceive('getId')
            ->andReturn($irhpApplicationId);

        $command->shouldReceive('getCheckedAnswers')
            ->andReturn(true);

        $result = $this->sut->handleCommand($command);

        $this->assertEquals(
            $irhpApplicationId,
            $result->getId('irhpApplication')
        );

        $this->assertEquals(
            [
                'IRHP application updated'
            ],
            $result->getMessages()
        );
    }
}
