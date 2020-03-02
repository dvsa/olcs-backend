<?php

namespace Dvsa\OlcsTest\Api\Domain\Repository;

use Mockery as m;
use Dvsa\Olcs\Api\Domain\Repository\TranslationKeyText as Repo;

/**
 * TranslationKeyTextTest
 *
 * @author Andy Newton <andy@vitri.ltd>
 */
class TranslationKeyTextTest extends RepositoryTestCase
{
    public function setUp()
    {
        $this->setUpSut(Repo::class);
    }

    public function testFetchByTranslationKeyLanguage()
    {
        $queryBuilder = $this->createMockQb('BLAH');

        $this->mockCreateQueryBuilder($queryBuilder);

        $queryBuilder->shouldReceive('getQuery')->andReturn(
            m::mock()->shouldReceive('execute')
                ->shouldReceive('getOneOrNullResult')
                ->andReturn(['RESULTS'])
                ->getMock()
        );

        self::assertEquals(['RESULTS'], $this->sut->fetchByTranslationKeyLanguage(1, 2));

        $expectedQuery = 'BLAH '
            . 'AND m.translationKey = [[1]] '
            . 'AND m.language = [[2]]';

        self::assertEquals($expectedQuery, $this->query);
    }
}
