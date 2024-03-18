<?php

namespace Dvsa\OlcsTest\Cli\Command\Batch;

use Dvsa\Olcs\Api\Domain\Exception\NotFoundException;
use Symfony\Component\Console\Command\Command;

trait QueryHandlerExceptionTestsTrait
{
    /**
     * Tests handling of a generic exception by the QueryHandlerManager.
     */
    public function testQueryHandlingGenericException()
    {
        $this->mockQueryHandlerManager->expects($this->any())
            ->method('handleQuery')
            ->willThrowException(new \Exception('Generic exception'));

        $this->executeCommand();
        $this->assertEquals(Command::FAILURE, $this->commandTester->getStatusCode());
    }

    /**
     * Tests handling of a NotFoundException by the QueryHandlerManager.
     */
    public function testQueryHandlingNotFoundException()
    {
        $this->mockQueryHandlerManager->expects($this->any())
            ->method('handleQuery')
            ->willThrowException(new NotFoundException('Not found exception'));

        $this->executeCommand();
        $this->assertEquals(Command::FAILURE, $this->commandTester->getStatusCode());
    }

    public function testExecuteHandlesThrowable()
    {
        $this->mockQueryHandlerManager->method('handleQuery')
            ->will($this->throwException(new \Error('Test throwable')));
        $this->executeCommand();
        $this->assertEquals(Command::FAILURE, $this->commandTester->getStatusCode());
    }
}
