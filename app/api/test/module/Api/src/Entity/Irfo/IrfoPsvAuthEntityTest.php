<?php

namespace Dvsa\OlcsTest\Api\Entity\Irfo;

use Dvsa\OlcsTest\Api\Entity\Abstracts\EntityTester;
use Dvsa\Olcs\Api\Entity\Fee\Fee as FeeEntity;
use Dvsa\Olcs\Api\Entity\Fee\FeeType as FeeTypeEntity;
use Dvsa\Olcs\Api\Entity\Irfo\IrfoPsvAuth as Entity;
use Dvsa\Olcs\Api\Entity\Irfo\IrfoPsvAuthType as IrfoPsvAuthTypeEntity;
use Dvsa\Olcs\Api\Entity\Organisation\Organisation as OrganisationEntity;
use Dvsa\Olcs\Api\Entity\System\RefData;
use Mockery as m;
use Dvsa\Olcs\Api\Domain\Exception\BadRequestException;

/**
 * IrfoPsvAuth Entity Unit Tests
 *
 * Initially auto-generated but won't be overridden
 */
class IrfoPsvAuthEntityTest extends EntityTester
{
    /**
     * Define the entity to test
     *
     * @var string
     */
    protected $entityClass = Entity::class;

    public function setUp()
    {
        /** @var Entity entity */
        $this->entity = $this->instantiate($this->entityClass);
        $this->entity->setId('999');
    }

    public function testConstruct()
    {
        $organisation = m::mock(OrganisationEntity::class);
        $type = m::mock(IrfoPsvAuthTypeEntity::class);
        $status = m::mock(RefData::class);

        $entity = new Entity($organisation, $type, $status);

        $this->assertSame($organisation, $entity->getOrganisation());
        $this->assertSame($type, $entity->getIrfoPsvAuthType());
        $this->assertSame($status, $entity->getStatus());
    }

    public function testUpdate()
    {
        $irfoPsvAuthType = m::mock(IrfoPsvAuthTypeEntity::class)->makePartial();
        $irfoPsvAuthType->setSectionCode('blah');
        $validityPeriod = 2;
        $inForceDate = new \DateTime('2010-02-03');
        $serviceRouteFrom = 'Bristol';
        $serviceRouteTo = 'Leeds';
        $journeyFrequency = m::mock(RefData::class)->makePartial();
        $journeyFrequency->setId('psv_freq_daily');
        $copiesRequired = 3;
        $copiesRequiredTotal = 4;

        $this->entity->update(
            $irfoPsvAuthType,
            $validityPeriod,
            $inForceDate,
            $serviceRouteFrom,
            $serviceRouteTo,
            $journeyFrequency,
            $copiesRequired,
            $copiesRequiredTotal
        );

        $this->assertEquals($irfoPsvAuthType, $this->entity->getIrfoPsvAuthType());
        $this->assertEquals($validityPeriod, $this->entity->getValidityPeriod());
        $this->assertEquals($inForceDate, $this->entity->getInForceDate());
        $this->assertEquals($serviceRouteFrom, $this->entity->getServiceRouteFrom());
        $this->assertEquals($serviceRouteTo, $this->entity->getServiceRouteTo());
        $this->assertEquals($journeyFrequency, $this->entity->getJourneyFrequency());
        $this->assertEquals($copiesRequired, $this->entity->getCopiesRequired());
        $this->assertEquals($copiesRequiredTotal, $this->entity->getCopiesRequiredTotal());
        $this->assertEquals('blah/999', $this->entity->getIrfoFileNo());
    }

    public function testPopulateIrfoFeeId()
    {
        $organisation = m::mock(OrganisationEntity::class);
        $organisation->shouldReceive('getId')->once()->andReturn(44);

        $type = m::mock(IrfoPsvAuthTypeEntity::class);
        $status = m::mock(RefData::class);

        $entity = new Entity($organisation, $type, $status);
        $entity->populateIrfoFeeId();

        $this->assertEquals('IR0000044', $entity->getIrfoFeeId());
    }

