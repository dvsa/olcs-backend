<?php

namespace Dvsa\OlcsTest\Api\Entity\Permits;

use Dvsa\Olcs\Api\Entity\System\RefData;
use Dvsa\OlcsTest\Api\Entity\Abstracts\EntityTester;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitWindow as Entity;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitStock;
use Mockery as m;

/**
 * IrhpPermitWindow Entity Unit Tests
 *
 * Initially auto-generated but won't be overridden
 */
class IrhpPermitWindowEntityTest extends EntityTester
{
    /**
     * Define the entity to test
     *
     * @var string
     */
    protected $entityClass = Entity::class;

    public function testCreateUpdate()
    {
        $irhpPermitStock = m::mock(IrhpPermitStock::class)->makePartial();
        $startDate = '2019-10-01';
        $endDate = '2019-10-20';
        $daysForPayment = 14;

        $updatedStartDate = '2019-11-01';
        $updatedEndDate = '2019-11-20';
        $updatedDaysForPayment = "30";
        $emissionsCategory = m::mock(RefData::class);

        $entity = Entity::create($irhpPermitStock, $emissionsCategory, $startDate, $endDate, $daysForPayment);

        $this->assertEquals($irhpPermitStock, $entity->getIrhpPermitStock());
        $this->assertEquals($startDate, $entity->getStartDate()->format('Y-m-d'));
        $this->assertEquals($endDate, $entity->getEndDate()->format('Y-m-d'));
        $this->assertEquals($daysForPayment, $entity->getDaysForPayment());
        $this->assertSame($emissionsCategory, $entity->getEmissionsCategory());

        $entity->update($irhpPermitStock, $emissionsCategory, $updatedStartDate, $updatedEndDate, $updatedDaysForPayment);

        $this->assertEquals($irhpPermitStock, $entity->getIrhpPermitStock());
        $this->assertEquals($updatedStartDate, $entity->getStartDate()->format('Y-m-d'));
        $this->assertEquals($updatedEndDate, $entity->getEndDate()->format('Y-m-d'));
        $this->assertEquals($updatedDaysForPayment, $entity->getDaysForPayment());
        $this->assertSame($emissionsCategory, $entity->getEmissionsCategory());
    }
}
