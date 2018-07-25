<?php

/**
 * Repository Factory test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\Repository;

use Doctrine\ORM\EntityManager;
use Dvsa\Olcs\Api\Domain\DbQueryServiceManager;
use Dvsa\Olcs\Api\Domain\Repository\RepositoryFactory;
use Dvsa\Olcs\Api\Domain\RepositoryServiceManager;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Dvsa\Olcs\Api\Domain\QueryBuilder;
use Dvsa\Olcs\Api\Domain\Repository\Application;

/**
 * Repository Factory test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class RepositoryFactoryTest extends MockeryTestCase
{
    /**
     * @var RepositoryFactory
     */
    protected $sut;

    public function setUp()
    {
        $this->sut = new RepositoryFactory();
    }

    public function testCreateService()
    {
        /** @var RepositoryServiceManager $sm */
        $sm = m::mock(RepositoryServiceManager::class)->makePartial();

        $em = m::mock(EntityManager::class);
        $qb = m::mock(QueryBuilder::class);
        $dbs = m::mock(DbQueryServiceManager::class);

        $sm->setService('doctrine.entitymanager.orm_default', $em);
        $sm->setService('QueryBuilder', $qb);
        $sm->setService('DbQueryServiceManager', $dbs);

        $name = 'Application';
        $requestedName = 'Application';

        $service = $this->sut->__invoke($sm, $name);

        $this->assertInstanceOf(Application::class, $service);
    }
}