    public function testIsGrantableWithApplicationFee()
    {
        $status = new RefData();
        $status->setId(Entity::STATUS_PENDING);
        $this->entity->setStatus($status);

        $feeType = new FeeTypeEntity();

        $feeStatusPaid = new RefData();
        $feeStatusPaid->setId(FeeEntity::STATUS_PAID);

        $fee = new FeeEntity($feeType, 10, $feeStatusPaid);

        $this->assertTrue($this->entity->isGrantable($fee));
    }

    public function testIsGrantableWithApplicationFeeNotPaid()
    {
        $status = new RefData();
        $status->setId(Entity::STATUS_PENDING);
        $this->entity->setStatus($status);

        $feeType = new FeeTypeEntity();

        $feeStatusPaid = new RefData();
        $feeStatusPaid->setId(FeeEntity::STATUS_OUTSTANDING);

        $fee = new FeeEntity($feeType, 10, $feeStatusPaid);

        $this->assertFalse($this->entity->isGrantable($fee));
    }

    public function testIsGrantableWithApplicationFeePaidInvalidState()
    {
        $status = new RefData();
        $status->setId(Entity::STATUS_GRANTED);
        $this->entity->setStatus($status);

        $feeType = new FeeTypeEntity();

        $feeStatusPaid = new RefData();
        $feeStatusPaid->setId(FeeEntity::STATUS_PAID);

        $fee = new FeeEntity($feeType, 10, $feeStatusPaid);

        $this->assertFalse($this->entity->isGrantable($fee));
    }

    public function testGrant()
    {
        $status = new RefData();
        $status->setId(Entity::STATUS_PENDING);
        $this->entity->setStatus($status);

        $feeType = new FeeTypeEntity();
        $feeStatusPaid = new RefData();
        $feeStatusPaid->setId(FeeEntity::STATUS_PAID);
        $fee = new FeeEntity($feeType, 10, $feeStatusPaid);

        $newStatus = new RefData();
        $newStatus->setId(Entity::STATUS_GRANTED);

        $this->entity->grant($newStatus, $fee);

        $this->assertEquals($newStatus, $this->entity->getStatus());
    }

    /**
     * @expectedException Dvsa\Olcs\Api\Domain\Exception\BadRequestException
     */
    public function testGrantFeeNotPaidThrowsException()
    {
        $status = new RefData();
        $status->setId(Entity::STATUS_PENDING);
        $this->entity->setStatus($status);

        $feeType = new FeeTypeEntity();
        $feeStatusPaid = new RefData();
        $feeStatusPaid->setId(FeeEntity::STATUS_OUTSTANDING);
        $fee = new FeeEntity($feeType, 10, $feeStatusPaid);

        $newStatus = new RefData();
        $newStatus->setId(Entity::STATUS_GRANTED);

        $this->entity->grant($newStatus, $fee);
    }

    /**
     * @expectedException Dvsa\Olcs\Api\Domain\Exception\BadRequestException
     */
    public function testGrantInvalidStateThrowsException()
    {
        $status = new RefData();
        $status->setId(Entity::STATUS_GRANTED);
        $this->entity->setStatus($status);

        $feeType = new FeeTypeEntity();
        $feeStatusPaid = new RefData();
        $feeStatusPaid->setId(FeeEntity::STATUS_PAID);
        $fee = new FeeEntity($feeType, 10, $feeStatusPaid);

        $newStatus = new RefData();
        $newStatus->setId(Entity::STATUS_GRANTED);

        $this->entity->grant($newStatus, $fee);

        $this->assertEquals($newStatus, $this->entity->getStatus());
    }

    public function testIsRefusable()
    {
        $status = new RefData();
        $status->setId(Entity::STATUS_PENDING);
        $this->entity->setStatus($status);

        $this->assertTrue($this->entity->isRefusable());
    }

    public function testIsRefusableInvalidState()
    {
        $status = new RefData();
        $status->setId(Entity::STATUS_GRANTED);
        $this->entity->setStatus($status);

        $this->assertFalse($this->entity->isRefusable());
    }

    public function testRefuse()
    {
        $status = new RefData();
        $status->setId(Entity::STATUS_PENDING);
        $this->entity->setStatus($status);

        $newStatus = new RefData();
        $newStatus->setId(Entity::STATUS_REFUSED);

        $this->entity->refuse($newStatus);

        $this->assertEquals($newStatus, $this->entity->getStatus());
    }
}
