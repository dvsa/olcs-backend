<?php

declare(strict_types=1);

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\SystemParameter;

use Dvsa\Olcs\Transfer\Service\CacheEncryption;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Transfer\Command\SystemParameter\UpdateSystemParameter as Cmd;
use Mockery as m;

class UpdateTest extends CommandHandlerTestCase
{
    public function setUp(): void
    {
        $this->sut = new \Dvsa\Olcs\Api\Domain\CommandHandler\SystemParameter\Update();
        $this->mockRepo('SystemParameter', \Dvsa\Olcs\Api\Domain\Repository\SystemParameter::class);
        $this->mockedSmServices[CacheEncryption::class] = m::mock(CacheEncryption::class);

        parent::setUp();
    }

    public function testHandleCommand(): void
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

        $this->expectedSingleCacheClear(CacheEncryption::SYS_PARAM_IDENTIFIER, 'NAME');
        $this->expectedListCacheClear(CacheEncryption::SYS_PARAM_LIST_IDENTIFIER);

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
