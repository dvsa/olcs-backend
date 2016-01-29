<?php

/**
 * Repository Test Case
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\Repository;

use Doctrine\ORM\EntityManager;
use Dvsa\Olcs\Api\Domain\DbQueryServiceManager;
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

    /**
     * @var DbQueryServiceManager
     */
    protected $dbQueryService;

    protected $query = '';
    protected $qb;

    public function setUpSut($class = null, $mockSut = false)
    {
        $this->em = m::mock(EntityManager::class);
        $this->queryBuilder = m::mock(QueryBuilderInterface::class);
        $this->dbQueryService = m::mock(DbQueryServiceManager::class);

        if ($mockSut) {
            $this->sut = m::mock($class, [$this->em, $this->queryBuilder, $this->dbQueryService])
                ->makePartial()
                ->shouldAllowMockingProtectedMethods();
        } else {
            $this->sut = new $class($this->em, $this->queryBuilder, $this->dbQueryService);
        }

        $this->query = '';
        $this->qb = null;
    }

    protected function mockCreateQueryBuilder($mock)
    {
        $this->em->shouldReceive('getRepository->createQueryBuilder')
            ->andReturn($mock);
    }

    protected function createMockQb($query = '')
    {
        $this->query = $query;

        $this->qb = m::mock(QueryBuilder::class);

        $this->qb->shouldReceive('expr->eq')
            ->andReturnUsing([$this, 'mockExprEq']);

        $this->qb->shouldReceive('expr->neq')
            ->andReturnUsing([$this, 'mockExprNeq']);

        $this->qb->shouldReceive('expr->lte')
            ->andReturnUsing([$this, 'mockExprLte']);

        $this->qb->shouldReceive('expr->lt')
            ->andReturnUsing([$this, 'mockExprLt']);

        $this->qb->shouldReceive('expr->gte')
            ->andReturnUsing([$this, 'mockExprGte']);

        $this->qb->shouldReceive('expr->gt')
            ->andReturnUsing([$this, 'mockExprGt']);

        $this->qb->shouldReceive('expr->isNull')
            ->andReturnUsing([$this, 'mockExprIsNull']);

        $this->qb->shouldReceive('expr->in')
            ->andReturnUsing([$this, 'mockExprIn']);

        $this->qb->shouldReceive('expr->isNotNull')
            ->andReturnUsing([$this, 'mockExprIsNotNull']);

        $this->qb->shouldReceive('expr->like')
            ->andReturnUsing([$this, 'mockExprLike']);

        $this->qb->shouldReceive('expr->orX')
            ->andReturnUsing([$this, 'mockOrX']);

        $this->qb->shouldReceive('expr->andX')
            ->andReturnUsing([$this, 'mockAndX']);

        $this->qb->shouldReceive('expr->isNull')
            ->andReturnUsing([$this, 'mockIsNull']);

        $this->qb->shouldReceive('addSelect')
            ->andReturnUsing([$this, 'mockAddSelect']);

        $this->qb->shouldReceive('andWhere')
            ->andReturnUsing([$this, 'mockAndWhere']);

        $this->qb->shouldReceive('orWhere')
            ->andReturnUsing([$this, 'mockOrWhere']);

        $this->qb->shouldReceive('innerJoin')
            ->andReturnUsing([$this, 'mockInnerJoin']);

        $this->qb->shouldReceive('leftJoin')
            ->andReturnUsing([$this, 'mockLeftJoin']);

        $this->qb->shouldReceive('orderBy')
            ->andReturnUsing([$this, 'mockOrderBy']);

        $this->qb->shouldReceive('setParameter')
            ->andReturnUsing([$this, 'mockSetParameter']);

        return $this->qb;
    }

    public function mockOrderBy($sort, $order)
    {
        $this->query .= ' ORDER BY ' . $sort . ' ' . $order;

        return $this->qb;
    }

    public function mockSetParameter($name, $value)
    {
        $value = $this->formatValue($value);

        $this->query = str_replace(':' . $name, '[[' . $value . ']]', $this->query);

        return $this->qb;
    }

    public function mockAddSelect($select)
    {
        $this->query .= ' SELECT ' . $select;

        return $this->qb;
    }

    public function mockAndWhere($where)
    {
        $this->query .= ' AND ' . $where;

        return $this->qb;
    }

    public function mockIsNull($where)
    {
        $this->query .= $where . ' IS NULL';

        return $this->qb;
    }

    public function mockOrWhere($where)
    {
        $this->query .= ' OR ' . $where;

        return $this->qb;
    }

    public function mockInnerJoin($field, $alias, $type = null, $condition = null)
    {
        $this->query .= ' INNER JOIN ' . $field . ' ' . $alias;

        if ($condition !== null) {
            $this->query .= ' ' . $type;
            $this->query .= ' ' . $condition;
        }

        return $this->qb;
    }

    public function mockLeftJoin($field, $alias, $type = null, $condition = null)
    {
        $this->query .= ' LEFT JOIN ' . $field . ' ' . $alias;

        if ($condition !== null) {
            $this->query .= ' ' . $type;
            $this->query .= ' ' . $condition;
        }

        return $this->qb;
    }

    public function mockExprEq($field, $value)
    {
        $value = $this->formatValue($value);

        return $field . ' = ' . $value;
    }

    public function mockExprNeq($field, $value)
    {
        $value = $this->formatValue($value);

        return $field . ' != ' . $value;
    }

    public function mockExprLte($field, $value)
    {
        $value = $this->formatValue($value);

        return $field . ' <= ' . $value;
    }

    public function mockExprLt($field, $value)
    {
        $value = $this->formatValue($value);

        return $field . ' < ' . $value;
    }

    public function mockExprGte($field, $value)
    {
        $value = $this->formatValue($value);

        return $field . ' >= ' . $value;
    }

    public function mockExprGt($field, $value)
    {
        $value = $this->formatValue($value);

        return $field . ' > ' . $value;
    }

    public function mockExprIn($field, $value)
    {
        $value = $this->formatValue($value);

        return $field . ' IN ' . $value;
    }

    public function mockExprIsNull($field)
    {
        return $field . ' IS NULL';
    }

    public function mockExprIsNotNull($field)
    {
        return $field . ' IS NOT NULL';
    }

    public function mockExprLike($field, $value)
    {
        $value = $this->formatValue($value);

        return $field . ' LIKE ' . $value;
    }

    public function mockOrX()
    {
        return '(' . implode(' OR ', func_get_args()) . ')';
    }

    public function mockAndX()
    {
        return '(' . implode(' AND ', func_get_args()) . ')';
    }

    protected function formatValue($value)
    {
        if (is_array($value)) {
            $value = json_encode($value);
        }

        if (is_a($value, \DateTime::class)) {
            return $value->format(\DateTime::W3C);
        }

        if ($value instanceof \Dvsa\Olcs\Api\Entity\System\RefData) {
            return $value->getId();
        }

        if (is_object($value)) {
            $value = get_class($value);
        }

        if (is_bool($value)) {
            $value = $value ? 'true' : 'false';
        }

        return $value;
    }

    protected function expectQueryWithData($queryName, $data = [])
    {
        $query = m::mock();
        $query->shouldReceive('execute')
            ->once()
            ->with($data);

        $this->dbQueryService->shouldReceive('get')
            ->with($queryName)
            ->andReturn($query);
    }
}
