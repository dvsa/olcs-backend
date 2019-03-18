<?php

namespace Dvsa\OlcsTest\Api\Domain\Repository;

use Doctrine\ORM\NoResultException;
use Dvsa\Olcs\Api\Domain\Exception\NotFoundException;
use Dvsa\Olcs\Api\Domain\Repository\Template as Repo;
use Dvsa\Olcs\Api\Entity\Template\Template;

use Mockery as m;

/**
 * TemplateTest
 */
class TemplateTest extends RepositoryTestCase
{
    /** @var m\MockInterface|Repo */
    protected $sut;

    public function setUp()
    {
        $this->setUpSut(Repo::class);
    }

    public function testFetchByLocaleFormatName()
    {
        $queryBuilder = m::mock(QueryBuilder::class);
        $this->em->shouldReceive('createQueryBuilder')->once()->andReturn($queryBuilder);

        $locale = 'en_GB';
        $format = 'plain';
        $name = 'send-ecmt-successful';

        $template = m::mock(Template::class);

        $queryBuilder->shouldReceive('select')
            ->with('t')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('from')
            ->with(Template::class, 't')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('where')
            ->with('t.locale = ?1')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('andWhere')
            ->with('t.format = ?2')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('andWhere')
            ->with('t.name = ?3')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('setParameter')
            ->with(1, $locale)
            ->once()
            ->andReturnSelf()
            ->shouldReceive('setParameter')
            ->with(2, $format)
            ->once()
            ->andReturnSelf()
            ->shouldReceive('setParameter')
            ->with(3, $name)
            ->once()
            ->andReturnSelf()
            ->shouldReceive('getQuery->getSingleResult')
            ->andReturn($template);

        $this->assertSame(
            $template,
            $this->sut->fetchByLocaleFormatName($locale, $format, $name)
        );
    }

    public function testFetchByLocaleFormatNameNotFound()
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Resource not found');

        $queryBuilder = m::mock(QueryBuilder::class);
        $this->em->shouldReceive('createQueryBuilder')->once()->andReturn($queryBuilder);

        $locale = 'en_GB';
        $format = 'plain';
        $name = 'send-ecmt-successful';

        $queryBuilder->shouldReceive('select')
            ->with('t')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('from')
            ->with(Template::class, 't')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('where')
            ->with('t.locale = ?1')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('andWhere')
            ->with('t.format = ?2')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('andWhere')
            ->with('t.name = ?3')
            ->once()
            ->andReturnSelf()
            ->shouldReceive('setParameter')
            ->with(1, $locale)
            ->once()
            ->andReturnSelf()
            ->shouldReceive('setParameter')
            ->with(2, $format)
            ->once()
            ->andReturnSelf()
            ->shouldReceive('setParameter')
            ->with(3, $name)
            ->once()
            ->andReturnSelf()
            ->shouldReceive('getQuery->getSingleResult')
            ->andThrow(new NoResultException());

        $this->sut->fetchByLocaleFormatName($locale, $format, $name);
    }

    public function testFetchAll()
    {
        $templates = [
            m::mock(Template::class),
            m::mock(Template::class),
        ];

        $queryBuilder = m::mock(QueryBuilder::class);
        $queryBuilder->shouldReceive('getQuery->getResult')
            ->andReturn($templates);

        $this->mockCreateQueryBuilder($queryBuilder);

        $this->assertEquals(
            $templates,
            $this->sut->fetchAll()
        );
    }
}
