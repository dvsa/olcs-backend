<?php

namespace Dvsa\OlcsTest\Api\Entity\Permits;

use Dvsa\OlcsTest\Api\Entity\Abstracts\EntityTester;
use Dvsa\Olcs\Api\Domain\Exception\ForbiddenException;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermit as Entity;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitApplication;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitRange;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitStock;
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

    public function testCreateForIrhpApplication()
    {
        $irhpPermitApplication = m::mock(IrhpPermitApplication::class);
        $irhpPermitRange = m::mock(IrhpPermitRange::class);
        $issueDate = m::mock(DateTime::class);
        $status = m::mock(RefData::class);
        $permitNumber = 473;

        $entity = Entity::createForIrhpApplication(
            $irhpPermitApplication,
            $irhpPermitRange,
            $issueDate,
            $status,
            $permitNumber,
            null
        );

        $this->assertSame($irhpPermitApplication, $entity->getIrhpPermitApplication());
        $this->assertSame($irhpPermitRange, $entity->getIrhpPermitRange());
        $this->assertSame($issueDate, $entity->getIssueDate());
        $this->assertNull($entity->getExpiryDate());
        $this->assertSame($status, $entity->getStatus());
        $this->assertEquals($permitNumber, $entity->getPermitNumber());
    }

    public function testCreateForIrhpApplicationWithExpiry()
    {
        $irhpPermitApplication = m::mock(IrhpPermitApplication::class);
        $irhpPermitRange = m::mock(IrhpPermitRange::class);
        $issueDate = m::mock(DateTime::class);
        $status = m::mock(RefData::class);
        $permitNumber = 473;
        $expiryDate = new DateTime();

        $entity = Entity::createForIrhpApplication(
            $irhpPermitApplication,
            $irhpPermitRange,
            $issueDate,
            $status,
            $permitNumber,
            $expiryDate
        );

        $this->assertSame($irhpPermitApplication, $entity->getIrhpPermitApplication());
        $this->assertSame($irhpPermitRange, $entity->getIrhpPermitRange());
        $this->assertSame($issueDate, $entity->getIssueDate());
        $this->assertSame($expiryDate, $entity->getExpiryDate());
        $this->assertSame($status, $entity->getStatus());
        $this->assertEquals($permitNumber, $entity->getPermitNumber());
    }

    public function testGetPermitNumberWithPrefix()
    {
        $prefix = 'UK';

        $this->irhpPermitRange->shouldReceive('getPrefix')
            ->andReturn($prefix);

        $this->assertSame('UK00431', $this->sut->getPermitNumberWithPrefix());
    }

    /**
    * @dataProvider dpGetStartDate
    */
    public function testGetStartDate($validFrom, $issueDate, $expected)
    {
        $this->sut->setIssueDate($issueDate);

        $irhpPermitStock = m::mock(IrhpPermitStock::class);
        $irhpPermitStock->shouldReceive('getValidFrom')
            ->with(true)
            ->andReturn($validFrom);

        $this->irhpPermitRange->shouldReceive('getIrhpPermitStock')
            ->andReturn($irhpPermitStock);

        $this->assertEquals($expected, $this->sut->getStartDate());
    }

    public function dpGetStartDate()
    {
        $inPast = new DateTime('last year');
        $now = new DateTime();
        $inFuture = new DateTime('next year');

        return [
            'issued before valid from date' => [
                'validFrom' => $inFuture,
                'issueDate' => $now,
                'expected' => $inFuture,
            ],
            'issued after valid from date' => [
                'validFrom' => $inPast,
                'issueDate' => $now,
                'expected' => $now,
            ],
            'not yet issued' => [
                'validFrom' => $inPast,
                'issueDate' => null,
                'expected' => $inPast,
            ],
        ];
    }

    public function testGetCalculatedBundleValues()
    {
        $prefix = 'UK';

        $now = new DateTime();
        $this->sut->setIssueDate($now);

        $irhpPermitStock = m::mock(IrhpPermitStock::class);
        $irhpPermitStock->shouldReceive('getValidFrom')
            ->with(true)
            ->andReturn(new DateTime('last year'));

        $this->irhpPermitRange->shouldReceive('getPrefix')
            ->andReturn($prefix)
            ->shouldReceive('getIrhpPermitStock')
            ->andReturn($irhpPermitStock);

        $this->assertEquals(
            [
                'permitNumberWithPrefix' => 'UK00431',
                'startDate' => $now,
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
            [Entity::STATUS_CEASED, false],
            [Entity::STATUS_TERMINATED, false],
            [Entity::STATUS_EXPIRED, false]
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
            [Entity::STATUS_CEASED, false],
            [Entity::STATUS_TERMINATED, false],
            [Entity::STATUS_EXPIRED, false]
        ];
    }

    /**
    * @dataProvider dpProceedToPrinted
    */
    public function testProceedToPrinted($statusId, $expected)
    {
        $this->sut->getStatus()->setId($statusId);

        if (!$expected) {
            $this->expectException(ForbiddenException::class);
        }

        $this->sut->proceedToStatus(new RefData(Entity::STATUS_PRINTED));

        $this->assertEquals(Entity::STATUS_PRINTED, $this->sut->getStatus()->getId());
    }

    public function dpProceedToPrinted()
    {
        return [
            [Entity::STATUS_PENDING, false],
            [Entity::STATUS_AWAITING_PRINTING, false],
            [Entity::STATUS_PRINTING, true],
            [Entity::STATUS_PRINTED, false],
            [Entity::STATUS_ERROR, false],
            [Entity::STATUS_CEASED, false],
            [Entity::STATUS_TERMINATED, false],
            [Entity::STATUS_EXPIRED, false]
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
            [Entity::STATUS_PENDING, true],
            [Entity::STATUS_AWAITING_PRINTING, true],
            [Entity::STATUS_PRINTING, true],
            [Entity::STATUS_PRINTED, false],
            [Entity::STATUS_ERROR, true],
            [Entity::STATUS_CEASED, false],
            [Entity::STATUS_TERMINATED, false],
            [Entity::STATUS_EXPIRED, false]
        ];
    }

    /**
    * @dataProvider dpProceedToTerminated
    */
    public function testProceedToTerminated($statusId, $expected)
    {
        $this->sut->getStatus()->setId($statusId);

        if (!$expected) {
            $this->expectException(ForbiddenException::class);
        }

        $this->sut->proceedToStatus(new RefData(Entity::STATUS_TERMINATED));

        $this->assertEquals(Entity::STATUS_TERMINATED, $this->sut->getStatus()->getId());
    }

    public function dpProceedToTerminated()
    {
        return [
            [Entity::STATUS_PENDING, true],
            [Entity::STATUS_AWAITING_PRINTING, true],
            [Entity::STATUS_PRINTING, true],
            [Entity::STATUS_PRINTED, true],
            [Entity::STATUS_ERROR, true],
            [Entity::STATUS_CEASED, false],
            [Entity::STATUS_TERMINATED, false],
            [Entity::STATUS_EXPIRED, false]
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
            [Entity::STATUS_CEASED, false],
            [Entity::STATUS_TERMINATED, false],
            [Entity::STATUS_EXPIRED, false]
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
            [Entity::STATUS_CEASED, false],
            [Entity::STATUS_TERMINATED, false],
            [Entity::STATUS_EXPIRED, false]
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
            [Entity::STATUS_CEASED, false],
            [Entity::STATUS_TERMINATED, false],
            [Entity::STATUS_EXPIRED, false]
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
            [Entity::STATUS_CEASED, false],
            [Entity::STATUS_TERMINATED, false],
            [Entity::STATUS_EXPIRED, false]
        ];
    }

    public function dpCease()
    {
        return [
            [Entity::STATUS_PENDING, false],
            [Entity::STATUS_AWAITING_PRINTING, false],
            [Entity::STATUS_PRINTING, false],
            [Entity::STATUS_PRINTED, false],
            [Entity::STATUS_ERROR, false],
            [Entity::STATUS_CEASED, true],
            [Entity::STATUS_TERMINATED, false],
            [Entity::STATUS_EXPIRED, false]
        ];
    }

    /**
     * @dataProvider dpCease
     */
    public function testCease($statusId, $expected)
    {
        $this->sut->getStatus()->setId($statusId);

        if (!$expected) {
            $this->expectException(ForbiddenException::class);
        }

        $this->sut->cease(new RefData($statusId));

        $this->assertEquals(Entity::STATUS_CEASED, $this->sut->getStatus()->getId());
    }

    /**
     * @dataProvider dpIsCeased
     */
    public function testIsCeased($statusId, $expected)
    {
        $this->sut->getStatus()->setId($statusId);

        $this->assertEquals($expected, $this->sut->isCeased());
    }

    public function dpIsCeased()
    {
        return [
            [Entity::STATUS_PENDING, false],
            [Entity::STATUS_AWAITING_PRINTING, false],
            [Entity::STATUS_PRINTING, false],
            [Entity::STATUS_PRINTED, false],
            [Entity::STATUS_ERROR, false],
            [Entity::STATUS_CEASED, true],
            [Entity::STATUS_TERMINATED, false],
            [Entity::STATUS_EXPIRED, false]
        ];
    }

    /**
     * @dataProvider dpIsTerminated
     */
    public function testIsTerminated($statusId, $expected)
    {
        $this->sut->getStatus()->setId($statusId);

        $this->assertEquals($expected, $this->sut->isTerminated());
    }

    public function dpIsTerminated()
    {
        return [
            [Entity::STATUS_PENDING, false],
            [Entity::STATUS_AWAITING_PRINTING, false],
            [Entity::STATUS_PRINTING, false],
            [Entity::STATUS_PRINTED, false],
            [Entity::STATUS_ERROR, false],
            [Entity::STATUS_CEASED, false],
            [Entity::STATUS_TERMINATED, true],
            [Entity::STATUS_EXPIRED, false]
        ];
    }

    /**
     * @dataProvider dpIsValid
     */
    public function testIsValid($statusId, $expected)
    {
        $this->sut->getStatus()->setId($statusId);

        $this->assertEquals($expected, $this->sut->isValid());
    }

    public function dpIsValid()
    {
        return [
            [Entity::STATUS_PENDING, true],
            [Entity::STATUS_AWAITING_PRINTING, true],
            [Entity::STATUS_PRINTING, true],
            [Entity::STATUS_PRINTED, true],
            [Entity::STATUS_ERROR, true],
            [Entity::STATUS_CEASED, false],
            [Entity::STATUS_TERMINATED, false],
            [Entity::STATUS_EXPIRED, false]
        ];
    }

    /**
     * @dataProvider dpIsExpired
     */
    public function testIsExpired($statusId, $expected)
    {
        $this->sut->getStatus()->setId($statusId);
        $this->assertEquals($expected, $this->sut->isExpired());
    }

    public function dpIsExpired()
    {
        return [
            [Entity::STATUS_PENDING, false],
            [Entity::STATUS_AWAITING_PRINTING, false],
            [Entity::STATUS_PRINTING, false],
            [Entity::STATUS_PRINTED, false],
            [Entity::STATUS_ERROR, false],
            [Entity::STATUS_CEASED, false],
            [Entity::STATUS_TERMINATED, false],
            [Entity::STATUS_EXPIRED, true]
        ];
    }
}
