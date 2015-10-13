<?php

/**
 * Role Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\Repository;

use Mockery as m;
use Dvsa\Olcs\Api\Domain\Repository;

/**
 * Role Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class RoleTest extends RepositoryTestCase
{
    public function setUp()
    {
        $this->setUpSut(Repository\Role::class);
    }

    public function testFetchOneByRole()
    {
        $role = 'foo';

        $qb = $this->createMockQb('QUERY');

        $this->mockCreateQueryBuilder($qb);

        $qb->shouldReceive('getQuery->getSingleResult')->once()->andReturn('foo');

        $result = $this->sut->fetchOneByRole($role);

        $this->assertEquals('QUERY AND m.role = [[foo]]', $this->query);

        $this->assertEquals('foo', $result);
    }
}
