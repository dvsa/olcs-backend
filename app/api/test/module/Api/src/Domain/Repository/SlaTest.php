<?php

/**
 * Sla Test
 *
 * @author Shaun Lizzio <shaun@lizzio.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\Repository;

use Mockery as m;
use Dvsa\Olcs\Api\Domain\Repository;

/**
 * Sla Repo Test
 *
 * @author Shaun Lizzio <shaun@lizzio.co.uk>
 */
class SlaTest extends RepositoryTestCase
{
    public function setUp()
    {
        $this->setUpSut(Repository\Sla::class);
    }

    public function testFetchByCategories()
    {
        $categories = ['foo', 'bar'];

        $qb = $this->createMockQb('QUERY');

        $this->mockCreateQueryBuilder($qb);

        $qb->shouldReceive('getQuery->getResult')->once()->andReturn('foobar');

        $result = $this->sut->fetchByCategories($categories);

        $this->assertEquals('QUERY AND m.category IN [[["foo","bar"]]]', $this->query);

        $this->assertEquals('foobar', $result);
    }
}
