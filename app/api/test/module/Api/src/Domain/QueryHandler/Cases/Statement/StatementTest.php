<?php

/**
 * Statement Test
 *
 * @author Shaun Lizzio <shaun@lizzio.co.uk>
 */

namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\Cases\Statement;

use DMS\PHPUnitExtensions\ArraySubset\Assert;
use Dvsa\Olcs\Api\Domain\QueryHandler\Cases\Statement\Statement;
use Dvsa\Olcs\Api\Domain\QueryHandler\Result;
use Dvsa\Olcs\Api\Entity\Cases\Statement as StatementEntity;
use Dvsa\Olcs\Api\Entity\User\User;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Dvsa\Olcs\Api\Domain\Repository\Statement as StatementRepo;
use Dvsa\Olcs\Transfer\Query\Cases\Statement\Statement as Qry;
use Mockery as m;

/**
 * Statement Test
 *
 * @author Shaun Lizzio <shaun@lizzio.co.uk>
 */
class StatementTest extends QueryHandlerTestCase
{
    public function setUp(): void
    {
        $this->sut = new Statement();
        $this->mockRepo('Statement', StatementRepo::class);

        parent::setUp();
    }

    public function testHandleQuery()
    {
        $query = Qry::create(['id' => 1]);
        $this->createMockStatement($query, null);

        /** @var Result $result */
        $result = $this->sut->handleQuery($query);
        $this->assertInstanceOf(Result::class, $result);
        Assert::assertArraySubset(
            [
                'DUMMY_STATEMENT_KEY' => 'DUMMY_STATEMENT_VALUE'
            ],
            $result->serialize(),
            true
        );
    }

    public function testHandleQueryWhenNoAssignedCaseworker()
    {
        $query = Qry::create(['id' => 1]);
        $this->createMockStatement($query, null);

        /** @var Result $result */
        $result = $this->sut->handleQuery($query);

        Assert::assertArraySubset(
            ['assignedCaseworker' => null],
            $result->serialize(),
            true
        );
    }

    public function testHandleQueryWhenAssignedCaseworker()
    {
        $query = Qry::create(['id' => 1]);

        /** @var m\Mock|User $caseworker */
        $caseworker = m::mock(User::class)
            ->shouldReceive('getId')
            ->andReturn('DUMMY_USER_ID')
            ->getMock();

        $this->createMockStatement($query, $caseworker);

        /** @var Result $result */
        $result = $this->sut->handleQuery($query);

        Assert::assertArraySubset(
            ['assignedCaseworker' => ['id' => 'DUMMY_USER_ID']],
            $result->serialize(),
            true
        );
    }

    protected function createMockStatement($query, $caseworker)
    {
        $statement = m::mock(StatementEntity::class);
        $statement->shouldReceive('getAssignedCaseworker')->andReturn($caseworker);
        $statement->shouldReceive('serialize')
            ->andReturn(['DUMMY_STATEMENT_KEY' => 'DUMMY_STATEMENT_VALUE']);

        $this->repoMap['Statement']->shouldReceive('fetchUsingId')
            ->with($query)
            ->andReturn($statement);

        return $statement;
    }
}
