<?php

/**
 * Create SystemParameter Test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\SystemParameter;

use Mockery as m;
use Dvsa\Olcs\Api\Domain\CommandHandler\SystemParameter\Create as Create;
use Dvsa\Olcs\Api\Domain\Repository\SystemParameter as SystemParameterRepo;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Transfer\Command\SystemParameter\CreateSystemParameter as Cmd;
use Dvsa\Olcs\Api\Entity\System\SystemParameter;

/**
 * Create SystemParameter Test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class CreateTest extends CommandHandlerTestCase
{
    public function setUp(): void
    {
        $this->sut = new Create();
        $this->mockRepo('SystemParameter', SystemParameterRepo::class);

        parent::setUp();
    }

    public function testHandleCommandNeedReassign()
    {
        $command = Cmd::create(['id' => 'foo', 'paramValue' => 'bar', 'description' => 'cake']);

        $systemParameter = null;
        $this->repoMap['SystemParameter']
            ->shouldReceive('save')
            ->once()
            ->with(m::type(SystemParameter::class))
            ->andReturnUsing(
                function (SystemParameter $sp) use (&$systemParameter) {
                    $sp->setId('foo');
                    $sp->setParamValue('bar');
                    $sp->setDescription('cake');
                    $systemParameter = $sp;
                }
            )
            ->getMock();

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => ['systemParameter' => 'foo'],
            'messages' => ["System Parameter 'foo' created"]
        ];
        $this->assertEquals($expected, $result->toArray());
    }
}
