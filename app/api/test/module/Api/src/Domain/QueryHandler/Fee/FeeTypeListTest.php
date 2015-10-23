<?php

/**
 * FeeType List Test
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\Fee;

use Doctrine\ORM\Query as DoctrineQuery;
use Dvsa\Olcs\Api\Domain\QueryHandler\Fee\FeeTypeList as QueryHandler;
use Dvsa\Olcs\Api\Domain\QueryHandler\Result;
use Dvsa\Olcs\Api\Domain\Repository;
use Dvsa\Olcs\Api\Entity\Fee\FeeType as Entity;
use Dvsa\Olcs\Api\Entity\Fee\System\RefData;
use Dvsa\Olcs\Api\Entity\Irfo\IrfoGvPermit;
use Dvsa\Olcs\Api\Entity\Irfo\IrfoPsvAuth;
use Dvsa\Olcs\Api\Entity\Organisation\Organisation;
use Dvsa\Olcs\Transfer\Query\Fee\FeeTypeList as Qry;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Mockery as m;

/**
 * FeeType List Test
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class FeeTypeListTest extends QueryHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new QueryHandler();
        $this->mockRepo('FeeType', Repository\FeeType::class);
        $this->mockRepo('IrfoGvPermit', Repository\IrfoGvPermit::class);
        $this->mockRepo('IrfoPsvAuth', Repository\IrfoPsvAuth::class);

        parent::setUp();
    }

    public function testHandleQueryNonIrfo()
    {
        $query = Qry::create([]);

        $feeTypeRefData = m::mock(RefData::class)
            ->shouldReceive('getId')
            ->andReturn(99)
            ->getMock();

        $feeType1 = m::mock(Entity::class)
            ->shouldReceive('getId')
            ->andReturn(1)
            ->shouldReceive('getDescription')
            ->andReturn('fee type 1')
            ->shouldReceive('getIrfoFeeType')
            ->andReturn(null)
            ->shouldReceive('getFeeType')
            ->andReturn($feeTypeRefData)
            ->shouldReceive('getEffectiveFrom')
            ->andReturn('2015-10-23')
            ->shouldReceive('serialize')
            ->andReturn(['id' => 1])
            ->getMock();
        $feeType2 = m::mock(Entity::class)
            ->shouldReceive('getId')
            ->andReturn(2)
            ->shouldReceive('getDescription')
            ->andReturn('fee type 2')
            ->shouldReceive('getIrfoFeeType')
            ->andReturn(null)
            ->shouldReceive('getFeeType')
            ->andReturn($feeTypeRefData)
            ->shouldReceive('getEffectiveFrom')
            ->andReturn('2014-10-23')
            ->shouldReceive('serialize')
            ->andReturn(['id' => 2])
            ->getMock();
        $mockList = new \ArrayObject([$feeType1, $feeType2]);

        $this->repoMap['FeeType']
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
            ],
            'count' => 1,
            'valueOptions' => [
                'feeType' => [
                    // we should only get the latest as feeType1 and feeType2 have the same feeType
                    1 => 'fee type 1',
                ],
                'irfoGvPermit' => [],
                'irfoPsvAuth' => [],
            ],
         ];

        $this->assertEquals($expected, $result);
    }

    public function testHandleQueryIrfo()
    {
        $query = Qry::create(['organisation' => 123]);

        $feeTypeRefData = m::mock(RefData::class)
            ->shouldReceive('getId')
            ->andReturn(99)
            ->getMock();
        $irfoFeeTypeRefData = m::mock(RefData::class)
            ->shouldReceive('getId')
            ->andReturn(101)
            ->getMock();

        $feeType1 = m::mock(Entity::class)
            ->shouldReceive('getId')
            ->andReturn(1)
            ->shouldReceive('getDescription')
            ->andReturn('irfo fee type 1')
            ->shouldReceive('getIrfoFeeType')
            ->andReturn($irfoFeeTypeRefData)
            ->shouldReceive('getEffectiveFrom')
            ->andReturn('2015-10-23')
            ->shouldReceive('serialize')
            ->andReturn(['id' => 1])
            ->getMock();
        $feeType2 = m::mock(Entity::class)
            ->shouldReceive('getId')
            ->andReturn(2)
            ->shouldReceive('getDescription')
            ->andReturn('irfo fee type 2')
            ->shouldReceive('getIrfoFeeType')
            ->andReturn($irfoFeeTypeRefData)
            ->shouldReceive('getEffectiveFrom')
            ->andReturn('2014-10-23')
            ->shouldReceive('serialize')
            ->andReturn(['id' => 2])
            ->getMock();
        $mockList = new \ArrayObject([$feeType1, $feeType2]);

        $this->repoMap['FeeType']
            ->shouldReceive('fetchList')
            ->with($query, DoctrineQuery::HYDRATE_OBJECT)
            ->once()
            ->andReturn($mockList)
            ->shouldReceive('fetchCount')
            ->with($query)
            ->andReturn(2);

        $mockOrganisation = m::mock(Organisation::class);
        $mockPermit = m::mock(IrfoGvPermit::class);
        $mockPermit
            ->shouldReceive('getId')
            ->andReturn(69);
        $mockPermit
            ->shouldReceive('getIrfoGvPermitType->getDescription')
            ->once()
            ->andReturn('permit description');
        $mockAuth = m::mock(IrfoPsvAuth::class);
        $mockAuth
            ->shouldReceive('getId')
            ->andReturn(69);
        $mockAuth
            ->shouldReceive('getIrfoPsvAuthType->getDescription')
            ->once()
            ->andReturn('auth description');
        $this->repoMap['FeeType']
            ->shouldReceive('getReference')
            ->twice()
            ->with(Organisation::class, 123)
            ->andReturn($mockOrganisation);
        $this->repoMap['IrfoGvPermit']
            ->shouldReceive('fetchByOrganisation')
            ->once()
            ->with($mockOrganisation)
            ->andReturn([$mockPermit]);
        $this->repoMap['IrfoPsvAuth']
            ->shouldReceive('fetchByOrganisation')
            ->once()
            ->with($mockOrganisation)
            ->andReturn([$mockAuth]);

        $result = $this->sut->handleQuery($query);

        $expected = [
            'result' => [
                ['id' => 1],
            ],
            'count' => 1,
            'valueOptions' => [
                'feeType' => [
                    // we should only get the latest as feeType1 and feeType2 have the same irfoFeeType
                    1 => 'irfo fee type 1',
                ],
                'irfoGvPermit' => [
                    69 => '69 (permit description)',
                ],
                'irfoPsvAuth' => [
                    69 => '69 (auth description)',
                ],
            ],
         ];

        $this->assertEquals($expected, $result);
    }
}
