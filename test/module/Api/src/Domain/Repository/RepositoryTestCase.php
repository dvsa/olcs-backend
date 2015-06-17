<?php

/**
 * Repository Test Case
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\Repository;

use Doctrine\ORM\EntityManager;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Dvsa\Olcs\Api\Domain\QueryBuilderInterface;
use Dvsa\Olcs\Api\Domain\Repository\RepositoryInterface;
use Doctrine\ORM\QueryBuilder;

/**
 * Repository Test Case
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class RepositoryTestCase extends MockeryTestCase
{
    /**
     * @var RepositoryInterface
     */
    protected $sut;

    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @var QueryBuilderInterface
     */
    protected $queryBuilder;

    protected $query = '';
    protected $qb;

    public function setUpSut($class = null, $mockSut = false)
    {
        $this->em = m::mock(EntityManager::class)->makePartial();
        $this->queryBuilder = m::mock(QueryBuilderInterface::class)->makePartial();

        if ($mockSut) {
            $this->sut = m::mock($class, [$this->em, $this->queryBuilder])
                ->makePartial()
                ->shouldAllowMockingProtectedMethods();
        } else {
            $this->sut = new $class($this->em, $this->queryBuilder);
        }


        $this->query = '';
        $this->qb = null;
    }

    protected function createMockQb($query = '')
    {
        $this->query = $query;

        $this->qb = m::mock(QueryBuilder::class);

        $this->qb->shouldReceive('expr->eq')
            ->andReturnUsing([$this, 'mockExprEq']);

        $this->qb->shouldReceive('expr->isNull')
            ->andReturnUsing([$this, 'mockExprIsNull']);

        $this->qb->shouldReceive('andWhere')
            ->andReturnUsing([$this, 'mockAndWhere']);

        return $this->qb;
    }

    public function mockAndWhere($where)
    {
        $this->query .= ' AND ' . $where;

        return $this->qb;
    }

    public function mockExprEq($field, $value)
    {
        return $field . ' = ' . $value;
    }

    public function mockExprIsNull($field)
    {
        return $field . ' IS NULL';
    }
}
