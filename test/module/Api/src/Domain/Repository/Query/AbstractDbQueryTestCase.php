<?php

namespace Dvsa\OlcsTest\Api\Domain\Repository\Query;

use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManager;
use Dvsa\Olcs\Api\Domain\Exception\RuntimeException;
use Dvsa\Olcs\Api\Entity\User\User as UserEntity;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Zend\ServiceManager\ServiceManager;
use ZfcRbac\Service\AuthorizationService;
use Dvsa\Olcs\Api\Rbac\PidIdentityProvider;

/**
 * Abstract Db Query Test Case
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
abstract class AbstractDbQueryTestCase extends BaseAbstractDbQueryTestCase
{
    abstract public function paramProvider();

    /**
     * @dataProvider paramProvider
     */
    public function testExecuteWithException($inputParams, $inputTypes, $expectedParams, $expectedTypes)
    {
        $this->mockPidIdentityProvider
            ->shouldReceive('getMasqueradedAsSystemUser')
            ->andReturn(false);

        // add generic params
        $expectedParams['currentUserId'] = 1;

        $this->expectException(RuntimeException::class);

        $this->connection->shouldReceive('executeQuery')
            ->with($this->getExpectedQuery(), $expectedParams, $expectedTypes)
            ->once()
            ->andThrow(new \Exception());

        $this->sut->execute($inputParams, $inputTypes);
    }

    /**
     * @dataProvider paramProvider
     */
    public function testExecute($inputParams, $inputTypes, $expectedParams, $expectedTypes)
    {
        $this->mockPidIdentityProvider
            ->shouldReceive('getMasqueradedAsSystemUser')
            ->andReturn(false);

        // add generic params
        $expectedParams['currentUserId'] = 1;

        $this->connection->shouldReceive('executeQuery')
            ->with($this->getExpectedQuery(), $expectedParams, $expectedTypes)
            ->once()
            ->andReturn('result');

        $this->assertEquals('result', $this->sut->execute($inputParams, $inputTypes));
    }

    /**
     * @dataProvider paramProvider
     */
    public function testExecuteAsSystemUser($inputParams, $inputTypes, $expectedParams, $expectedTypes)
    {
        $this->mockPidIdentityProvider
            ->shouldReceive('getMasqueradedAsSystemUser')
            ->andReturn(true);

        $user = m::mock(UserEntity::class)->makePartial();
        $user->setId(PidIdentityProvider::SYSTEM_USER);

        $this->mockUserRepo
            ->shouldReceive('fetchById')
            ->with(PidIdentityProvider::SYSTEM_USER)
            ->andReturn($user);

        // add generic params
        $expectedParams['currentUserId'] = PidIdentityProvider::SYSTEM_USER;

        $this->connection->shouldReceive('executeQuery')
            ->with($this->getExpectedQuery(), $expectedParams, $expectedTypes)
            ->once()
            ->andReturn('result');

        $this->assertEquals('result', $this->sut->execute($inputParams, $inputTypes));
    }
}
