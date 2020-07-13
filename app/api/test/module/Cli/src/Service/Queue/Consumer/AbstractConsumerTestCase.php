<?php

namespace Dvsa\OlcsTest\Cli\Service\Queue\Consumer;

use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use OlcsTest\Bootstrap;

/**
 * Abstract Queue Consumer Test
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
abstract class AbstractConsumerTestCase extends MockeryTestCase
{
    /** @var  \Dvsa\Olcs\Cli\Service\Queue\Consumer\AbstractConsumer */
    protected $sut;
    protected $sm;
    /** @var  m\MockInterface */
    protected $qhm;
    /** @var  m\MockInterface */
    protected $chm;
    protected $consumerClass = 'override_me';

    public function setUp(): void
    {
        $this->sm = Bootstrap::getServiceManager();

        $this->chm = m::mock();
        $this->sm->setService('CommandHandlerManager', $this->chm);

        $this->qhm = m::mock();
        $this->sm->setService('QueryHandlerManager', $this->qhm);

        $consumerClass = $this->consumerClass;
        $this->sut = new $consumerClass();
        $this->sut->setServiceLocator($this->sm);
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

    /**
     * @param string $class expected Query class name
     * @param array $expectedDtoData
     * @param array $result to be returned by $response->getResult()
     */
    protected function expectQuery($class, $expectedDtoData, $result)
    {
        $this->qhm
            ->shouldReceive('handleQuery')
            ->with(
                m::on(
                    function (QueryInterface $qry) use ($expectedDtoData, $class) {
                        $matched = (
                            is_a($qry, $class)
                            &&
                            $qry->getArrayCopy() == $expectedDtoData
                        );
                        return $matched;
                    }
                )
            )
            ->once()
            ->andReturn($result);
    }

    /**
     * @param string $class
     * @param array $expectedDtoData
     * @param string|\Exception $exception
     */
    protected function expectQueryException($class, $expectedDtoData, $exception, $exceptionMsg = '')
    {
        if (is_string($exception)) {
            $exception = new $exception($exceptionMsg);
        }

        $this->qhm
            ->shouldReceive('handleQuery')
            ->with(
                m::on(
                    function (QueryInterface $qry) use ($expectedDtoData, $class) {
                        $matched = (
                            is_a($qry, $class)
                            &&
                            $qry->getArrayCopy() == $expectedDtoData
                        );
                        return $matched;
                    }
                )
            )
            ->once()
            ->andThrow($exception);
    }
}
