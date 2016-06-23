<?php

/**
 * Abstract Queue Consumer Test
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace Dvsa\OlcsTest\Cli\Service\Queue\Consumer;

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
    protected $sut;
    protected $sm;
    protected $chm;
    protected $qhm;
    protected $consumerClass = 'override_me';

    public function setUp()
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
    protected function expectCommand($class, $expectedDtoData, $result)
    {
        $this->chm
            ->shouldReceive('handleCommand')
            ->with(
                m::on(
                    function ($cmd) use ($expectedDtoData, $class) {
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
        $retryAfter = 900
    ) {
        $exception = new $exceptionClass($exceptionMsg);

        //it's a pain that we have two ways to set retry after - this deals with those that are set on the exception
        if (method_exists($exceptionClass, 'setRetryAfter')) {
            $exception->setRetryAfter($retryAfter);
        }

        $this->chm
            ->shouldReceive('handleCommand')
            ->with(
                m::on(
                    function ($cmd) use ($expectedDtoData, $class) {
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
                    function ($qry) use ($expectedDtoData, $class) {
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
     * @param string|Exception $exception
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
                    function ($qry) use ($expectedDtoData, $class) {
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
