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

    public function setUpSut($class = null)
    {
        $this->em = m::mock(EntityManager::class)->makePartial();
        $this->queryBuilder = m::mock(QueryBuilderInterface::class)->makePartial();
        $this->sut = new $class($this->em, $this->queryBuilder);
    }
}
