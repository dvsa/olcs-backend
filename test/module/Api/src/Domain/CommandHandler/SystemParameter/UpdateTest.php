<?php

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\SystemParameter;

use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Transfer\Command\SystemParameter\UpdateSystemParameter as Cmd;

/**
 * Update SystemParameter
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class UpdateTest extends CommandHandlerTestCase
{
    public function setUp(): void
    {
        $this->sut = new \Dvsa\Olcs\Api\Domain\CommandHandler\SystemParameter\Update();
        $this->mockRepo('SystemParameter', \Dvsa\Olcs\Api\Domain\Repository\SystemParameter::class);

        parent::setUp();
    }

    public function testHandleCommand()
    {
        $data = [
            'id' => 'NAME',
            'paramValue' => 'Foo',
            'description' => 'Bar'
        ];
        $command = Cmd::create($data);

        $systemParameter = new \Dvsa\Olcs\Api\Entity\System\SystemParameter();
        $this->repoMap['SystemParameter']->shouldReceive('fetchUsingId')->with($command)->once()
            ->andReturn($systemParameter);

        $this->repoMap['SystemParameter']->shouldReceive('save')->with($systemParameter)->once();

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [],
            'messages' => [
                'SystemParameter \'NAME\' updated'
            ]
        ];
        $this->assertEquals($expected, $result->toArray());

        $this->assertSame('Foo', $systemParameter->getParamValue());
    }
}
