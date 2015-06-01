<?php

/**
 * Fee test
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\Repository;

use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Dvsa\Olcs\Api\Entity\System\Category;
use Dvsa\Olcs\Api\Entity\System\SubCategory;
use Dvsa\Olcs\Api\Entity\System\RefData;
use Doctrine\DBAL\LockMode;
use Dvsa\Olcs\Api\Entity\Application\Application;
use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Mockery as m;
use Dvsa\Olcs\Api\Domain\Exception\RuntimeException;
use Dvsa\Olcs\Api\Domain\Repository\Fee as FeeRepo;
use Doctrine\ORM\OptimisticLockException;
use Dvsa\Olcs\Api\Domain\Exception\VersionConflictException;
use Doctrine\ORM\EntityRepository;
use Dvsa\Olcs\Api\Domain\Exception\NotFoundException;

/**
 * Fee test
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class FeeTest extends RepositoryTestCase
{
    public function setUp()
    {
        $this->setUpSut(FeeRepo::class);
    }

    private function setupFetchInterimFeesByApplicationId($mockQb, $applicationId)
    {
        $this->em->shouldReceive('getRepository->createQueryBuilder')->with('f')->once()->andReturn($mockQb);
        $this->queryBuilder->shouldReceive('withRefdata')->with()->once();

        $mockQb->shouldReceive('join')->with('f.feeType', 'ft')->once()->andReturnSelf();
        //$mockQb->shouldReceive('expr->eq')->with('f.application', ':applicationId')->once()->andReturnSelf();

        $mockQb->shouldReceive('expr->eq')->with('ft.feeType', ':feeTypeFeeType')->once()->andReturn('foo');
        $mockQb->shouldReceive('andWhere')->with('foo')->once()->andReturnSelf();

        $mockQb->shouldReceive('expr->eq')->with('f.application', ':applicationId')->once()->andReturn('bar');
        $mockQb->shouldReceive('andWhere')->with('bar')->once()->andReturnSelf();

        $mockQb->shouldReceive('orderBy')->with('f.invoicedDate')->once()->andReturnSelf();
        $this->em->shouldReceive('getReference')->with(
            \Dvsa\Olcs\Api\Entity\System\RefData::class,
            \Dvsa\Olcs\Api\Entity\Fee\FeeType::FEE_TYPE_GRANTINT
        )->once()->andReturn('refdata');
        $mockQb->shouldReceive('setParameter')->with('feeTypeFeeType', 'refdata')->once()->andReturnSelf();
        $mockQb->shouldReceive('setParameter')->with('applicationId', $applicationId)->once()->andReturnSelf();

        $mockQb->shouldReceive('getQuery->getResult')->once()->andReturn('result');
    }

    public function testFetchInterimFeesByApplicationId()
    {
        $mockQb = m::mock();

        $this->setupFetchInterimFeesByApplicationId($mockQb, 33);

        $this->assertSame('result', $this->sut->fetchInterimFeesByApplicationId(33));
    }

    public function testFetchInterimFeesByApplicationIdOutstanding()
    {
        $mockQb = m::mock();

        $this->setupFetchInterimFeesByApplicationId($mockQb, 12);

        $this->em->shouldReceive('getReference')->with(
            \Dvsa\Olcs\Api\Entity\System\RefData::class,
            \Dvsa\Olcs\Api\Entity\Fee\Fee::STATUS_OUTSTANDING
        )->once()->andReturn('ot');
        $this->em->shouldReceive('getReference')->with(
            \Dvsa\Olcs\Api\Entity\System\RefData::class,
            \Dvsa\Olcs\Api\Entity\Fee\Fee::STATUS_WAIVE_RECOMMENDED
        )->once()->andReturn('wr');

        $mockQb->shouldReceive('expr->in')->with('f.feeStatus', ':feeStatus')->once()->andReturn('expr-in');
        $mockQb->shouldReceive('andWhere')->with('expr-in')->once()->andReturnSelf();
        $mockQb->shouldReceive('setParameter')->with('feeStatus', ['ot', 'wr'])->once();

        $this->assertSame('result', $this->sut->fetchInterimFeesByApplicationId(12, true));
    }
}
