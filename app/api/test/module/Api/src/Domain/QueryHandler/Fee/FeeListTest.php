<?php

/**
 * Fee List Test
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\Fee;

use Doctrine\ORM\Query as DoctrineQuery;
use Dvsa\Olcs\Api\Domain\QueryHandler\Fee\FeeList as QueryHandler;
use Dvsa\Olcs\Api\Domain\QueryHandler\Result;
use Dvsa\Olcs\Api\Domain\Repository;
use Dvsa\Olcs\Api\Entity\Application\Application;
use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Dvsa\Olcs\Transfer\Query\Fee\FeeList as Qry;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Mockery as m;

/**
 * Fee List Test
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class FeeListTest extends QueryHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new QueryHandler();
        $this->mockRepo('Fee', Repository\Fee::class);
        $this->mockRepo('Licence', Repository\Licence::class);
        $this->mockRepo('Application', Repository\Application::class);

        parent::setUp();
    }

    public function testHandleQuery()
    {
        $query = Qry::create([]);

        $fee1 = m::mock()->shouldReceive('serialize')->andReturn(['id' => 1])->getMock();
        $fee2 = m::mock()->shouldReceive('serialize')->andReturn(['id' => 2])->getMock();
        $mockList = [$fee1, $fee2];

        $this->repoMap['Fee']
            ->shouldReceive('fetchList')
            ->with($query, DoctrineQuery::HYDRATE_OBJECT)
            ->once()
            ->andReturn($mockList)
            ->shouldReceive('fetchCount')
            ->with($query)
            ->andReturn(2);

        $result = $this->sut->handleQuery($query);

        $expected = [
            'result' => [
                ['id' => 1],
                ['id' => 2],
            ],
            'count' => 2,
            'allowFeePayments' => true,
        ];

        $this->assertEquals($expected, $result);
    }

    public function testHandleQueryWithLicence()
    {
        $licenceId = 7;
        $query = Qry::create(['licence' => $licenceId]);

        $fee1 = m::mock()->shouldReceive('serialize')->andReturn(['id' => 1])->getMock();
        $fee2 = m::mock()->shouldReceive('serialize')->andReturn(['id' => 2])->getMock();
        $mockList = [$fee1, $fee2];

        $this->repoMap['Fee']
            ->shouldReceive('fetchList')
            ->with($query, DoctrineQuery::HYDRATE_OBJECT)
            ->once()
            ->andReturn($mockList)
            ->shouldReceive('fetchCount')
            ->with($query)
            ->andReturn(2);

        $this->repoMap['Licence']
            ->shouldReceive('fetchById')
            ->once()
            ->with($licenceId)
            ->andReturn(
                m::mock(Licence::class)
                    ->shouldReceive('allowFeePayments')
                    ->andReturn(false)
                    ->getMock()
            );

        $result = $this->sut->handleQuery($query);

        $expected = [
            'result' => [
                ['id' => 1],
                ['id' => 2],
            ],
            'count' => 2,
            'allowFeePayments' => false,
        ];

        $this->assertEquals($expected, $result);
    }

    public function testHandleQueryWithApplication()
    {
        $applicationId = 7;
        $query = Qry::create(['application' => $applicationId]);

        $fee1 = m::mock()->shouldReceive('serialize')->andReturn(['id' => 1])->getMock();
        $fee2 = m::mock()->shouldReceive('serialize')->andReturn(['id' => 2])->getMock();
        $mockList = [$fee1, $fee2];

        $this->repoMap['Fee']
            ->shouldReceive('fetchList')
            ->with($query, DoctrineQuery::HYDRATE_OBJECT)
            ->once()
            ->andReturn($mockList)
            ->shouldReceive('fetchCount')
            ->with($query)
            ->andReturn(2);

        $this->repoMap['Application']
            ->shouldReceive('fetchById')
            ->once()
            ->with($applicationId)
            ->andReturn(
                m::mock(Application::class)
                    ->shouldReceive('allowFeePayments')
                    ->andReturn(false)
                    ->getMock()
            );

        $result = $this->sut->handleQuery($query);

        $expected = [
            'result' => [
                ['id' => 1],
                ['id' => 2],
            ],
            'count' => 2,
            'allowFeePayments' => false,
        ];

        $this->assertEquals($expected, $result);
    }
}
