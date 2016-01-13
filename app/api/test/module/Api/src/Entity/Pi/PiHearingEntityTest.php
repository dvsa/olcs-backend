<?php

namespace Dvsa\OlcsTest\Api\Entity\Pi;

use Dvsa\OlcsTest\Api\Entity\Abstracts\EntityTester;
use Dvsa\Olcs\Api\Entity\Pi\PiHearing as Entity;
use Dvsa\Olcs\Api\Entity\Pi\PresidingTc as PresidingTcEntity;
use Dvsa\Olcs\Api\Entity\Pi\Pi as PiEntity;
use Dvsa\Olcs\Api\Entity\System\RefData;
use Mockery as m;

/**
 * PiHearing Entity Unit Tests
 *
 * Initially auto-generated but won't be overridden
 */
class PiHearingEntityTest extends EntityTester
{
    /**
     * Define the entity to test
     *
     * @var string
     */
    protected $entityClass = Entity::class;

    public function setUp()
    {
        /** @var \Dvsa\Olcs\Api\Entity\Pi\PiHearing entity */
        $this->entity = $this->instantiate($this->entityClass);
    }

    /**
     * test create
     */
    public function testCreateNotAdjournedOrCancelled()
    {
        $piEntity = m::mock(PiEntity::class);
        $piEntity->shouldReceive('isClosed')->andReturn(false);
        $presidingTc = m::mock(PresidingTcEntity::class);
        $presidedByRole = m::mock(RefData::class);
        $hearingDate = m::mock(\DateTime::class);
        $piVenue = null;
        $piVenueOther = 'other venue';
        $witnesses = 2;
        $isCancelled = 'N';
        $cancelledReason = 'cancelled reason';
        $isAdjourned = 'N';
        $adjournedReason = 'adjourned reason';
        $details = 'details';
        $adjournedDate = '2015-12-25 12:00:00';
        $cancelledDate = '2015-12-25';

        $hearing = new Entity(
            $piEntity,
            $presidingTc,
            $presidedByRole,
            $hearingDate,
            $piVenue,
            $piVenueOther,
            $witnesses,
            $isCancelled,
            $cancelledDate,
            $cancelledReason,
            $isAdjourned,
            $adjournedDate,
            $adjournedReason,
            $details
        );

        $this->assertEquals($piEntity, $hearing->getPi());
        $this->assertEquals($presidingTc, $hearing->getPresidingTc());
        $this->assertEquals($presidedByRole, $hearing->getPresidedByRole());
        $this->assertEquals($hearingDate, $hearing->getHearingDate());
        $this->assertEquals($piVenue, $hearing->getPiVenue());
        $this->assertEquals($piVenueOther, $hearing->getPiVenueOther());
        $this->assertEquals($witnesses, $hearing->getWitnesses());
        $this->assertEquals($isCancelled, $hearing->getIsCancelled());
        $this->assertEquals(null, $hearing->getCancelledDate());
        $this->assertEquals(null, $hearing->getCancelledReason());
        $this->assertEquals($isAdjourned, $hearing->getIsAdjourned());
        $this->assertEquals(null, $hearing->getAdjournedDate());
        $this->assertEquals(null, $hearing->getAdjournedReason());
        $this->assertEquals($details, $hearing->getDetails());
    }

    /**
     * test update with different adjourned dates to also test date processing
     *
     * @dataProvider adjournedDateProvider
     * @param string|null $adjournedDate
     */
    public function testUpdateWithAdjournedOrCancelled($adjournedDate)
    {
        $piEntity = m::mock(PiEntity::class);
        $piEntity->shouldReceive('isClosed')->andReturn(false);
        $this->entity->setPi($piEntity);
        $presidingTc = m::mock(PresidingTcEntity::class);
        $presidedByRole = m::mock(RefData::class);
        $hearingDate = m::mock(\DateTime::class);
        $piVenue = null;
        $piVenueOther = 'other venue';
        $witnesses = 2;
        $isCancelled = 'Y';
        $cancelledReason = 'cancelled reason';
        $isAdjourned = 'Y';
        $adjournedReason = 'adjourned reason';
        $details = 'details';
        $entityAdjournedDate = \DateTime::createFromFormat('Y-m-d H:i:s', $adjournedDate);
        $cancelledDate = '2015-12-25';
        $entityCancelledDate = \DateTime::createFromFormat('Y-m-d', $cancelledDate)->setTime(0, 0, 0);

        $this->entity->update(
            $presidingTc,
            $presidedByRole,
            $hearingDate,
            $piVenue,
            $piVenueOther,
            $witnesses,
            $isCancelled,
            $cancelledDate,
            $cancelledReason,
            $isAdjourned,
            $adjournedDate,
            $adjournedReason,
            $details
        );

        $this->assertEquals($presidingTc, $this->entity->getPresidingTc());
        $this->assertEquals($presidedByRole, $this->entity->getPresidedByRole());
        $this->assertEquals($hearingDate, $this->entity->getHearingDate());
        $this->assertEquals($piVenue, $this->entity->getPiVenue());
        $this->assertEquals($piVenueOther, $this->entity->getPiVenueOther());
        $this->assertEquals($witnesses, $this->entity->getWitnesses());
        $this->assertEquals($isCancelled, $this->entity->getIsCancelled());
        $this->assertEquals($entityCancelledDate, $this->entity->getCancelledDate());
        $this->assertEquals($cancelledReason, $this->entity->getCancelledReason());
        $this->assertEquals($isAdjourned, $this->entity->getIsAdjourned());
        $this->assertEquals($entityAdjournedDate, $this->entity->getAdjournedDate());
        $this->assertEquals($adjournedReason, $this->entity->getAdjournedReason());
        $this->assertEquals($details, $this->entity->getDetails());
    }

    /**
     * @expectedException \Dvsa\Olcs\Api\Domain\Exception\ForbiddenException
     */
    public function testCreateExceptionWhenPiClosed()
    {
        $piEntity = m::mock(PiEntity::class);
        $piEntity->shouldReceive('isClosed')->andReturn(true);
        $presidingTc = m::mock(PresidingTcEntity::class);
        $presidedByRole = m::mock(RefData::class);
        $hearingDate = m::mock(\DateTime::class);

        $hearing = new Entity(
            $piEntity,
            $presidingTc,
            $presidedByRole,
            $hearingDate,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null
        );
    }

    /**
     * @expectedException \Dvsa\Olcs\Api\Domain\Exception\ForbiddenException
     */
    public function testUpdateExceptionWhenPiClosed()
    {
        $piEntity = m::mock(PiEntity::class);
        $piEntity->shouldReceive('isClosed')->andReturn(true);
        $this->entity->setPi($piEntity);
        $presidingTc = m::mock(PresidingTcEntity::class);
        $presidedByRole = m::mock(RefData::class);
        $hearingDate = m::mock(\DateTime::class);

        $this->entity->update(
            $presidingTc,
            $presidedByRole,
            $hearingDate,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null
        );
    }

    /**
     * @return array
     */
    public function adjournedDateProvider()
    {
        return [
            [null],
            ['2015-12-25 12:00:00']
        ];
    }
}
