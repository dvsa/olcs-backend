<?php

namespace Dvsa\OlcsTest\Api\Entity\Permits;

use Dvsa\OlcsTest\Api\Entity\Abstracts\EntityTester;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitRange as Entity;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitStock;
use Mockery as m;

/**
 * IrhpPermitRange Entity Unit Tests
 *
 * Initially auto-generated but won't be overridden
 */
class IrhpPermitRangeEntityTest extends EntityTester
{
    /**
     * Define the entity to test
     *
     * @var string
     */
    protected $entityClass = Entity::class;

    public function testCreateUpdate()
    {
        $irhpPermitStock = m::mock(IrhpPermitStock::class);
        $prefix = "UK";
        $fromNo = "1";
        $toNo = "150";
        $isReserve = 0;
        $isReplacement = 0;
        $countrys = [];

        $updatedPrefix = "AU";
        $updatedFromNo = "10";
        $updatedToNo = "1500";
        $updatedIsReserve = 1;
        $updatedCountrys = ['AU'];

        $entity = Entity::create($irhpPermitStock, $prefix, $fromNo, $toNo, $isReserve, $isReplacement, $countrys);

        $this->assertEquals($irhpPermitStock, $entity->getIrhpPermitStock());
        $this->assertEquals($prefix, $entity->getPrefix());
        $this->assertEquals($fromNo, $entity->getFromNo());
        $this->assertEquals($toNo, $entity->getToNo());
        $this->assertEquals($isReserve, $entity->getSsReserve());
        $this->assertEquals($isReplacement, $entity->getLostReplacement());
        $this->assertEquals($countrys, $entity->getCountrys());

        $entity->update($irhpPermitStock, $updatedPrefix, $updatedFromNo, $updatedToNo, $updatedIsReserve, $isReplacement, $updatedCountrys);

        $this->assertEquals($irhpPermitStock, $entity->getIrhpPermitStock());
        $this->assertEquals($updatedPrefix, $entity->getPrefix());
        $this->assertEquals($updatedFromNo, $entity->getFromNo());
        $this->assertEquals($updatedToNo, $entity->getToNo());
        $this->assertEquals($updatedIsReserve, $entity->getSsReserve());
        $this->assertEquals($isReplacement, $entity->getLostReplacement());
        $this->assertEquals($updatedCountrys, $entity->getCountrys());
    }

    public function testGetSize()
    {
        $irhpPermitStock = m::mock(IrhpPermitStock::class);
        $prefix = "UK";
        $fromNo = "75";
        $toNo = "150";
        $isReserve = 0;
        $isReplacement = 0;
        $countrys = [];

        $entity = Entity::create($irhpPermitStock, $prefix, $fromNo, $toNo, $isReserve, $isReplacement, $countrys);

        $this->assertEquals(76, $entity->getSize());
    }
}
