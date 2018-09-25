<?php

namespace Dvsa\OlcsTest\Api\Entity\Permits;

use Dvsa\OlcsTest\Api\Entity\Abstracts\EntityTester;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitStock as Entity;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitType;
use Dvsa\Olcs\Api\Entity\System\RefData;
use Mockery as m;
use Dvsa\Olcs\Api\Entity\Traits\ProcessDateTrait;

/**
 * IrhpPermitStock Entity Unit Tests
 *
 * Initially auto-generated but won't be overridden
 */
class IrhpPermitStockEntityTest extends EntityTester
{
    use ProcessDateTrait;
    /**
     * Define the entity to test
     *
     * @var string
     */
    protected $entityClass = Entity::class;

    public function testCreateUpdate()
    {
        $irhpPermitType = m::mock(IrhpPermitType::class)->makePartial();
        $validFrom = '2019-01-01';
        $expectedFrom = $this->processDate($validFrom, 'Y-m-d');
        $validTo = '2019-02-01';
        $expectedTo = $this->processDate($validTo, 'Y-m-d');
        $initialStock = 1400;

        $updateValidFrom = '2019-02-01';
        $updateExpectedFrom = $this->processDate($updateValidFrom, 'Y-m-d');
        $updateValidTo = '2019-02-02';
        $updateExpectedTo = $this->processDate($updateValidTo, 'Y-m-d');
        $updateInitialStock = 1401;
        $status = m::mock(RefData::class);

        $entity = Entity::create($irhpPermitType, $validFrom, $validTo, $initialStock, $status);

        $this->assertEquals($irhpPermitType, $entity->getIrhpPermitType());
        $this->assertEquals($expectedFrom, $entity->getValidFrom());
        $this->assertEquals($expectedTo, $entity->getValidTo());
        $this->assertEquals($initialStock, $entity->getInitialStock());
        $this->assertEquals($status, $entity->getStatus());

        $entity->update($irhpPermitType, $updateValidFrom, $updateValidTo, $updateInitialStock);

        $this->assertEquals($updateExpectedFrom, $entity->getValidFrom());
        $this->assertEquals($updateExpectedTo, $entity->getValidTo());
        $this->assertEquals($updateInitialStock, $entity->getInitialStock());
    }
}
