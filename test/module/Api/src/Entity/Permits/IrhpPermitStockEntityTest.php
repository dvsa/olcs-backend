<?php

namespace Dvsa\OlcsTest\Api\Entity\Permits;

use Dvsa\OlcsTest\Api\Entity\Abstracts\EntityTester;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitStock as Entity;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitType;
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
        $validFrom = ['day' => '01', 'month' => '01', 'year' => '2019'];
        $expectedFrom = $this->processDate('01-01-2019', 'd-m-Y');
        $validTo = ['day' => '01', 'month' => '02', 'year' => '2019'];
        $expectedTo = $this->processDate('01-02-2019', 'd-m-Y');
        $initialStock = 1400;

        $updateValidFrom = ['day' => '01', 'month' => '02', 'year' => '2019'];
        $updateExpectedFrom = $this->processDate('01-02-2019', 'd-m-Y');
        $updateValidTo = ['day' => '02', 'month' => '02', 'year' => '2019'];
        $updateExpectedTo = $this->processDate('02-02-2019', 'd-m-Y');
        $updateInitialStock = 1401;

        $entity = Entity::create($irhpPermitType, $validFrom, $validTo, $initialStock);

        $this->assertEquals($irhpPermitType, $entity->getIrhpPermitType());
        $this->assertEquals($expectedFrom, $entity->getValidFrom());
        $this->assertEquals($expectedTo, $entity->getValidTo());
        $this->assertEquals($initialStock, $entity->getInitialStock());

        $entity->update($irhpPermitType, $updateValidFrom, $updateValidTo, $updateInitialStock);

        $this->assertEquals($updateExpectedFrom, $entity->getValidFrom());
        $this->assertEquals($updateExpectedTo, $entity->getValidTo());
        $this->assertEquals($updateInitialStock, $entity->getInitialStock());
    }
}
