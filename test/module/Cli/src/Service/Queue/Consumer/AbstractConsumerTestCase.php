<?php

/**
 * Abstract Queue Consumer Test
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace Dvsa\OlcsTest\Cli\Service\Queue\Consumer;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Entity\Queue\Queue as QueueEntity;
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
    protected $consumerClass = 'override_me';

    public function setUp()
    {
        $this->sm = Bootstrap::getServiceManager();

        $this->chm = m::mock();
        $this->sm->setService('CommandHandlerManager', $this->chm);

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
                )
            )
            ->once()
            ->andReturn($result);
    }

    /**
     * @param string $class
     * @param array $expectedDtoData
     * @param string $exceptionClass
     */
    protected function expectCommandException($class, $expectedDtoData, $exceptionClass, $exceptionMsg)
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
                )
            )
            ->once()
            ->andThrow(new $exceptionClass($exceptionMsg));
    }
}
