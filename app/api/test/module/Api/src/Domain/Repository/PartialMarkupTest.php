<?php

namespace Dvsa\OlcsTest\Api\Domain\Repository;

use Mockery as m;
use Dvsa\Olcs\Api\Domain\Repository\PartialMarkup as Repo;

/**
 * PartialMarkupTest
 *
 * @author Andy Newton <andy@vitri.ltd>
 */
class PartialMarkupTest extends RepositoryTestCase
{
    public function setUp(): void
    {
        $this->setUpSut(Repo::class);
    }

    public function testFetchByPartialLanguage()
    {
        $queryBuilder = $this->createMockQb('BLAH');

        $this->mockCreateQueryBuilder($queryBuilder);

        $queryBuilder->shouldReceive('getQuery')->andReturn(
            m::mock()->shouldReceive('execute')
                ->shouldReceive('getOneOrNullResult')
                ->andReturn(['RESULTS'])
                ->getMock()
        );

        self::assertEquals(['RESULTS'], $this->sut->fetchByParentLanguage(1, 2));

        $expectedQuery = 'BLAH '
            . 'AND m.partial = [[1]] '
            . 'AND m.language = [[2]]';

        self::assertEquals($expectedQuery, $this->query);
    }
}
