<?php

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\SystemParameter;

use Dvsa\Olcs\Api\Domain\Command\SystemParameter\Update as Cmd;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;

/**
 * Update SystemParameter
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class UpdateTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new \Dvsa\Olcs\Api\Domain\CommandHandler\SystemParameter\Update();
        $this->mockRepo('SystemParameter', \Dvsa\Olcs\Api\Domain\Repository\SystemParameter::class);

        parent::setUp();
    }

    public function testHandleCommand()
    {
        $data = [
            'id' => 'NAME',
            'value' => 'Foo',
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
