<?php

namespace Dvsa\OlcsTest\Api\Entity\Permits;

use Dvsa\OlcsTest\Api\Entity\Abstracts\EntityTester;
use Dvsa\Olcs\Api\Domain\Exception\ForbiddenException;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermit as Entity;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitApplication;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitRange;
use Dvsa\Olcs\Api\Entity\Permits\IrhpCandidatePermit;
use Dvsa\Olcs\Api\Entity\System\RefData;
use DateTime;
use Mockery as m;

/**
 * IrhpPermit Entity Unit Tests
 *
 * Initially auto-generated but won't be overridden
 */
class IrhpPermitEntityTest extends EntityTester
{
    /**
     * Define the entity to test
     *
     * @var string
     */
    protected $entityClass = Entity::class;

    /**
     * @var Entity
     */
    protected $sut;

    /**
     * @var DateTime
     */
    protected $issueDate;

    /**
     * @var RefData
     */
    protected $status;

    /**
     * @var int
     */
    protected $permitNumber;

    /**
     * @var IrhpPermitApplication
     */
    protected $irhpPermitApplication;

    /**
     * @var IrhpPermitRange
     */
    protected $irhpPermitRange;

    /**
     * @var IrhpCandidatePermit
     */
    protected $irhpCandidatePermit;

    public function setUp()
    {
        $this->issueDate = m::mock(DateTime::class);
        $this->status = new RefData();
        $this->permitNumber = 431;

        $this->irhpPermitApplication = m::mock(IrhpPermitApplication::class);
        $this->irhpPermitRange = m::mock(IrhpPermitRange::class);

        $this->irhpCandidatePermit = m::mock(IrhpCandidatePermit::class);
        $this->irhpCandidatePermit->shouldReceive('getIrhpPermitApplication')
            ->andReturn($this->irhpPermitApplication);
        $this->irhpCandidatePermit->shouldReceive('getIrhpPermitRange')
            ->andReturn($this->irhpPermitRange);

        $this->sut = Entity::createNew(
            $this->irhpCandidatePermit,
            $this->issueDate,
            $this->status,
            $this->permitNumber
        );
    }

    public function testCreateNew()
    {
        $this->assertSame($this->irhpCandidatePermit, $this->sut->getIrhpCandidatePermit());
        $this->assertSame($this->irhpPermitApplication, $this->sut->getIrhpPermitApplication());
        $this->assertSame($this->irhpPermitRange, $this->sut->getIrhpPermitRange());
        $this->assertSame($this->issueDate, $this->sut->getIssueDate());
        $this->assertSame($this->permitNumber, $this->sut->getPermitNumber());
        $this->assertSame($this->status, $this->sut->getStatus());
    }

    public function testGetPermitNumberWithPrefix()
    {
        $prefix = 'UK';

        $this->irhpPermitRange->shouldReceive('getPrefix')
            ->andReturn($prefix);

        $this->assertSame('UK00431', $this->sut->getPermitNumberWithPrefix());
    }

    public function testGetCalculatedBundleValues()
    {
        $prefix = 'UK';
        
        $this->irhpPermitRange->shouldReceive('getPrefix')
            ->andReturn($prefix);

        $this->assertEquals(
            [
                'permitNumberWithPrefix' => 'UK00431',
            ],
            $this->sut->getCalculatedBundleValues()
        );
    }

    /**
    * @dataProvider dpProceedToAwaitingPrinting
    */
    public function testProceedToAwaitingPrinting($statusId, $expected)
    {
        $this->sut->getStatus()->setId($statusId);

        if (!$expected) {
            $this->expectException(ForbiddenException::class);
        }

        $this->sut->proceedToStatus(new RefData(Entity::STATUS_AWAITING_PRINTING));

        $this->assertEquals(Entity::STATUS_AWAITING_PRINTING, $this->sut->getStatus()->getId());
    }

    public function dpProceedToAwaitingPrinting()
    {
        return [
            [Entity::STATUS_PENDING, true],
            [Entity::STATUS_AWAITING_PRINTING, false],
            [Entity::STATUS_PRINTING, false],
            [Entity::STATUS_PRINTED, false],
            [Entity::STATUS_ERROR, true],
        ];
    }

