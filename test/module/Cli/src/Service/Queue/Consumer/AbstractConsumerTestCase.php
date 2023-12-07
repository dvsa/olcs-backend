<?php

namespace Dvsa\OlcsTest\Cli\Service\Queue\Consumer;

use Dvsa\Olcs\Api\Domain\CommandHandlerManager;
use Dvsa\Olcs\Cli\Service\Queue\Consumer\AbstractConsumerServices;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Olcs\Logging\Log\Logger;

/**
 * Abstract Queue Consumer Test
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
abstract class AbstractConsumerTestCase extends MockeryTestCase
{
    /** @var  \Dvsa\Olcs\Cli\Service\Queue\Consumer\AbstractConsumer */
    protected $sut;

    /** @var  m\MockInterface */
    protected $chm;

    /** @var  m\MockInterface */
    protected $abstractConsumerServices;

    protected $consumerClass = 'override_me';

    public function setUp(): void
    {
        $this->chm = m::mock(CommandHandlerManager::class);

        $this->abstractConsumerServices = m::mock(AbstractConsumerServices::class);
        $this->abstractConsumerServices->shouldReceive('getCommandHandlerManager')
            ->withNoArgs()
            ->andReturn($this->chm);

        $this->instantiate();

        $logWriter = new \Laminas\Log\Writer\Mock();
        $logger = new \Laminas\Log\Logger();
        $logger->addWriter($logWriter);

        Logger::setLogger($logger);
    }

    protected function instantiate()
    {
        $consumerClass = $this->consumerClass;
        $this->sut = new $consumerClass($this->abstractConsumerServices);
    }

    /**
     * @param string $class
     * @param array $expectedDtoData
     * @param mixed $result
     */
    protected function expectCommand($class, $expectedDtoData, $result, $validate = true)
    {
        if ($validate) {
            $this->chm
                ->shouldReceive('handleCommand')
                ->with(
                    m::on(
                        function (CommandInterface $cmd) use ($expectedDtoData, $class) {
                            $matched = (
                                is_a($cmd, $class)
                                &&
                                $cmd->getArrayCopy() == $expectedDtoData
                            );
                            return $matched;
                        }
                    )
                )
                ->once()
                ->andReturn($result);
        } else {
            $this->chm
                ->shouldReceive('handleCommand')
                ->with(
                    m::on(
                        function (CommandInterface $cmd) use ($expectedDtoData, $class) {
                            $matched = (
                                is_a($cmd, $class)
                                &&
                                $cmd->getArrayCopy() == $expectedDtoData
                            );
                            return $matched;
                        }
                    ),
                    false
                )
                ->once()
                ->andReturn($result);
        }
    }

    /**
     * @param string $class
     * @param array $expectedDtoData
     * @param string $exceptionClass
     * @param string $exceptionMsg
     * @param int $retryAfter
     */
    protected function expectCommandException(
        $class,
        $expectedDtoData,
        $exceptionClass,
        $exceptionMsg = '',
        $retryAfter = 900,
        $validate = true
    ) {
        $exception = new $exceptionClass($exceptionMsg);

        //it's a pain that we have two ways to set retry after - this deals with those that are set on the exception
        if (method_exists($exceptionClass, 'setRetryAfter')) {
            $exception->setRetryAfter($retryAfter);
        }

        if ($validate) {
            $this->chm
                ->shouldReceive('handleCommand')
                ->with(
                    m::on(
                        function (CommandInterface $cmd) use ($expectedDtoData, $class) {
                            $matched = (
                                is_a($cmd, $class)
                                &&
                                $cmd->getArrayCopy() == $expectedDtoData
                            );
                            return $matched;
                        }
                    )
                )
                ->once()
                ->andThrow($exception);
        } else {
            $this->chm
                ->shouldReceive('handleCommand')
                ->with(
                    m::on(
                        function (CommandInterface $cmd) use ($expectedDtoData, $class) {
                            $matched = (
                                is_a($cmd, $class)
                                &&
                                $cmd->getArrayCopy() == $expectedDtoData
                            );
                            return $matched;
                        }
                    ),
                    false
                )
                ->once()
                ->andThrow($exception);
        }
    }
}
