<?php

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Cache;

use Dvsa\Olcs\Api\Domain\Command\Cache\Generate as Cmd;
use Dvsa\Olcs\Api\Domain\CommandHandler\Cache\Generate as Handler;
use Dvsa\Olcs\Transfer\Query\Cache\ById;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\AbstractCommandHandlerTestCase;
use Mockery as m;
use Olcs\Logging\Log\Logger;

/**
 * Test generating a cache
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
class GenerateTest extends AbstractCommandHandlerTestCase
{
    public function setUp(): void
    {
        $this->sut = m::mock(Handler::class)->makePartial()->shouldAllowMockingProtectedMethods();

        $logWriter = new \Laminas\Log\Writer\Mock();
        $logger = new \Laminas\Log\Logger();
        $logger->addWriter($logWriter);

        Logger::setLogger($logger);

        parent::setUp();
    }

    /**
     * @dataProvider dpHandleCommand
     */
    public function testHandleCommand($uniqueId, $messages)
    {
        $cacheId = 'cacheId';

        $commandParams = [
            'id' => $cacheId,
            'uniqueId' => $uniqueId,
        ];

        $command = Cmd::create($commandParams);

        $this->sut->expects('handleQuery')
            ->with(m::type(ById::class))
            ->andReturnUsing(function ($query) use ($cacheId, $uniqueId) {
                $this->assertEquals($cacheId, $query->getId());
                $this->assertEquals($uniqueId, $query->getUniqueId());
                $this->assertTrue($query->getShouldRegen());
            });

        $result = $this->sut->handleCommand($command);

        $this->assertEquals($messages, $result->getMessages());
    }

    public function dpHandleCommand()
    {
        return [
            [null, ['Cache updated for cacheId without a unique id']],
            ['uniqueId', ['Cache updated for cacheId with unique id of uniqueId']]
        ];
    }

    public function testHandleCommandWithQueryException()
    {
        $commandParams = [
            'id' => 'cacheId',
            'uniqueId' => null,
        ];

        $command = Cmd::create($commandParams);

        $this->sut->expects('handleQuery')
            ->with(m::type(ById::class))
            ->andThrow(\Exception::class, 'exception message');

        $result = $this->sut->handleCommand($command);

        $messages = ['Cache update failed for cacheId with error message: exception message'];
        $this->assertEquals($messages, $result->getMessages());
    }
}
