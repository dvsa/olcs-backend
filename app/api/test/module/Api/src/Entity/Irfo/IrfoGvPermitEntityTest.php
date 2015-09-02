<?php

namespace Dvsa\OlcsTest\Api\Entity\Irfo;

use Dvsa\OlcsTest\Api\Entity\Abstracts\EntityTester;
use Dvsa\Olcs\Api\Entity\Irfo\IrfoGvPermit as Entity;
use Dvsa\Olcs\Api\Entity\Irfo\IrfoGvPermitType as IrfoGvPermitTypeEntity;
use Dvsa\Olcs\Api\Entity\Organisation\Organisation as OrganisationEntity;
use Dvsa\Olcs\Api\Entity\System\RefData;
use Mockery as m;

/**
 * IrfoGvPermit Entity Unit Tests
 *
 * Initially auto-generated but won't be overridden
 */
class IrfoGvPermitEntityTest extends EntityTester
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
    }

    public function testConstruct()
    {
        $organisation = m::mock(OrganisationEntity::class);
        $type = m::mock(IrfoGvPermitTypeEntity::class);
        $status = m::mock(RefData::class);

        $entity = new Entity($organisation, $type, $status);

        $this->assertSame($organisation, $entity->getOrganisation());
        $this->assertSame($type, $entity->getIrfoGvPermitType());
        $this->assertSame($status, $entity->getIrfoPermitStatus());
    }

    public function testUpdate()
    {
        $irfoGvPermitType = m::mock(IrfoGvPermitTypeEntity::class);
        $yearRequired = 2010;
        $inForceDate = new \DateTime('2010-02-03');
        $expiryDate = new \DateTime('2011-02-03');
        $noOfCopies = 11;
        $isFeeExempt = 'N';
        $exemptionDetails = 'testing';
        $irfoFeeId = 'N00001';

        $this->entity->update(
            $irfoGvPermitType,
            $yearRequired,
            $inForceDate,
            $expiryDate,
            $noOfCopies,
            $isFeeExempt,
            $exemptionDetails,
            $irfoFeeId
        );

        $this->assertEquals($irfoGvPermitType, $this->entity->getIrfoGvPermitType());
        $this->assertEquals($yearRequired, $this->entity->getYearRequired());
        $this->assertEquals($inForceDate, $this->entity->getInForceDate());
        $this->assertEquals($expiryDate, $this->entity->getExpiryDate());
        $this->assertEquals($noOfCopies, $this->entity->getNoOfCopies());
        $this->assertEquals($isFeeExempt, $this->entity->getIsFeeExempt());
        $this->assertEquals($exemptionDetails, $this->entity->getExemptionDetails());
        $this->assertEquals($irfoFeeId, $this->entity->getIrfoFeeId());
    }

    /**
     * Tests update throws exception correctly
     *
     * @expectedException \Dvsa\Olcs\Api\Domain\Exception\ValidationException
     */
    public function testUpdateWithInvalidExpiryDate()
    {
        $irfoGvPermitType = m::mock(IrfoGvPermitTypeEntity::class);
        $yearRequired = 2010;
        $inForceDate = new \DateTime('2010-02-03');
        $expiryDate = new \DateTime('2010-01-05');
        $noOfCopies = 11;

        $this->entity->update(
            $irfoGvPermitType,
            $yearRequired,
            $inForceDate,
            $expiryDate,
            $noOfCopies
        );
    }

    public function testReset()
    {
        $status = new RefData();
        $status->setId(Entity::STATUS_APPROVED);
        $this->entity->setIrfoPermitStatus($status);

        $newStatus = new RefData();
        $newStatus->setId(Entity::STATUS_PENDING);

        $this->entity->reset($newStatus);

        $this->assertEquals($newStatus, $this->entity->getIrfoPermitStatus());
    }

    /**
     * Tests reset throws exception correctly
     *
     * @expectedException \Dvsa\Olcs\Api\Domain\Exception\BadRequestException
     */
    public function testResetThrowsCanMakeDecisionException()
    {
        $status = new RefData();
        $status->setId(Entity::STATUS_APPROVED);

        $this->entity->reset($status);

        return true;
    }
}
