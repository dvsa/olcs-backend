<?php

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Variation;

use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Api\Domain\CommandHandler\Variation\DeleteListConditionUndertaking as CommandHandler;
use Dvsa\Olcs\Transfer\Command\Variation\DeleteListConditionUndertaking as Command;
use Dvsa\Olcs\Api\Domain\Repository\ConditionUndertaking as ConditionUndertakingRepo;
use Mockery as m;

/**
 * DeleteListConditionUndertakingTest
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class DeleteListConditionUndertakingTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new CommandHandler();
        $this->mockRepo('ConditionUndertaking', ConditionUndertakingRepo::class);

        parent::setUp();
    }

    protected function initReferences()
    {
        $this->references = [
            \Dvsa\Olcs\Api\Entity\Application\Application::class => [
                65 => m::mock(\Dvsa\Olcs\Api\Entity\Application\Application::class)
            ]
        ];

        parent::initReferences();
    }

    public function testHandleCommand()
    {
        $data = [
            'id' => 65,
            'ids' => [73,324]
        ];
        $command = Command::create($data);

        $mockConditionUndertaking1 = new \Dvsa\Olcs\Api\Entity\Cases\ConditionUndertaking(
            new \Dvsa\Olcs\Api\Entity\System\RefData(),
            1,
            1
        );
        $mockConditionUndertaking1
            ->setId(73)
            ->setApplication(32432);

        $mockConditionUndertaking2 = new \Dvsa\Olcs\Api\Entity\Cases\ConditionUndertaking(
            new \Dvsa\Olcs\Api\Entity\System\RefData(),
            0,
            0
        );
        $mockConditionUndertaking2
            ->setId(324)
            ->setAttachedTo('ATTACHED_TO')
            ->setLicence('LICENCE');

        $this->repoMap['ConditionUndertaking']->shouldReceive('fetchById')->with(73)->once()
            ->andReturn($mockConditionUndertaking1);
        $this->repoMap['ConditionUndertaking']->shouldReceive('delete')->with($mockConditionUndertaking1)->once();

        $this->repoMap['ConditionUndertaking']->shouldReceive('fetchById')->with(324)->once()
            ->andReturn($mockConditionUndertaking2);
        $this->repoMap['ConditionUndertaking']->shouldReceive('save')->once()->andReturnUsing(
            function (\Dvsa\Olcs\Api\Entity\Cases\ConditionUndertaking $cu) use ($mockConditionUndertaking2) {
                $this->assertSame('ATTACHED_TO', $cu->getAttachedTo());
                $this->assertSame('D', $cu->getAction());
                $this->assertSame($mockConditionUndertaking2, $cu->getLicConditionVariation());
                $this->assertSame(null, $cu->getLicence());
                $this->assertSame(
                    $this->references[\Dvsa\Olcs\Api\Entity\Application\Application::class][65],
                    $cu->getApplication()
                );
            }
        );

        $result = $this->sut->handleCommand($command);

        $this->assertEquals(
            ['ConditionUndertaking ID 73 deleted', 'ConditionUndertaking ID 324 deleted'],
            $result->getMessages()
        );
    }
}