    /**
    * @dataProvider dpProceedToPrinting
    */
    public function testProceedToPrinting($statusId, $expected)
    {
        $this->sut->getStatus()->setId($statusId);

        if (!$expected) {
            $this->expectException(ForbiddenException::class);
        }

        $this->sut->proceedToStatus(new RefData(Entity::STATUS_PRINTING));

        $this->assertEquals(Entity::STATUS_PRINTING, $this->sut->getStatus()->getId());
    }

    public function dpProceedToPrinting()
    {
        return [
            [Entity::STATUS_PENDING, false],
            [Entity::STATUS_AWAITING_PRINTING, true],
            [Entity::STATUS_PRINTING, false],
            [Entity::STATUS_PRINTED, false],
            [Entity::STATUS_ERROR, false],
        ];
    }

    /**
    * @dataProvider dpProceedToError
    */
    public function testProceedToError($statusId, $expected)
    {
        $this->sut->getStatus()->setId($statusId);

        if (!$expected) {
            $this->expectException(ForbiddenException::class);
        }

        $this->sut->proceedToStatus(new RefData(Entity::STATUS_ERROR));

        $this->assertEquals(Entity::STATUS_ERROR, $this->sut->getStatus()->getId());
    }

    public function dpProceedToError()
    {
        return [
            [Entity::STATUS_PENDING, false],
            [Entity::STATUS_AWAITING_PRINTING, false],
            [Entity::STATUS_PRINTING, true],
            [Entity::STATUS_PRINTED, false],
            [Entity::STATUS_ERROR, false],
        ];
    }

    public function testProceedToUnknown()
    {
        $this->expectException(ForbiddenException::class);

        $this->sut->proceedToStatus(new RefData());
    }

    /**
    * @dataProvider dpIsPending
    */
    public function testIsPending($statusId, $expected)
    {
        $this->sut->getStatus()->setId($statusId);

        $this->assertEquals($expected, $this->sut->isPending());
    }

    public function dpIsPending()
    {
        return [
            [Entity::STATUS_PENDING, true],
            [Entity::STATUS_AWAITING_PRINTING, false],
            [Entity::STATUS_PRINTING, false],
            [Entity::STATUS_PRINTED, false],
            [Entity::STATUS_ERROR, false],
        ];
    }

    /**
    * @dataProvider dpIsAwaitingPrinting
    */
    public function testIsAwaitingPrinting($statusId, $expected)
    {
        $this->sut->getStatus()->setId($statusId);

        $this->assertEquals($expected, $this->sut->isAwaitingPrinting());
    }

    public function dpIsAwaitingPrinting()
    {
        return [
            [Entity::STATUS_PENDING, false],
            [Entity::STATUS_AWAITING_PRINTING, true],
            [Entity::STATUS_PRINTING, false],
            [Entity::STATUS_PRINTED, false],
            [Entity::STATUS_ERROR, false],
        ];
    }

    /**
    * @dataProvider dpIsPrinting
    */
    public function testIsPrinting($statusId, $expected)
    {
        $this->sut->getStatus()->setId($statusId);

        $this->assertEquals($expected, $this->sut->isPrinting());
    }

    public function dpIsPrinting()
    {
        return [
            [Entity::STATUS_PENDING, false],
            [Entity::STATUS_AWAITING_PRINTING, false],
            [Entity::STATUS_PRINTING, true],
            [Entity::STATUS_PRINTED, false],
            [Entity::STATUS_ERROR, false],
        ];
    }

    /**
    * @dataProvider dpHasError
    */
    public function testHasError($statusId, $expected)
    {
        $this->sut->getStatus()->setId($statusId);

        $this->assertEquals($expected, $this->sut->hasError());
    }

    public function dpHasError()
    {
        return [
            [Entity::STATUS_PENDING, false],
            [Entity::STATUS_AWAITING_PRINTING, false],
            [Entity::STATUS_PRINTING, false],
            [Entity::STATUS_PRINTED, false],
            [Entity::STATUS_ERROR, true],
        ];
    }
}
