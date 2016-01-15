<?php

/**
 * Goods Disc test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\Repository;

use Mockery as m;
use Dvsa\Olcs\Api\Domain\Repository\GoodsDisc as GoodsDiscRepo;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Doctrine\ORM\QueryBuilder;
use Dvsa\Olcs\Api\Entity\Licence\Licence as LicenceEntity;
use Dvsa\Olcs\Api\Entity\TrafficArea\TrafficArea as TrafficAreaEntity;
use Dvsa\Olcs\Api\Domain\Util\DateTime\DateTime;

/**
 * Goods Disc test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class GoodsDiscTest extends RepositoryTestCase
{
    public function setUp()
    {
        $this->setUpSut(GoodsDiscRepo::class);
    }

    public function testFetchDiscsToPrintNi()
    {
        $licenceType = 'ltyp_r';

        $mockQb = m::mock(QueryBuilder::class);
        $mockQb->shouldReceive('expr->eq')->with('lvlta.isNi', 1)->once()->andReturn('condition1');
        $mockQb->shouldReceive('expr->eq')->with('gd.isInterim', 1)->once()->andReturn('condition2');
        $mockQb->shouldReceive('expr->eq')
            ->with('lvalt.id', ':applicationLicenceType')->once()->andReturn('condition3');
        $mockQb->shouldReceive('expr->andX')
            ->with('condition1', 'condition2', 'condition3')->once()->andReturn('conditionAndX1');

        $mockQb->shouldReceive('expr->eq')->with('lvlta.isNi', 1)->once()->andReturn('condition5');
        $mockQb->shouldReceive('expr->eq')->with('gd.isInterim', 0)->once()->andReturn('condition6');
        $mockQb->shouldReceive('expr->eq')->with('lvllt.id', ':licenceLicenceType')->once()->andReturn('condition7');
        $mockQb->shouldReceive('expr->andX')
            ->with('condition5', 'condition6', 'condition7')->once()->andReturn('conditionAndX2');

        $mockQb->shouldReceive('expr->orX')->with('conditionAndX1', 'conditionAndX2')->once()->andReturn('and');
        $mockQb->shouldReceive('andWhere')->with('and')->once()->andReturnSelf();

        $mockQb->shouldReceive('expr->isNull')->with('gd.ceasedDate')->once()->andReturn('noCeasedDateCond');
        $mockQb->shouldReceive('expr->isNull')->with('gd.issuedDate')->once()->andReturn('noIssuedDateCond');
        $mockQb->shouldReceive('andWhere')->with('noCeasedDateCond')->once()->andReturnSelf();
        $mockQb->shouldReceive('andWhere')->with('noIssuedDateCond')->once()->andReturnSelf();

        $mockQb->shouldReceive('setParameter')
            ->with('applicationLicenceType', $licenceType)
            ->once()
            ->andReturnSelf();

        $mockQb->shouldReceive('setParameter')
            ->with('licenceLicenceType', $licenceType)
            ->once()
            ->andReturnSelf();

        $mockQb->shouldReceive('leftJoin')->with('gd.licenceVehicle', 'lv')->once()->andReturnSelf();
        $mockQb->shouldReceive('leftJoin')->with('lv.licence', 'lvl')->once()->andReturnSelf();
        $mockQb->shouldReceive('leftJoin')->with('lvl.goodsOrPsv', 'lvlgp')->once()->andReturnSelf();
        $mockQb->shouldReceive('leftJoin')->with('lvl.licenceType', 'lvllt')->once()->andReturnSelf();
        $mockQb->shouldReceive('leftJoin')->with('lvl.trafficArea', 'lvlta')->once()->andReturnSelf();
        $mockQb->shouldReceive('leftJoin')->with('lv.vehicle', 'lvv')->once()->andReturnSelf();
        $mockQb->shouldReceive('leftJoin')->with('lv.application', 'lva')->once()->andReturnSelf();
        $mockQb->shouldReceive('leftJoin')->with('lva.licenceType', 'lvalt')->once()->andReturnSelf();
        $mockQb->shouldReceive('leftJoin')->with('lva.goodsOrPsv', 'lvagp')->once()->andReturnSelf();

        $this->em->shouldReceive('getRepository->createQueryBuilder')->with('gd')->once()->andReturn($mockQb);
        $mockQb->shouldReceive('getQuery->getResult')->once()->andReturn(['result']);

        $this->sut->fetchDiscsToPrint('Y', $licenceType);
    }

    public function testFetchDiscsToPrint()
    {
        $licenceType = 'ltyp_r';

        $mockQb = m::mock(QueryBuilder::class);
        $mockQb->shouldReceive('expr->eq')->with('lvlta.isNi', 0)->once()->andReturn('condition1');
        $mockQb->shouldReceive('expr->eq')->with('gd.isInterim', 1)->once()->andReturn('condition2');
        $mockQb->shouldReceive('expr->eq')
            ->with('lvagp.id', ':operatorType')->once()->andReturn('condition3');
        $mockQb->shouldReceive('expr->eq')
            ->with('lvalt.id', ':applicationLicenceType')->once()->andReturn('condition4');
        $mockQb->shouldReceive('expr->andX')
            ->with('condition1', 'condition2', 'condition3', 'condition4')
            ->once()
            ->andReturn('conditionAndX1');

        $mockQb->shouldReceive('expr->eq')->with('lvlta.isNi', 0)->once()->andReturn('condition6');
        $mockQb->shouldReceive('expr->eq')->with('gd.isInterim', 0)->once()->andReturn('condition7');
        $mockQb->shouldReceive('expr->eq')
            ->with('lvlgp.id', ':operatorType1')->once()->andReturn('condition8');
        $mockQb->shouldReceive('expr->eq')->with('lvllt.id', ':licenceLicenceType')->once()->andReturn('condition9');
        $mockQb->shouldReceive('expr->andX')
            ->with('condition6', 'condition7', 'condition8', 'condition9')
            ->once()
            ->andReturn('conditionAndX2');

        $mockQb->shouldReceive('expr->orX')->with('conditionAndX1', 'conditionAndX2')->once()->andReturn('and');
        $mockQb->shouldReceive('andWhere')->with('and')->once()->andReturnSelf();

        $mockQb->shouldReceive('expr->isNull')->with('gd.ceasedDate')->once()->andReturn('noCeasedDateCond');
        $mockQb->shouldReceive('expr->isNull')->with('gd.issuedDate')->once()->andReturn('noIssuedDateCond');
        $mockQb->shouldReceive('andWhere')->with('noCeasedDateCond')->once()->andReturnSelf();
        $mockQb->shouldReceive('andWhere')->with('noIssuedDateCond')->once()->andReturnSelf();

        $mockQb->shouldReceive('setParameter')
            ->with('operatorType', LicenceEntity::LICENCE_CATEGORY_GOODS_VEHICLE)
            ->once()
            ->andReturnSelf();
        $mockQb->shouldReceive('setParameter')
            ->with('applicationLicenceType', $licenceType)
            ->once()
            ->andReturnSelf();
        $mockQb->shouldReceive('setParameter')
            ->with('operatorType1', LicenceEntity::LICENCE_CATEGORY_GOODS_VEHICLE)
            ->once()
            ->andReturnSelf();
        $mockQb->shouldReceive('setParameter')
            ->with('licenceLicenceType', $licenceType)
            ->once()
            ->andReturnSelf();

        $mockQb->shouldReceive('leftJoin')->with('gd.licenceVehicle', 'lv')->once()->andReturnSelf();
        $mockQb->shouldReceive('leftJoin')->with('lv.licence', 'lvl')->once()->andReturnSelf();
        $mockQb->shouldReceive('leftJoin')->with('lvl.goodsOrPsv', 'lvlgp')->once()->andReturnSelf();
        $mockQb->shouldReceive('leftJoin')->with('lvl.licenceType', 'lvllt')->once()->andReturnSelf();
        $mockQb->shouldReceive('leftJoin')->with('lvl.trafficArea', 'lvlta')->once()->andReturnSelf();
        $mockQb->shouldReceive('leftJoin')->with('lv.vehicle', 'lvv')->once()->andReturnSelf();
        $mockQb->shouldReceive('leftJoin')->with('lv.application', 'lva')->once()->andReturnSelf();
        $mockQb->shouldReceive('leftJoin')->with('lva.licenceType', 'lvalt')->once()->andReturnSelf();
        $mockQb->shouldReceive('leftJoin')->with('lva.goodsOrPsv', 'lvagp')->once()->andReturnSelf();

        $this->em->shouldReceive('getRepository->createQueryBuilder')->with('gd')->once()->andReturn($mockQb);
        $mockQb->shouldReceive('getQuery->getResult')->once()->andReturn(['result']);

        $this->sut->fetchDiscsToPrint('N', $licenceType);
    }

    public function testSetPrintingOn()
    {
        $discs = ['d1', 'd2'];
        $sut = m::mock(GoodsDiscRepo::class)->makePartial()->shouldAllowMockingProtectedMethods();
        $sut->shouldReceive('setIsPrinting')
            ->with('Y', $discs)
            ->once()
            ->getMock();

        $this->assertNull($sut->setIsPrintingOn($discs));
    }

    public function testSetPrintingOff()
    {
        $discs = ['d1', 'd2'];
        $sut = m::mock(GoodsDiscRepo::class)->makePartial()->shouldAllowMockingProtectedMethods();
        $sut->shouldReceive('setIsPrinting')
            ->with('N', $discs)
            ->once()
            ->getMock();

        $this->assertNull($sut->setIsPrintingOff($discs));
    }

    public function testSetPrinting()
    {
        $type = 'Y';
        $sut = m::mock(GoodsDiscRepo::class)->makePartial()->shouldAllowMockingProtectedMethods();

        $mockDisc = m::mock()
            ->shouldReceive('getId')
            ->andReturn(1)
            ->once()
            ->getMock();

        $mockFetched = m::mock()
            ->shouldReceive('setIsPrinting')
            ->with($type)
            ->once()
            ->getMock();

        $sut->shouldReceive('fetchById')
            ->with(1)
            ->andReturn($mockFetched)
            ->once()
            ->shouldReceive('save')
            ->with($mockFetched)
            ->once()
            ->getMock();

        $this->assertNull($sut->setIsPrinting($type, [$mockDisc]));
    }

    public function testSetIsPrintingOffAndAssignNumbers()
    {
        $type = 'N';
        $sut = m::mock(GoodsDiscRepo::class)->makePartial()->shouldAllowMockingProtectedMethods();

        $mockDisc = m::mock()
            ->shouldReceive('getId')
            ->andReturn(1)
            ->once()
            ->getMock();

        $mockFetched = m::mock()
            ->shouldReceive('setIsPrinting')
            ->with($type)
            ->once()
            ->shouldReceive('setDiscNo')
            ->with(1)
            ->once()
            ->shouldReceive('setIssuedDate')
            ->with(m::type(DateTime::class))
            ->once()
            ->getMock();

        $sut->shouldReceive('fetchById')
            ->with(1)
            ->andReturn($mockFetched)
            ->once()
            ->shouldReceive('save')
            ->with($mockFetched)
            ->once()
            ->getMock();

        $this->assertNull($sut->setIsPrintingOffAndAssignNumbers([$mockDisc], 1));
    }
}
