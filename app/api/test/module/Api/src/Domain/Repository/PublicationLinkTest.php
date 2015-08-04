<?php

/**
 * Publication link test
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\Repository;

use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Dvsa\Olcs\Api\Entity\Publication\PublicationLink as PublicationLinkEntity;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Mockery as m;
use Dvsa\Olcs\Api\Domain\Repository\PublicationLink as PublicationLinkRepo;
use Doctrine\ORM\EntityRepository;

use Dvsa\Olcs\Api\Domain\Query\Bookmark\UnpublishedBusReg;
use Dvsa\Olcs\Api\Domain\Query\Bookmark\UnpublishedApplication;
use Dvsa\Olcs\Api\Domain\Query\Bookmark\UnpublishedPi;
use Dvsa\Olcs\Api\Domain\Query\Bookmark\PreviousPublicationByPi;
use Dvsa\Olcs\Api\Domain\Query\Bookmark\PreviousPublicationByLicence;
use Dvsa\Olcs\Api\Domain\Query\Bookmark\PreviousPublicationByApplication;

/**
 * Publication link test
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
class PublicationLinkTest extends RepositoryTestCase
{
    public function setUp()
    {
        $this->setUpSut(PublicationLinkRepo::class);
    }

    /**
     * @param $qb
     * @return m\MockInterface
     */
    private function getMockRepo($qb)
    {
        $repo = m::mock(EntityRepository::class);
        $repo->shouldReceive('createQueryBuilder')
            ->with('m')
            ->andReturn($qb);

        return $repo;
    }

    private function getPublicationAndSectionQb($query)
    {
        $mockQb = m::mock(QueryBuilder::class);
        $mockQb->shouldReceive('expr->eq')->with('m.publication', ':byPublication')->once()->andReturnSelf();
        $mockQb->shouldReceive('andWhere')->once()->andReturnSelf();
        $mockQb->shouldReceive('setParameter')
            ->with(
                'byPublication',
                $query->getPublication()
            )->once()
            ->andReturnSelf();
        $mockQb->shouldReceive('expr->eq')
            ->with('m.publicationSection', ':byPublicationSection')
            ->once()
            ->andReturnSelf();
        $mockQb->shouldReceive('andWhere')->once()->andReturnSelf();
        $mockQb->shouldReceive('setParameter')
            ->with('byPublicationSection', $query->getPublicationSection())
            ->once()
            ->andReturnSelf();

        return $mockQb;
    }

    private function getPublicationNoPubTypeTaQb($query)
    {
        $mockQb = m::mock(QueryBuilder::class);
        $mockQb->shouldReceive('expr->eq')->with('m.pubType', ':byPubType')->once()->andReturnSelf();
        $mockQb->shouldReceive('andWhere')->once()->andReturnSelf();
        $mockQb->shouldReceive('setParameter')
            ->with(
                'byPubType',
                $query->getPubType()
            )->once()
            ->andReturnSelf();
        $mockQb->shouldReceive('expr->eq')
            ->with('m.trafficArea', ':byTrafficArea')
            ->once()
            ->andReturnSelf();
        $mockQb->shouldReceive('andWhere')->once()->andReturnSelf();
        $mockQb->shouldReceive('setParameter')
            ->with('byTrafficArea', $query->getTrafficArea())
            ->once()
            ->andReturnSelf();
        $mockQb->shouldReceive('expr->lt')
            ->with('p.publicationNo', ':byPublicationNo')
            ->once()
            ->andReturnSelf();
        $mockQb->shouldReceive('andWhere')->once()->andReturnSelf();
        $mockQb->shouldReceive('setParameter')
            ->with('byPublicationNo', $query->getPublicationNo())
            ->once()
            ->andReturnSelf();
        $mockQb->shouldReceive('orderBy')
            ->with('p.publicationNo', 'DESC')
            ->once()
            ->andReturnSelf();
        $mockQb->shouldReceive('setMaxResults')
            ->with(1)
            ->once()
            ->andReturnSelf();

        return $mockQb;
    }

    /**
     * @param $query
     * @param UnpublishedPi $mockQb
     * @return mixed
     */
    private function addPi(QueryInterface $query, $mockQb)
    {
        $mockQb->shouldReceive('expr->eq')->with('m.pi', ':byPi')->once()->andReturnSelf();
        $mockQb->shouldReceive('andWhere')->once()->andReturnSelf();
        $mockQb->shouldReceive('setParameter')->with('byPi', $query->getPi())->once()->andReturnSelf();

        return $mockQb;
    }

    /**
     * @param UnpublishedBusReg $query
     * @param $mockQb
     * @return mixed
     */
    private function addBus(QueryInterface $query, $mockQb)
    {
        $mockQb->shouldReceive('expr->eq')->with('m.busReg', ':byBusReg')->once()->andReturnSelf();
        $mockQb->shouldReceive('andWhere')->once()->andReturnSelf();
        $mockQb->shouldReceive('setParameter')->with('byBusReg', $query->getBusReg())->once()->andReturnSelf();

        return $mockQb;
    }

    /**
 * @param UnpublishedApplication $query
 * @param $mockQb
 * @return mixed
 */
    private function addApplication(QueryInterface $query, $mockQb)
    {
        $mockQb->shouldReceive('expr->eq')->with('m.application', ':byApplication')->once()->andReturnSelf();
        $mockQb->shouldReceive('andWhere')->once()->andReturnSelf();
        $mockQb->shouldReceive('setParameter')
            ->with('byApplication', $query->getApplication())
            ->once()
            ->andReturnSelf();

        return $mockQb;
    }

    /**
     * @param UnpublishedApplication $query
     * @param $mockQb
     * @return mixed
     */
    private function addLicence(QueryInterface $query, $mockQb)
    {
        $mockQb->shouldReceive('expr->eq')->with('m.licence', ':byLicence')->once()->andReturnSelf();
        $mockQb->shouldReceive('andWhere')->once()->andReturnSelf();
        $mockQb->shouldReceive('setParameter')
            ->with('byLicence', $query->getLicence())
            ->once()
            ->andReturnSelf();

        return $mockQb;
    }

    /**
     * @param $mockQb
     * @param bool|false $results
     * @return mixed
     */
    private function addQueryResult($mockQb, $results = false)
    {
        if ($results === false) {
            $results = [0 => m::mock(PublicationLinkEntity::class)];
        }

        $mockQb->shouldReceive('getQuery->getResult')
            ->with(Query::HYDRATE_OBJECT)
            ->andReturn($results);

        $this->queryBuilder->shouldReceive('modifyQuery')
            ->once()
            ->with($mockQb)->andReturnSelf();

        return $mockQb;
    }

    /**
     * @param $mockQb
     * @param bool|false $results
     * @return mixed
     */
    private function addPreviousPublicationResult($mockQb, $results = false)
    {
        if ($results === false) {
            $results = [0 => m::mock(PublicationLinkEntity::class)];
        }

        $mockQb->shouldReceive('getQuery->getResult')
            ->with(Query::HYDRATE_OBJECT)
            ->andReturn($results);

        $this->queryBuilder->shouldReceive('modifyQuery')
            ->once()
            ->with($mockQb)->andReturnSelf()
            ->shouldReceive('with')
            ->with('publication', 'p')
            ->once()
            ->andReturnSelf();

        return $mockQb;
    }

    /**
     * Tests fetch unpublished application
     */
    public function testFetchSingleUnpublishedApplication()
    {
        $query = UnpublishedApplication::create(
            [
                'publication' => 123,
                'publicationSection' => 456,
                'application' => 789
            ]
        );

        $mockQb = $this->getPublicationAndSectionQb($query);
        $mockQb = $this->addApplication($query, $mockQb);
        $mockQb = $this->addQueryResult($mockQb);

        /** @var EntityRepository $repo */
        $repo = $this->getMockRepo($mockQb);

        $this->em->shouldReceive('getRepository')
            ->with(PublicationLinkEntity::class)
            ->andReturn($repo);

        $this->sut->fetchSingleUnpublished($query);
    }

    /**
     * Tests fetch unpublished pi
     */
    public function testFetchSingleUnpublishedPi()
    {
        $query = UnpublishedPi::create(
            [
                'publication' => 123,
                'publicationSection' => 456,
                'pi' => 789
            ]
        );

        $mockQb = $this->getPublicationAndSectionQb($query);
        $mockQb = $this->addPi($query, $mockQb);
        $mockQb = $this->addQueryResult($mockQb);

        /** @var EntityRepository $repo */
        $repo = $this->getMockRepo($mockQb);

        $this->em->shouldReceive('getRepository')
            ->with(PublicationLinkEntity::class)
            ->andReturn($repo);

        $this->sut->fetchSingleUnpublished($query);
    }

    /**
     * Tests fetch unpublished bus
     */
    public function testFetchSingleUnpublishedBus()
    {
        $query = UnpublishedBusReg::create(
            [
                'publication' => 123,
                'publicationSection' => 456,
                'busReg' => 789
            ]
        );

        $mockQb = $this->getPublicationAndSectionQb($query);
        $mockQb = $this->addBus($query, $mockQb);
        $mockQb = $this->addQueryResult($mockQb);

        /** @var EntityRepository $repo */
        $repo = $this->getMockRepo($mockQb);

        $this->em->shouldReceive('getRepository')
            ->with(PublicationLinkEntity::class)
            ->andReturn($repo);

        $this->sut->fetchSingleUnpublished($query);
    }

    /**
     * Tests fetch previous publication
     */
    public function testFetchPreviousPublicationPi()
    {
        $query = PreviousPublicationByPi::create(
            [
                'publicationNo' => 123,
                'pubType' => 'N&P',
                'trafficArea' => 'M',
                'pi' => 789
            ]
        );

        $mockQb = $this->getPublicationNoPubTypeTaQb($query);
        $mockQb = $this->addPi($query, $mockQb);
        $mockQb = $this->addPreviousPublicationResult($mockQb);

        /** @var EntityRepository $repo */
        $repo = $this->getMockRepo($mockQb);

        $this->em->shouldReceive('getRepository')
            ->with(PublicationLinkEntity::class)
            ->andReturn($repo);

        $this->sut->fetchPreviousPublicationNo($query);
    }

    /**
     * Tests fetch previous publication
     */
    public function testFetchPreviousPublicationApp()
    {
        $query = PreviousPublicationByApplication::create(
            [
                'publicationNo' => 123,
                'pubType' => 'N&P',
                'trafficArea' => 'M',
                'application' => 789
            ]
        );

        $mockQb = $this->getPublicationNoPubTypeTaQb($query);
        $mockQb = $this->addApplication($query, $mockQb);
        $mockQb = $this->addPreviousPublicationResult($mockQb);

        /** @var EntityRepository $repo */
        $repo = $this->getMockRepo($mockQb);

        $this->em->shouldReceive('getRepository')
            ->with(PublicationLinkEntity::class)
            ->andReturn($repo);

        $this->sut->fetchPreviousPublicationNo($query);
    }

    /**
     * Tests fetch previous publication
     */
    public function testFetchPreviousPublicationLic()
    {
        $query = PreviousPublicationByLicence::create(
            [
                'publicationNo' => 123,
                'pubType' => 'N&P',
                'trafficArea' => 'M',
                'licence' => 789
            ]
        );

        $mockQb = $this->getPublicationNoPubTypeTaQb($query);
        $mockQb = $this->addLicence($query, $mockQb);
        $mockQb = $this->addPreviousPublicationResult($mockQb);

        /** @var EntityRepository $repo */
        $repo = $this->getMockRepo($mockQb);

        $this->em->shouldReceive('getRepository')
            ->with(PublicationLinkEntity::class)
            ->andReturn($repo);

        $this->sut->fetchPreviousPublicationNo($query);
    }
}
