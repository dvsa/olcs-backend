<?php

namespace Dvsa\OlcsTest\Api\Entity\Licence;

use Dvsa\OlcsTest\Api\Entity\Abstracts\EntityTester;
use Dvsa\Olcs\Api\Entity\Licence\ContinuationDetail as Entity;
use Mockery as m;

/**
 * ContinuationDetail Entity Unit Tests
 */
class ContinuationDetailEntityTest extends EntityTester
{
    /**
     * Define the entity to test
     *
     * @var string
     */
    protected $entityClass = Entity::class;

    public function testGetRelatedOrganisation()
    {
        /** @var Entity $continuationDetail */
        $continuationDetail = m::mock(Entity::class)->makePartial();
        $continuationDetail->shouldReceive('getLicence->getOrganisation')->andReturn('ORG');

        $this->assertEquals('ORG', $continuationDetail->getRelatedOrganisation());
    }

    public function dpGetAmountDeclaredDataProvider()
    {
        return [
            [0.00, null, null, null, null],
            [0.00, 0, 0, 0, 0],
            [10.00, 1, 2, 3, 4],
            [10.10, 1.01, 2.02, 3.03, 4.04],
            [1.01, 1.01, null, null, null],
            [1.01, null, 1.01, null, null],
            [1.01, null, null, 1.01, null],
            [1.01, null, null, null, 1.01],
        ];
    }

    /**
     * @dataProvider dpGetAmountDeclaredDataProvider
     */
    public function testGetAmountDeclared(
        $expected,
        $averageBalanceAmount,
        $overdraftAmount,
        $factoringAmount,
        $otherFinancesAmount
    ) {
        $continuationDetail = new Entity();
        $continuationDetail->setAverageBalanceAmount($averageBalanceAmount);
        $continuationDetail->setOverdraftAmount($overdraftAmount);
        $continuationDetail->setFactoringAmount($factoringAmount);
        $continuationDetail->setOtherFinancesAmount($otherFinancesAmount);

        $this->assertSame($expected, $continuationDetail->getAmountDeclared());
    }

    public function testGetContextValue()
    {
        $continuationDetail = new Entity();
        $continuationDetail->setId(87);

        $this->assertSame(87, $continuationDetail->getContextValue());
    }
}
