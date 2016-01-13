<?php

/**
 * Query Partial Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\QueryPartial;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Doctrine\ORM\QueryBuilder;
use Dvsa\Olcs\Api\Domain\QueryPartial\QueryPartialInterface;

/**
 * Query Partial Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class QueryPartialTestCase extends MockeryTestCase
{
    /**
     * @var QueryPartialInterface
     */
    protected $sut;

    /**
     * @var QueryBuilder
     */
    protected $qb;

    public function setUp()
    {
        $this->qb = m::mock(QueryBuilder::class)->makePartial();

        $this->qb->select('a')
            ->from('foo', 'a');
    }
}
