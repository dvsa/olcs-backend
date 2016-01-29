<?php

/**
 * Abstract Db Query Test Case
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\Repository\Query;

use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManager;
use Dvsa\Olcs\Api\Domain\Exception\RuntimeException;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Zend\ServiceManager\ServiceManager;

/**
 * Abstract Db Query Test Case
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
abstract class AbstractDbQueryTestCase extends AbstractDbTestCase
{
    /**
     * @dataProvider paramProvider
     */
    public function testExecuteWithException($inputParams, $expectedParams)
    {
        $this->setExpectedException(RuntimeException::class);

        $this->connection->shouldReceive('prepare')
            ->with($this->getExpectedQuery())
            ->once()
            ->andThrow(new \Exception());

        $this->sut->execute($inputParams);
    }

    /**
     * @dataProvider paramProvider
     */
    public function testExecute($inputParams, $expectedParams)
    {
        $statement = m::mock();

        $this->connection->shouldReceive('prepare')
            ->with($this->getExpectedQuery())
            ->once()
            ->andReturn($statement);

        $statement->shouldReceive('execute')
            ->with($expectedParams)
            ->once()
            ->andReturn('result');

        $this->assertEquals('result', $this->sut->execute($inputParams));
    }
}
