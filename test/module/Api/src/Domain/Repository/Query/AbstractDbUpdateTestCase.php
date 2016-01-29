<?php

/**
 * Abstract Db Update Test Case
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\Repository\Query;

use Dvsa\Olcs\Api\Domain\Exception\RuntimeException;
use Mockery as m;

/**
 * Abstract Db Update Test Case
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
abstract class AbstractDbUpdateTestCase extends AbstractDbTestCase
{
    /**
     * @dataProvider paramProvider
     */
    public function testExecuteUpdateWithException($inputParams, $inputTypes, $expectedParams, $expectedTypes)
    {
        $this->setExpectedException(RuntimeException::class);

        $this->connection->shouldReceive('executeUpdate')
            ->with($this->getExpectedQuery(), $expectedParams, $expectedTypes)
            ->once()
            ->andThrow(new \Exception());

        $this->sut->executeUpdate($inputParams, $inputTypes);
    }

    /**
     * @dataProvider paramProvider
     */
    public function testExecuteUpdate($inputParams, $inputTypes, $expectedParams, $expectedTypes)
    {
        $this->connection->shouldReceive('executeUpdate')
            ->with($this->getExpectedQuery(), $expectedParams, $expectedTypes)
            ->once()
            ->andReturn('result');

        $this->assertEquals('result', $this->sut->executeUpdate($inputParams, $inputTypes));
    }
}
