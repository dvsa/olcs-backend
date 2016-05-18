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

    /**
     * @dataProvider isRefusableStates
     * @param $input
     * @param $expected
     */
    public function testIsRefusable($input, $expected)
    {
        $status = new RefData();
        $status->setId($input);
        $this->entity->setStatus($status);

        $this->assertEquals($expected, $this->entity->isRefusable());
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

    /**
     * @expectedException Dvsa\Olcs\Api\Domain\Exception\BadRequestException
     */
    public function testRefuseThrowsException()
    {
        $status = new RefData();
        $status->setId(Entity::STATUS_REFUSED);
        $this->entity->setStatus($status);

        $newStatus = new RefData();
        $newStatus->setId(Entity::STATUS_REFUSED);

        $this->entity->refuse($newStatus);
    }

    /**
     * @dataProvider isWithdrawableStates
     */
    public function testIsWithdrawable($input, $expected)
    {
        $status = new RefData();
        $status->setId($input);
        $this->entity->setStatus($status);

        $this->assertEquals($expected, $this->entity->isWithdrawable());
    }

    public function testWithdraw()
    {
        $status = new RefData();
        $status->setId(Entity::STATUS_PENDING);
        $this->entity->setStatus($status);

        $newStatus = new RefData();
        $newStatus->setId(Entity::STATUS_WITHDRAWN);

        $this->entity->withdraw($newStatus);

        $this->assertEquals($newStatus, $this->entity->getStatus());
    }

    /**
     * @expectedException \Dvsa\Olcs\Api\Domain\Exception\BadRequestException
     */
    public function testWithdrawThrowsException()
    {
        $status = new RefData();
        $status->setId(Entity::STATUS_WITHDRAWN);
        $this->entity->setStatus($status);

        $newStatus = new RefData();
        $newStatus->setId(Entity::STATUS_WITHDRAWN);

        $this->entity->withdraw($newStatus);
    }


    public function isWithdrawableStates()
    {
        return [
            [Entity::STATUS_PENDING, true],
            [Entity::STATUS_CNS, true],
            [Entity::STATUS_RENEW, true],
            [Entity::STATUS_APPROVED, true],
            [Entity::STATUS_WITHDRAWN, false],
            [Entity::STATUS_GRANTED, false],
            [Entity::STATUS_REFUSED, false]
        ];
    }

    public function isRefusableStates()
    {
        return [
            [Entity::STATUS_PENDING, true],
            [Entity::STATUS_CNS, false],
            [Entity::STATUS_RENEW, true],
            [Entity::STATUS_APPROVED, false],
            [Entity::STATUS_WITHDRAWN, false],
            [Entity::STATUS_GRANTED, false],
            [Entity::STATUS_REFUSED, false]
        ];
    }

    /**
     * @dataProvider isCnsableStates
     */
    public function testIsCnsable($input, $expected)
    {
        $status = new RefData();
        $status->setId($input);
        $this->entity->setStatus($status);

        $this->assertEquals($expected, $this->entity->isCnsable());
    }

    public function isCnsableStates()
    {
        return [
            [Entity::STATUS_PENDING, false],
            [Entity::STATUS_CNS, false],
            [Entity::STATUS_RENEW, true],
            [Entity::STATUS_APPROVED, false],
            [Entity::STATUS_WITHDRAWN, false],
            [Entity::STATUS_GRANTED, false],
            [Entity::STATUS_REFUSED, false]
        ];
    }

    public function testContinuationNotSought()
    {
        $status = new RefData();
        $status->setId(Entity::STATUS_RENEW);
        $this->entity->setStatus($status);

        $newStatus = new RefData();
        $newStatus->setId(Entity::STATUS_CNS);

        $this->entity->continuationNotSought($newStatus);

        $this->assertEquals($newStatus, $this->entity->getStatus());
    }

    /**
     * @expectedException \Dvsa\Olcs\Api\Domain\Exception\BadRequestException
     */
    public function testContinuationNotSoughtThrowsException()
    {
        $status = new RefData();
        $status->setId(Entity::STATUS_PENDING);
        $this->entity->setStatus($status);

        $newStatus = new RefData();
        $newStatus->setId(Entity::STATUS_CNS);

        $this->entity->continuationNotSought($newStatus);
    }

    /**
     * @dataProvider isApprovableStates
     */
    public function testIsApprovable($statusId, $outstandingFees, $expected)
    {
        $status = new RefData();
        $status->setId($statusId);
        $this->entity->setStatus($status);

        $this->assertEquals($expected, $this->entity->isApprovable($outstandingFees));
    }

    public function isApprovableStates()
    {
        return [
            [Entity::STATUS_PENDING, [], false],
            [Entity::STATUS_CNS, [], false],
            [Entity::STATUS_RENEW, [], false],
            [Entity::STATUS_APPROVED, [], false],
            [Entity::STATUS_WITHDRAWN, [], false],
            [Entity::STATUS_GRANTED, [], true],
            [Entity::STATUS_GRANTED, ['FEE'], false],
            [Entity::STATUS_REFUSED, [], false]
        ];
    }

    public function testApprove()
    {
        $status = new RefData();
        $status->setId(Entity::STATUS_GRANTED);
        $this->entity->setStatus($status);

        $newStatus = new RefData();
        $newStatus->setId(Entity::STATUS_APPROVED);

        $this->assertNull($this->entity->getRenewalDate());

        $this->entity->approve($newStatus, []);

        $this->assertEquals($newStatus, $this->entity->getStatus());
        $this->assertInstanceOf(\DateTime::class, $this->entity->getRenewalDate());
    }

    /**
     * @expectedException \Dvsa\Olcs\Api\Domain\Exception\BadRequestException
     */
    public function testApproveThrowsException()
    {
        $status = new RefData();
        $status->setId(Entity::STATUS_GRANTED);
        $this->entity->setStatus($status);

        $newStatus = new RefData();
        $newStatus->setId(Entity::STATUS_APPROVED);

        $this->entity->approve($newStatus, ['FEE']);
    }

    /**
     * @dataProvider isRenewableStates
     */
    public function testIsRenewable($input, $expected)
    {
        $status = new RefData();
        $status->setId($input);
        $this->entity->setStatus($status);

        $this->assertEquals($expected, $this->entity->isRenewable());
    }

    public function isRenewableStates()
    {
        return [
            [Entity::STATUS_PENDING, true],
            [Entity::STATUS_CNS, false],
            [Entity::STATUS_RENEW, true],
            [Entity::STATUS_APPROVED, true],
            [Entity::STATUS_WITHDRAWN, false],
            [Entity::STATUS_GRANTED, true],
            [Entity::STATUS_REFUSED, false]
        ];
    }

    public function testRenew()
    {
        $status = new RefData();
        $status->setId(Entity::STATUS_PENDING);
        $this->entity->setStatus($status);

        $newStatus = new RefData();
        $newStatus->setId(Entity::STATUS_RENEW);

        $this->entity->renew($newStatus);

        $this->assertEquals($newStatus, $this->entity->getStatus());
    }

    /**
     * @expectedException \Dvsa\Olcs\Api\Domain\Exception\BadRequestException
     */
    public function testRenewThrowsException()
    {
        $status = new RefData();
        $status->setId(Entity::STATUS_WITHDRAWN);
        $this->entity->setStatus($status);

        $newStatus = new RefData();
        $newStatus->setId(Entity::STATUS_RENEW);

        $this->entity->renew($newStatus);
    }

    /**
     * @dataProvider isGeneratableDataProvider
     */
    public function testIsGeneratable($statusId, $outstandingFees, $expected)
    {
        $status = new RefData();
        $status->setId($statusId);
        $this->entity->setStatus($status);

        $this->assertEquals($expected, $this->entity->isGeneratable($outstandingFees));
    }

    public function isGeneratableDataProvider()
    {
        return [
            [Entity::STATUS_PENDING, [], false],
            [Entity::STATUS_CNS, [], false],
            [Entity::STATUS_RENEW, [], false],
            [Entity::STATUS_APPROVED, [], true],
            [Entity::STATUS_WITHDRAWN, [], false],
            [Entity::STATUS_GRANTED, [], false],
            [Entity::STATUS_GRANTED, ['FEE'], false],
            [Entity::STATUS_REFUSED, [], false]
        ];
    }

    public function testGenerate()
    {
        $status = new RefData();
        $status->setId(Entity::STATUS_APPROVED);
        $this->entity->setStatus($status);

        $this->entity->setCopiesRequired(2);
        $this->entity->setCopiesRequiredTotal(5);

        $this->entity->setCopiesIssued(20);
        $this->entity->setCopiesIssuedTotal(50);

        $this->entity->generate([]);

        $this->assertEquals(0, $this->entity->getCopiesRequired());
        $this->assertEquals(0, $this->entity->getCopiesRequiredTotal());
        $this->assertEquals(22, $this->entity->getCopiesIssued());
        $this->assertEquals(55, $this->entity->getCopiesIssuedTotal());
    }

    /**
     * @expectedException \Dvsa\Olcs\Api\Domain\Exception\BadRequestException
     */
    public function testGenerateThrowsException()
    {
        $status = new RefData();
        $status->setId(Entity::STATUS_GRANTED);
        $this->entity->setStatus($status);

        $this->entity->generate(['FEE']);
    }
}
