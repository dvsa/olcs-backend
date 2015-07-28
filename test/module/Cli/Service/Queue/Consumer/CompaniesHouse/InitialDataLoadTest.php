<?php

/**
 * Companies House Initial Data Load Queue Consumer Test
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace Dvsa\OlcsTest\Service\Queue\Consumer\CompaniesHouse;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Entity\Queue\Queue as QueueEntity;
use Dvsa\Olcs\Cli\Service\Queue\Consumer\CompaniesHouse\InitialDataLoad;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use OlcsTest\Bootstrap;

/**
 * Companies House Initial Data Load Queue Consumer Test
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class InitialDataLoadTest extends MockeryTestCase
{
    protected $sut;
    protected $sm;
    protected $chm;

    public function setUp()
    {
        $this->sm = Bootstrap::getServiceManager();

        $this->chm = m::mock();
        $this->sm->setService('CommandHandlerManager', $this->chm);

        $this->sut = new InitialDataLoad();
        $this->sut->setServiceLocator($this->sm);
    }

    public function testProcessMessageSuccess()
    {
        $item = new QueueEntity();
        $item->setId(99);
        $item->setOptions('{"companyNumber":"01234567"}');

        $expectedDtoData = ['companyNumber' => '01234567'];
        $cmdResult = new Result();
        $cmdResult
            ->addId('companiesHouseCompany', 101)
            ->addMessage('Company added');

        $this->expectCommand(
            \Dvsa\Olcs\Api\Domain\Command\CompaniesHouse\InitialLoad::class,
            $expectedDtoData,
            $cmdResult
        );

        $this->expectCommand(
            \Dvsa\Olcs\Api\Domain\Command\Queue\Complete::class,
            ['item' => $item],
            new Result()
        );

        $result = $this->sut->processMessage($item);

        $this->assertEquals(
            'Successfully processed message: 99 {"companyNumber":"01234567"} Company added',
            $result
        );
    }

    public function testProcessMessageFailure()
    {
        $item = new QueueEntity();
        $item->setId(99);
        $item->setOptions('{"companyNumber":"01234567"}');

        $this->expectCommandException(
            \Dvsa\Olcs\Api\Domain\Command\CompaniesHouse\InitialLoad::class,
            ['companyNumber' => '01234567'],
            \Dvsa\Olcs\Api\Domain\Exception\Exception::class,
            'epic fail'
        );

        $this->expectCommand(
            \Dvsa\Olcs\Api\Domain\Command\Queue\Failed::class,
            ['item' => $item],
            new Result()
        );

        $result = $this->sut->processMessage($item);

        $this->assertEquals(
            'Failed to process message: 99 {"companyNumber":"01234567"} epic fail',
            $result
        );
    }

    /**
     * @param string $class
     * @param array $expectedDtoData
     * @param mixed $result
     */
    private function expectCommand($class, $expectedDtoData, $result)
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
    private function expectCommandException($class, $expectedDtoData, $exceptionClass, $exceptionMsg)
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
