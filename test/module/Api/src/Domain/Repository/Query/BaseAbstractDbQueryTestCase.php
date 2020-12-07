<?php

namespace Dvsa\OlcsTest\Api\Domain\Repository\Query;

use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManager;
use Dvsa\Olcs\Api\Domain\Exception\RuntimeException;
use Dvsa\Olcs\Api\Entity\User\User as UserEntity;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Laminas\ServiceManager\ServiceManager;
use ZfcRbac\Service\AuthorizationService;
use Dvsa\Olcs\Api\Rbac\PidIdentityProvider;

/**
 * Abstract Db Query Test Case
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
abstract class BaseAbstractDbQueryTestCase extends MockeryTestCase
{
    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @var Connection
     */
    protected $connection;

    protected $sut;

    protected $tableNameMap = [];

    protected $columnNameMap = [];

    private $metaMap = [];

    protected $mockPidIdentityProvider;

    protected $mockUserRepo;

    abstract protected function getSut();
    abstract protected function getExpectedQuery();

    public function setUp(): void
    {
        $this->connection = m::mock(Connection::class);
        $this->em = m::mock(EntityManager::class);
        $this->em->shouldReceive('getConnection')->andReturn($this->connection);

        $this->em->shouldReceive('getClassMetadata')
            ->andReturnUsing([$this, 'getClassMetadata']);

        $user = m::mock(UserEntity::class)->makePartial();
        $user->setId(1);

        $auth = m::mock(AuthorizationService::class);
        $auth->shouldReceive('getIdentity->getUser')->andReturn($user);

        $this->mockUserRepo = m::mock(\Dvsa\Olcs\Api\Domain\Repository\User::class);

        $mockRepoServiceManager = m::mock()
            ->shouldReceive('get')
            ->with('User')
            ->andReturn($this->mockUserRepo)
            ->getMock();

        $this->mockPidIdentityProvider = m::mock(PidIdentityProvider::class);

        $sm = m::mock(ServiceManager::class)->makePartial();
        $sm->shouldReceive('getServiceLocator')->andReturnSelf();
        $sm->setService('doctrine.entitymanager.orm_default', $this->em);
        $sm->setService(AuthorizationService::class, $auth);
        $sm->setService('RepositoryServiceManager', $mockRepoServiceManager);
        $sm->setService(PidIdentityProvider::class, $this->mockPidIdentityProvider);

        $sut = $this->getSut();
        $this->sut = $sut->createService($sm);

        $this->assertSame($sut, $this->sut);
    }

    public function getClassMetadata($entity)
    {
        if (empty($this->metaMap[$entity])) {
            $this->metaMap[$entity] = m::mock();

            $this->metaMap[$entity]->shouldReceive('getTableName')->andReturn($this->tableNameMap[$entity]);

            foreach ($this->columnNameMap[$entity] as $column => $details) {
                $isAssociation = isset($details['isAssociation']) ? $details['isAssociation'] : false;

                $this->metaMap[$entity]->shouldReceive('isAssociationWithSingleJoinColumn')
                    ->with($column)
                    ->andReturn($isAssociation);

                if ($isAssociation) {
                    $this->metaMap[$entity]->shouldReceive('getSingleAssociationJoinColumnName')
                        ->with($column)
                        ->andReturn($details['column']);
                } else {
                    $this->metaMap[$entity]->shouldReceive('getColumnName')
                        ->with($column)
                        ->andReturn($details['column']);
                }
            }
        }

        return $this->metaMap[$entity];
    }
}
