<?php

/**
 * UpdateVariationCompletionTest
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Application;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\Repository\Application;
use Dvsa\Olcs\Api\Domain\CommandHandler\Application\UpdateVariationCompletion;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Api\Domain\Command\Application\UpdateApplicationCompletion as UpdateApplicationCompletionCommand;

/**
 * UpdateVariationCompletionTest
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class UpdateVariationCompletionTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new UpdateVariationCompletion();
        $this->mockRepo('Application', Application::class);

        parent::setUp();
    }

    public function testHandleCommand()
    {
        // Params
        $command = UpdateApplicationCompletionCommand::create(
            [
                'id' => 627,
                'section' => 'secion1',
            ]
        );

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [],
            'messages' => ['UpdateVariationCompletion needs to be implemented.']
        ];

        $this->assertInstanceOf(Result::class, $result);
        $this->assertEquals($expected, $result->toArray());
    }
}
