<?php

namespace Dvsa\OlcsTest\Api\Entity\Permits;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Dvsa\Olcs\Api\Domain\Exception\ForbiddenException;
use Dvsa\Olcs\Api\Domain\Exception\RuntimeException;
use Dvsa\Olcs\Api\Domain\Exception\ValidationException;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitStock;
use Dvsa\Olcs\Api\Entity\WithdrawableInterface;
use Dvsa\OlcsTest\Api\Entity\Abstracts\EntityTester;
use Dvsa\Olcs\Api\Domain\Command\Email\SendEcmtShortTermSuccessful;
use Dvsa\Olcs\Api\Domain\Command\Email\SendEcmtShortTermUnsuccessful;
use Dvsa\Olcs\Api\Domain\Command\Email\SendEcmtShortTermApsgPartSuccessful;
use Dvsa\Olcs\Api\Domain\Command\Email\SendEcmtShortTermAppSubmitted;
use Dvsa\Olcs\Api\Entity\Fee\Fee;
use Dvsa\Olcs\Api\Entity\Fee\FeeType;
use Dvsa\Olcs\Api\Entity\Generic\Answer;
use Dvsa\Olcs\Api\Entity\Generic\ApplicationPath;
use Dvsa\Olcs\Api\Entity\Generic\ApplicationStep;
use Dvsa\Olcs\Api\Entity\Generic\Question;
use Dvsa\Olcs\Api\Entity\Generic\QuestionText;
use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Dvsa\Olcs\Api\Entity\Task\Task;
use Dvsa\Olcs\Api\Entity\Organisation\Organisation;
use Dvsa\Olcs\Api\Entity\Permits\IrhpApplication as Entity;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitApplication;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitType;
use Dvsa\Olcs\Api\Entity\Permits\Sectors;
use Dvsa\Olcs\Api\Entity\Permits\Traits\ApplicationAcceptConsts;
use Dvsa\Olcs\Api\Entity\SectionableInterface;
use Dvsa\Olcs\Api\Entity\IrhpInterface;
use Dvsa\Olcs\Api\Entity\System\RefData;
use Mockery as m;

/**
 * IrhpApplication Entity Unit Tests
 *
 * Initially auto-generated but won't be overridden
 */
class IrhpApplicationEntityTest extends EntityTester
{
    /**
     * Define the entity to test
     *
     * @var string
     */
    protected $entityClass = Entity::class;

    /**
     * @var Entity|m\MockInterface
     */
    protected $sut;

    public function setUp()
    {
        $this->sut = m::mock(Entity::class)->makePartial()->shouldAllowMockingProtectedMethods();
        $this->sut->initCollections();

        parent::setUp();
    }

    public function testGetCalculatedBundleValues()
    {
        $businessProcess = m::mock(RefData::class);

        $this->sut->shouldReceive('getApplicationRef')
            ->once()
            ->withNoArgs()
            ->andReturn('appRef')
            ->shouldReceive('canBeCancelled')
            ->once()
            ->withNoArgs()
            ->andReturn(false)
            ->shouldReceive('canBeWithdrawn')
            ->once()
            ->withNoArgs()
            ->andReturn(false)
            ->shouldReceive('canBeDeclined')
            ->once()
            ->withNoArgs()
            ->andReturn(false)
            ->shouldReceive('canBeSubmitted')
            ->once()
            ->withNoArgs()
            ->andReturn(false)
            ->shouldReceive('canBeRevivedFromWithdrawn')
            ->once()
            ->withNoArgs()
            ->andReturn(false)
            ->shouldReceive('canBeRevivedFromUnsuccessful')
            ->once()
            ->withNoArgs()
            ->andReturn(false)
            ->shouldReceive('hasOutstandingFees')
            ->once()
            ->withNoArgs()
            ->andReturn(false)
            ->shouldReceive('getOutstandingFeeAmount')
            ->once()
            ->withNoArgs()
            ->andReturn(0)
            ->shouldReceive('getSectionCompletion')
            ->once()
            ->withNoArgs()
            ->andReturn([])
            ->shouldReceive('hasCheckedAnswers')
            ->once()
            ->withNoArgs()
            ->andReturn(false)
            ->shouldReceive('hasMadeDeclaration')
            ->once()
            ->withNoArgs()
            ->andReturn(false)
            ->shouldReceive('isNotYetSubmitted')
            ->once()
            ->withNoArgs()
            ->andReturn(true)
            ->shouldReceive('isSubmittedForConsideration')
            ->once()
            ->withNoArgs()
            ->andReturn(false)
            ->shouldReceive('isValid')
            ->andReturn(false)
            ->shouldReceive('isFeePaid')
            ->andReturn(false)
            ->shouldReceive('isIssueInProgress')
            ->andReturn(false)
            ->shouldReceive('isAwaitingFee')
            ->andReturn(false)
            ->shouldReceive('isUnderConsideration')
            ->andReturn(false)
            ->shouldReceive('isCancelled')
            ->andReturn(false)
            ->shouldReceive('isWithdrawn')
            ->once()
            ->withNoArgs()
            ->andReturn(false)
            ->shouldReceive('isDeclined')
            ->once()
            ->withNoArgs()
            ->andReturn(false)
            ->shouldReceive('isReadyForNoOfPermits')
            ->once()
            ->withNoArgs()
            ->andReturn(false)
            ->shouldReceive('isBilateral')
            ->once()
            ->withNoArgs()
            ->andReturn(false)
            ->shouldReceive('isMultilateral')
            ->once()
            ->withNoArgs()
            ->andReturn(false)
            ->shouldReceive('canCheckAnswers')
            ->once()
            ->withNoArgs()
            ->andReturn(true)
            ->shouldReceive('canMakeDeclaration')
            ->once()
            ->withNoArgs()
            ->andReturn(true)
            ->shouldReceive('getPermitsRequired')
            ->once()
            ->withNoArgs()
            ->andReturn(0)
            ->shouldReceive('canUpdateCountries')
            ->once()
            ->withNoArgs()
            ->andReturn(true)
            ->shouldReceive('getQuestionAnswerData')
            ->once()
            ->withNoArgs()
            ->andReturn([])
            ->shouldReceive('getBusinessProcess')
            ->once()
            ->withNoArgs()
            ->andReturn($businessProcess)
            ->shouldReceive('requiresPreAllocationCheck')
            ->once()
            ->withNoArgs()
            ->andReturn(true);

        $this->assertSame(
            [
                'applicationRef' => 'appRef',
                'canBeCancelled' => false,
                'canBeWithdrawn' => false,
                'canBeGranted' => false,
                'canBeDeclined' => false,
                'canBeSubmitted' => false,
                'canBeRevivedFromWithdrawn' => false,
                'canBeRevivedFromUnsuccessful' => false,
                'hasOutstandingFees' => false,
                'outstandingFeeAmount' => 0,
                'sectionCompletion' => [],
                'hasCheckedAnswers' => false,
                'hasMadeDeclaration' => false,
                'isNotYetSubmitted' => true,
                'isSubmittedForConsideration' => false,
                'isValid' => false,
                'isFeePaid' => false,
                'isIssueInProgress' => false,
                'isAwaitingFee' => false,
                'isUnderConsideration' => false,
                'isReadyForNoOfPermits' => false,
                'isCancelled' => false,
                'isWithdrawn' => false,
                'isDeclined' => false,
                'isBilateral' => false,
                'isMultilateral' => false,
                'canCheckAnswers' => true,
                'canMakeDeclaration' => true,
                'permitsRequired' => 0,
                'canUpdateCountries' => true,
                'questionAnswerData' => [],
                'businessProcess' => $businessProcess,
                'requiresPreAllocationCheck' => true,
            ],
            $this->sut->getCalculatedBundleValues()
        );
    }

    public function testGetApplicationRef()
    {
        $this->sut->setId(987);

        $licence = m::mock(Licence::class);
        $licence->shouldReceive('getLicNo')
            ->andReturn('ABC123');

        $this->sut->setLicence($licence);

        $this->assertSame('ABC123 / 987', $this->sut->getApplicationRef());
    }

    public function testGetRelatedOrganisation()
    {
        $organisation = m::mock(Organisation::class);

        $licence = m::mock(Licence::class);
        $licence->shouldReceive('getOrganisation')
            ->andReturn($organisation);

        $this->sut->setLicence($licence);

        $this->assertSame(
            $organisation,
            $this->sut->getRelatedOrganisation()
        );
    }

    public function testGetRelatedLicence()
    {
        $licence = m::mock(Licence::class);
        $entity = $this->createNewEntity(null, null, null, $licence);
        $this->assertEquals($licence, $entity->getRelatedLicence());
    }

    /**
     * @dataProvider dpTestIsValid
     */
    public function testIsValid($status, $expected)
    {
        $this->sut->setStatus(new RefData($status));
        $this->assertSame($expected, $this->sut->isValid());
    }

    public function dpTestIsValid()
    {
        return [
            [IrhpInterface::STATUS_CANCELLED, false],
            [IrhpInterface::STATUS_NOT_YET_SUBMITTED, false],
            [IrhpInterface::STATUS_UNDER_CONSIDERATION, false],
            [IrhpInterface::STATUS_WITHDRAWN, false],
            [IrhpInterface::STATUS_AWAITING_FEE, false],
            [IrhpInterface::STATUS_FEE_PAID, false],
            [IrhpInterface::STATUS_UNSUCCESSFUL, false],
            [IrhpInterface::STATUS_ISSUED, false],
            [IrhpInterface::STATUS_ISSUING, false],
            [IrhpInterface::STATUS_VALID, true],
        ];
    }

    /**
     * @dataProvider dpTestIsUnderConsideration
     */
    public function testIsUnderConsideration($status, $expected)
    {
        $this->sut->setStatus(new RefData($status));
        $this->assertSame($expected, $this->sut->isUnderConsideration());
    }

    public function dpTestIsUnderConsideration()
    {
        return [
            [IrhpInterface::STATUS_CANCELLED, false],
            [IrhpInterface::STATUS_NOT_YET_SUBMITTED, false],
            [IrhpInterface::STATUS_UNDER_CONSIDERATION, true],
            [IrhpInterface::STATUS_WITHDRAWN, false],
            [IrhpInterface::STATUS_AWAITING_FEE, false],
            [IrhpInterface::STATUS_FEE_PAID, false],
            [IrhpInterface::STATUS_UNSUCCESSFUL, false],
            [IrhpInterface::STATUS_ISSUED, false],
            [IrhpInterface::STATUS_ISSUING, false],
            [IrhpInterface::STATUS_VALID, false],
        ];
    }

    /**
     * @dataProvider dpTestIsAwaitingFee
     */
    public function testIsAwaitingFee($status, $expected)
    {
        $this->sut->setStatus(new RefData($status));
        $this->assertSame($expected, $this->sut->isAwaitingFee());
    }

    public function dpTestIsAwaitingFee()
    {
        return [
            [IrhpInterface::STATUS_CANCELLED, false],
            [IrhpInterface::STATUS_NOT_YET_SUBMITTED, false],
            [IrhpInterface::STATUS_UNDER_CONSIDERATION, false],
            [IrhpInterface::STATUS_WITHDRAWN, false],
            [IrhpInterface::STATUS_AWAITING_FEE, true],
            [IrhpInterface::STATUS_FEE_PAID, false],
            [IrhpInterface::STATUS_UNSUCCESSFUL, false],
            [IrhpInterface::STATUS_ISSUED, false],
            [IrhpInterface::STATUS_ISSUING, false],
            [IrhpInterface::STATUS_VALID, false],
        ];
    }

    /**
     * @dataProvider dpTestIsFeePaid
     */
    public function testIsFeePaid($status, $expected)
    {
        $this->sut->setStatus(new RefData($status));
        $this->assertSame($expected, $this->sut->isFeePaid());
    }

    public function dpTestIsFeePaid()
    {
        return [
            [IrhpInterface::STATUS_CANCELLED, false],
            [IrhpInterface::STATUS_NOT_YET_SUBMITTED, false],
            [IrhpInterface::STATUS_UNDER_CONSIDERATION, false],
            [IrhpInterface::STATUS_WITHDRAWN, false],
            [IrhpInterface::STATUS_AWAITING_FEE, false],
            [IrhpInterface::STATUS_FEE_PAID, true],
            [IrhpInterface::STATUS_UNSUCCESSFUL, false],
            [IrhpInterface::STATUS_ISSUED, false],
            [IrhpInterface::STATUS_ISSUING, false],
            [IrhpInterface::STATUS_VALID, false],
        ];
    }

    /**
     * @dataProvider dpTestIsIssueInProgress
     */
    public function testIsIssueInProgress($status, $expected)
    {
        $this->sut->setStatus(new RefData($status));
        $this->assertSame($expected, $this->sut->isIssueInProgress());
    }

    public function dpTestIsIssueInProgress()
    {
        return [
            [IrhpInterface::STATUS_CANCELLED, false],
            [IrhpInterface::STATUS_NOT_YET_SUBMITTED, false],
            [IrhpInterface::STATUS_UNDER_CONSIDERATION, false],
            [IrhpInterface::STATUS_WITHDRAWN, false],
            [IrhpInterface::STATUS_AWAITING_FEE, false],
            [IrhpInterface::STATUS_FEE_PAID, false],
            [IrhpInterface::STATUS_UNSUCCESSFUL, false],
            [IrhpInterface::STATUS_ISSUED, false],
            [IrhpInterface::STATUS_ISSUING, true],
            [IrhpInterface::STATUS_VALID, false],
        ];
    }

    /**
     * @dataProvider dpTestIsActive
     */
    public function testIsActive($status, $expected)
    {
        $this->sut->setStatus(new RefData($status));
        $this->assertSame($expected, $this->sut->isActive());
    }

    public function dpTestIsActive()
    {
        return [
            [IrhpInterface::STATUS_CANCELLED, false],
            [IrhpInterface::STATUS_NOT_YET_SUBMITTED, true],
            [IrhpInterface::STATUS_UNDER_CONSIDERATION, true],
            [IrhpInterface::STATUS_WITHDRAWN, false],
            [IrhpInterface::STATUS_AWAITING_FEE, true],
            [IrhpInterface::STATUS_FEE_PAID, true],
            [IrhpInterface::STATUS_UNSUCCESSFUL, false],
            [IrhpInterface::STATUS_ISSUED, false],
            [IrhpInterface::STATUS_ISSUING, true],
            [IrhpInterface::STATUS_VALID, false],
            [IrhpInterface::STATUS_EXPIRED, false],
        ];
    }

    /**
     * Tests cancelling an application
     */
    public function testCancel()
    {
        $entity = m::mock(Entity::class)->makePartial();
        $entity->setStatus(new RefData(IrhpInterface::STATUS_NOT_YET_SUBMITTED));
        $entity->cancel(new RefData(IrhpInterface::STATUS_CANCELLED));
        $this->assertEquals(IrhpInterface::STATUS_CANCELLED, $entity->getStatus()->getId());
        $this->assertEquals(date('Y-m-d'), $entity->getCancellationDate()->format('Y-m-d'));
    }

    /**
     * @dataProvider dpCancelException
     */
    public function testCancelException($status)
    {
        $this->expectException(ForbiddenException::class);
        $this->expectExceptionMessage(Entity::ERR_CANT_CANCEL);
        $entity = m::mock(Entity::class)->makePartial();
        $entity->setStatus(new RefData($status));
        $entity->cancel(new RefData(IrhpInterface::STATUS_CANCELLED));
    }

    /**
     * Pass array of app status to make sure an exception is thrown
     *
     * @return array
     */
    public function dpCancelException()
    {
        return [
            [IrhpInterface::STATUS_CANCELLED],
            [IrhpInterface::STATUS_UNDER_CONSIDERATION],
            [IrhpInterface::STATUS_WITHDRAWN],
            [IrhpInterface::STATUS_AWAITING_FEE],
            [IrhpInterface::STATUS_FEE_PAID],
            [IrhpInterface::STATUS_UNSUCCESSFUL],
            [IrhpInterface::STATUS_ISSUED],
            [IrhpInterface::STATUS_ISSUING],
            [IrhpInterface::STATUS_VALID],
        ];
    }

    /**
     * @dataProvider dpTestCanBeCancelled
     */
    public function testCanBeCancelled($status, $expected)
    {
        $this->sut->setStatus(new RefData($status));
        $this->assertSame($expected, $this->sut->canBeCancelled());
    }

    public function dpTestCanBeCancelled()
    {
        return [
            [IrhpInterface::STATUS_CANCELLED, false],
            [IrhpInterface::STATUS_NOT_YET_SUBMITTED, true],
            [IrhpInterface::STATUS_UNDER_CONSIDERATION, false],
            [IrhpInterface::STATUS_WITHDRAWN, false],
            [IrhpInterface::STATUS_AWAITING_FEE, false],
            [IrhpInterface::STATUS_FEE_PAID, false],
            [IrhpInterface::STATUS_UNSUCCESSFUL, false],
            [IrhpInterface::STATUS_ISSUED, false],
            [IrhpInterface::STATUS_ISSUING, false],
            [IrhpInterface::STATUS_VALID, false],
        ];
    }

    /**
     * @dataProvider dpTestIsCancelled
     */
    public function testIsCancelled($status, $expected)
    {
        $this->sut->setStatus(new RefData($status));
        $this->assertSame($expected, $this->sut->isCancelled());
    }

    public function dpTestIsCancelled()
    {
        return [
            [IrhpInterface::STATUS_CANCELLED, true],
            [IrhpInterface::STATUS_NOT_YET_SUBMITTED, false],
            [IrhpInterface::STATUS_UNDER_CONSIDERATION, false],
            [IrhpInterface::STATUS_WITHDRAWN, false],
            [IrhpInterface::STATUS_AWAITING_FEE, false],
            [IrhpInterface::STATUS_FEE_PAID, false],
            [IrhpInterface::STATUS_UNSUCCESSFUL, false],
            [IrhpInterface::STATUS_ISSUED, false],
            [IrhpInterface::STATUS_ISSUING, false],
            [IrhpInterface::STATUS_VALID, false],
        ];
    }

    /**
     * @dataProvider dpTestCanBeWithdrawn
     */
    public function testCanBeWithdrawn($status, $expected)
    {
        $entity = $this->createNewEntity(null, new RefData($status));
        $this->assertSame($expected, $entity->canBeWithdrawn());
    }

    public function dpTestCanBeWithdrawn()
    {
        return [
            [IrhpInterface::STATUS_CANCELLED, false],
            [IrhpInterface::STATUS_NOT_YET_SUBMITTED, false],
            [IrhpInterface::STATUS_UNDER_CONSIDERATION, true],
            [IrhpInterface::STATUS_WITHDRAWN, false],
            [IrhpInterface::STATUS_AWAITING_FEE, false],
            [IrhpInterface::STATUS_FEE_PAID, false],
            [IrhpInterface::STATUS_UNSUCCESSFUL, false],
            [IrhpInterface::STATUS_ISSUED, false],
            [IrhpInterface::STATUS_ISSUING, false],
            [IrhpInterface::STATUS_VALID, false],
        ];
    }

    /**
     * @dataProvider dpTestIsWithdrawn
     */
    public function testIsWithdrawn($status, $expected)
    {
        $entity = $this->createNewEntity(null, new RefData($status));
        $this->assertSame($expected, $entity->isWithdrawn());
    }

    public function dpTestIsWithdrawn()
    {
        return [
            [IrhpInterface::STATUS_CANCELLED, false],
            [IrhpInterface::STATUS_NOT_YET_SUBMITTED, false],
            [IrhpInterface::STATUS_UNDER_CONSIDERATION, false],
            [IrhpInterface::STATUS_WITHDRAWN, true],
            [IrhpInterface::STATUS_AWAITING_FEE, false],
            [IrhpInterface::STATUS_FEE_PAID, false],
            [IrhpInterface::STATUS_UNSUCCESSFUL, false],
            [IrhpInterface::STATUS_ISSUED, false],
            [IrhpInterface::STATUS_ISSUING, false],
            [IrhpInterface::STATUS_VALID, false],
        ];
    }

    /**
     * @dataProvider dpTestIsDeclined
     */
    public function testIsDeclined($reason, $expected)
    {
        $entity = $this->createNewEntity(null, new RefData(IrhpInterface::STATUS_WITHDRAWN));
        $entity->setWithdrawReason(new RefData($reason));
        $this->assertSame($expected, $entity->isDeclined());
    }

    public function dpTestIsDeclined()
    {
        return [
            [WithdrawableInterface::WITHDRAWN_REASON_DECLINED, true],
            [WithdrawableInterface::WITHDRAWN_REASON_BY_USER, false],
            [WithdrawableInterface::WITHDRAWN_REASON_UNPAID, false],
        ];
    }

    /**
     * @dataProvider dpWithdraw
     */
    public function testWithdraw($status, $reason)
    {
        $entity = $this->createNewEntity(null, new RefData($status));
        $entity->withdraw(
            new RefData(IrhpInterface::STATUS_WITHDRAWN),
            new RefData($reason)
        );
        $this->assertEquals(IrhpInterface::STATUS_WITHDRAWN, $entity->getStatus()->getId());
        $this->assertEquals($reason, $entity->getWithdrawReason()->getId());
        $this->assertEquals(date('Y-m-d'), $entity->getWithdrawnDate()->format('Y-m-d'));
    }

    public function dpWithdraw()
    {
        return [
            [
                IrhpInterface::STATUS_UNDER_CONSIDERATION,
                WithdrawableInterface::WITHDRAWN_REASON_BY_USER
            ],
            [
                IrhpInterface::STATUS_AWAITING_FEE,
                WithdrawableInterface::WITHDRAWN_REASON_DECLINED
            ],
        ];
    }

    /**
     * @dataProvider dpWithdrawException
     */
    public function testWithdrawException($status, $reason, $expectedError)
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage($expectedError);
        $entity = $this->createNewEntity(null, new RefData($status));
        $entity->withdraw(
            new RefData(IrhpInterface::STATUS_WITHDRAWN),
            new RefData($reason)
        );
    }

    /**
     * Pass array of app status to make sure an exception is thrown
     *
     * @return array
     */
    public function dpWithdrawException()
    {
        return [
            [
                IrhpInterface::STATUS_CANCELLED,
                WithdrawableInterface::WITHDRAWN_REASON_BY_USER,
                Entity::ERR_CANT_WITHDRAW
            ],
            [
                IrhpInterface::STATUS_NOT_YET_SUBMITTED,
                WithdrawableInterface::WITHDRAWN_REASON_BY_USER,
                Entity::ERR_CANT_WITHDRAW
            ],
            [
                IrhpInterface::STATUS_WITHDRAWN,
                WithdrawableInterface::WITHDRAWN_REASON_BY_USER,
                Entity::ERR_CANT_WITHDRAW
            ],
            [
                IrhpInterface::STATUS_AWAITING_FEE,
                WithdrawableInterface::WITHDRAWN_REASON_BY_USER,
                Entity::ERR_CANT_WITHDRAW
            ],
            [
                IrhpInterface::STATUS_FEE_PAID,
                WithdrawableInterface::WITHDRAWN_REASON_BY_USER,
                Entity::ERR_CANT_WITHDRAW
            ],
            [
                IrhpInterface::STATUS_UNSUCCESSFUL,
                WithdrawableInterface::WITHDRAWN_REASON_BY_USER,
                Entity::ERR_CANT_WITHDRAW
            ],
            [
                IrhpInterface::STATUS_ISSUED,
                WithdrawableInterface::WITHDRAWN_REASON_BY_USER,
                Entity::ERR_CANT_WITHDRAW
            ],
            [
                IrhpInterface::STATUS_ISSUING,
                WithdrawableInterface::WITHDRAWN_REASON_BY_USER,
                Entity::ERR_CANT_WITHDRAW
            ],
            [
                IrhpInterface::STATUS_VALID,
                WithdrawableInterface::WITHDRAWN_REASON_BY_USER,
                Entity::ERR_CANT_WITHDRAW
            ],
            [
                IrhpInterface::STATUS_CANCELLED,
                WithdrawableInterface::WITHDRAWN_REASON_DECLINED,
                Entity::ERR_CANT_DECLINE
            ],
            [
                IrhpInterface::STATUS_NOT_YET_SUBMITTED,
                WithdrawableInterface::WITHDRAWN_REASON_DECLINED,
                Entity::ERR_CANT_DECLINE
            ],
            [
                IrhpInterface::STATUS_WITHDRAWN,
                WithdrawableInterface::WITHDRAWN_REASON_DECLINED,
                Entity::ERR_CANT_DECLINE
            ],
            [
                IrhpInterface::STATUS_FEE_PAID,
                WithdrawableInterface::WITHDRAWN_REASON_DECLINED,
                Entity::ERR_CANT_DECLINE
            ],
            [
                IrhpInterface::STATUS_UNSUCCESSFUL,
                WithdrawableInterface::WITHDRAWN_REASON_DECLINED,
                Entity::ERR_CANT_DECLINE
            ],
            [
                IrhpInterface::STATUS_ISSUED,
                WithdrawableInterface::WITHDRAWN_REASON_DECLINED,
                Entity::ERR_CANT_DECLINE
            ],
            [
                IrhpInterface::STATUS_ISSUING,
                WithdrawableInterface::WITHDRAWN_REASON_DECLINED,
                Entity::ERR_CANT_DECLINE
            ],
            [
                IrhpInterface::STATUS_VALID,
                WithdrawableInterface::WITHDRAWN_REASON_DECLINED,
                Entity::ERR_CANT_DECLINE
            ],
        ];
    }

    /**
     * @dataProvider trueOrFalseProvider
     */
    public function testIsBilateral($isBilateral)
    {
        $irhpPermitType = m::mock(IrhpPermitType::class);
        $irhpPermitType->shouldReceive('isBilateral')->once()->withNoArgs()->andReturn($isBilateral);
        $entity = $this->createNewEntity(null, null, $irhpPermitType);
        $this->assertEquals($isBilateral, $entity->isBilateral());
    }

    /**
     * @dataProvider trueOrFalseProvider
     */
    public function testIsCertificateOfRoadworthiness($isCertificateOfRoadworthiness)
    {
        $irhpPermitType = m::mock(IrhpPermitType::class);
        $irhpPermitType->shouldReceive('isCertificateOfRoadworthiness')->once()->withNoArgs()->andReturn($isCertificateOfRoadworthiness);
        $entity = $this->createNewEntity(null, null, $irhpPermitType);
        $this->assertEquals($isCertificateOfRoadworthiness, $entity->isCertificateOfRoadworthiness());
    }

    /**
     * @dataProvider trueOrFalseProvider
     */
    public function testIsMultiStock($isMultiStock)
    {
        $irhpPermitType = m::mock(IrhpPermitType::class);
        $irhpPermitType->shouldReceive('isMultiStock')->once()->withNoArgs()->andReturn($isMultiStock);
        $entity = $this->createNewEntity(null, null, $irhpPermitType);
        $this->assertEquals($isMultiStock, $entity->isMultiStock());
    }

    public function trueOrFalseProvider()
    {
        return [
            [true],
            [false],
        ];
    }

    public function testGetAssociatedStock()
    {
        $irhpPermitStock = m::mock(IrhpPermitStock::class);

        $irhpPermitType = m::mock(IrhpPermitType::class);
        $irhpPermitType->shouldReceive('isMultiStock')->once()->withNoArgs()->andReturn(false);

        $irhpPermitApplication = m::mock(IrhpPermitApplication::class);
        $irhpPermitApplication->shouldReceive('getIrhpPermitWindow->getIrhpPermitStock')
            ->once()
            ->withNoArgs()
            ->andReturn($irhpPermitStock);

        $entity = $this->createNewEntity(null, null, $irhpPermitType);
        $entity->setIrhpPermitApplications(new ArrayCollection([$irhpPermitApplication]));

        $this->assertEquals($irhpPermitStock, $entity->getAssociatedStock());
    }

    public function testGetAssociatedStockMultiStockException()
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Multi stock permit types can\'t use this method');

        $irhpPermitType = m::mock(IrhpPermitType::class);
        $irhpPermitType->shouldReceive('isMultiStock')->once()->withNoArgs()->andReturn(true);

        $entity = $this->createNewEntity(null, null, $irhpPermitType);

        $entity->getAssociatedStock();
    }

    /**
     * @dataProvider dpIsNotYetSubmitted
     */
    public function testIsNotYetSubmitted($status, $expectedNotYetSubmitted)
    {
        $statusRefData = m::mock(RefData::class);
        $statusRefData->shouldReceive('getId')
            ->andReturn($status);

        $irhpApplication = new Entity();
        $irhpApplication->setStatus($statusRefData);

        $this->assertEquals(
            $expectedNotYetSubmitted,
            $irhpApplication->isNotYetSubmitted()
        );
    }

    public function dpIsNotYetSubmitted()
    {
        return [
            [IrhpInterface::STATUS_CANCELLED, false],
            [IrhpInterface::STATUS_NOT_YET_SUBMITTED, true],
            [IrhpInterface::STATUS_UNDER_CONSIDERATION, false],
            [IrhpInterface::STATUS_WITHDRAWN, false],
            [IrhpInterface::STATUS_AWAITING_FEE, false],
            [IrhpInterface::STATUS_FEE_PAID, false],
            [IrhpInterface::STATUS_UNSUCCESSFUL, false],
            [IrhpInterface::STATUS_ISSUED, false],
            [IrhpInterface::STATUS_ISSUING, false],
            [IrhpInterface::STATUS_VALID, false],
        ];
    }

    /**
     * @dataProvider dpIsSubmittedForConsideration
     */
    public function testIsSubmittedForConsideration($irhpPermitTypeId, $status, $expected)
    {
        $irhpPermitType = new IrhpPermitType();
        $irhpPermitType->setId($irhpPermitTypeId);

        $statusRefData = m::mock(RefData::class);
        $statusRefData->shouldReceive('getId')
            ->andReturn($status);

        $irhpApplication = new Entity();
        $irhpApplication->setStatus($statusRefData);
        $irhpApplication->setIrhpPermitType($irhpPermitType);

        $this->assertEquals(
            $expected,
            $irhpApplication->isSubmittedForConsideration()
        );
    }

    public function dpIsSubmittedForConsideration()
    {
        return [
            [IrhpPermitType::IRHP_PERMIT_TYPE_ID_ECMT_SHORT_TERM, IrhpInterface::STATUS_CANCELLED, false],
            [IrhpPermitType::IRHP_PERMIT_TYPE_ID_ECMT_SHORT_TERM, IrhpInterface::STATUS_NOT_YET_SUBMITTED, false],
            [IrhpPermitType::IRHP_PERMIT_TYPE_ID_ECMT_SHORT_TERM, IrhpInterface::STATUS_UNDER_CONSIDERATION, true],
            [IrhpPermitType::IRHP_PERMIT_TYPE_ID_ECMT_SHORT_TERM, IrhpInterface::STATUS_WITHDRAWN, false],
            [IrhpPermitType::IRHP_PERMIT_TYPE_ID_ECMT_SHORT_TERM, IrhpInterface::STATUS_AWAITING_FEE, false],
            [IrhpPermitType::IRHP_PERMIT_TYPE_ID_ECMT_SHORT_TERM, IrhpInterface::STATUS_FEE_PAID, false],
            [IrhpPermitType::IRHP_PERMIT_TYPE_ID_ECMT_SHORT_TERM, IrhpInterface::STATUS_UNSUCCESSFUL, false],
            [IrhpPermitType::IRHP_PERMIT_TYPE_ID_ECMT_SHORT_TERM, IrhpInterface::STATUS_ISSUED, false],
            [IrhpPermitType::IRHP_PERMIT_TYPE_ID_ECMT_SHORT_TERM, IrhpInterface::STATUS_ISSUING, false],
            [IrhpPermitType::IRHP_PERMIT_TYPE_ID_ECMT_SHORT_TERM, IrhpInterface::STATUS_VALID, false],
            [IrhpPermitType::IRHP_PERMIT_TYPE_ID_ECMT_REMOVAL, IrhpInterface::STATUS_CANCELLED, false],
            [IrhpPermitType::IRHP_PERMIT_TYPE_ID_ECMT_REMOVAL, IrhpInterface::STATUS_NOT_YET_SUBMITTED, false],
            [IrhpPermitType::IRHP_PERMIT_TYPE_ID_ECMT_REMOVAL, IrhpInterface::STATUS_UNDER_CONSIDERATION, false],
            [IrhpPermitType::IRHP_PERMIT_TYPE_ID_ECMT_REMOVAL, IrhpInterface::STATUS_WITHDRAWN, false],
            [IrhpPermitType::IRHP_PERMIT_TYPE_ID_ECMT_REMOVAL, IrhpInterface::STATUS_AWAITING_FEE, false],
            [IrhpPermitType::IRHP_PERMIT_TYPE_ID_ECMT_REMOVAL, IrhpInterface::STATUS_FEE_PAID, false],
            [IrhpPermitType::IRHP_PERMIT_TYPE_ID_ECMT_REMOVAL, IrhpInterface::STATUS_UNSUCCESSFUL, false],
            [IrhpPermitType::IRHP_PERMIT_TYPE_ID_ECMT_REMOVAL, IrhpInterface::STATUS_ISSUED, false],
            [IrhpPermitType::IRHP_PERMIT_TYPE_ID_ECMT_REMOVAL, IrhpInterface::STATUS_ISSUING, false],
            [IrhpPermitType::IRHP_PERMIT_TYPE_ID_ECMT_REMOVAL, IrhpInterface::STATUS_VALID, false],
            [IrhpPermitType::IRHP_PERMIT_TYPE_ID_BILATERAL, IrhpInterface::STATUS_CANCELLED, false],
            [IrhpPermitType::IRHP_PERMIT_TYPE_ID_BILATERAL, IrhpInterface::STATUS_NOT_YET_SUBMITTED, false],
            [IrhpPermitType::IRHP_PERMIT_TYPE_ID_BILATERAL, IrhpInterface::STATUS_UNDER_CONSIDERATION, false],
            [IrhpPermitType::IRHP_PERMIT_TYPE_ID_BILATERAL, IrhpInterface::STATUS_WITHDRAWN, false],
            [IrhpPermitType::IRHP_PERMIT_TYPE_ID_BILATERAL, IrhpInterface::STATUS_AWAITING_FEE, false],
            [IrhpPermitType::IRHP_PERMIT_TYPE_ID_BILATERAL, IrhpInterface::STATUS_FEE_PAID, false],
            [IrhpPermitType::IRHP_PERMIT_TYPE_ID_BILATERAL, IrhpInterface::STATUS_UNSUCCESSFUL, false],
            [IrhpPermitType::IRHP_PERMIT_TYPE_ID_BILATERAL, IrhpInterface::STATUS_ISSUED, false],
            [IrhpPermitType::IRHP_PERMIT_TYPE_ID_BILATERAL, IrhpInterface::STATUS_ISSUING, false],
            [IrhpPermitType::IRHP_PERMIT_TYPE_ID_BILATERAL, IrhpInterface::STATUS_VALID, false],
            [IrhpPermitType::IRHP_PERMIT_TYPE_ID_MULTILATERAL, IrhpInterface::STATUS_CANCELLED, false],
            [IrhpPermitType::IRHP_PERMIT_TYPE_ID_MULTILATERAL, IrhpInterface::STATUS_NOT_YET_SUBMITTED, false],
            [IrhpPermitType::IRHP_PERMIT_TYPE_ID_MULTILATERAL, IrhpInterface::STATUS_UNDER_CONSIDERATION, false],
            [IrhpPermitType::IRHP_PERMIT_TYPE_ID_MULTILATERAL, IrhpInterface::STATUS_WITHDRAWN, false],
            [IrhpPermitType::IRHP_PERMIT_TYPE_ID_MULTILATERAL, IrhpInterface::STATUS_AWAITING_FEE, false],
            [IrhpPermitType::IRHP_PERMIT_TYPE_ID_MULTILATERAL, IrhpInterface::STATUS_FEE_PAID, false],
            [IrhpPermitType::IRHP_PERMIT_TYPE_ID_MULTILATERAL, IrhpInterface::STATUS_UNSUCCESSFUL, false],
            [IrhpPermitType::IRHP_PERMIT_TYPE_ID_MULTILATERAL, IrhpInterface::STATUS_ISSUED, false],
            [IrhpPermitType::IRHP_PERMIT_TYPE_ID_MULTILATERAL, IrhpInterface::STATUS_ISSUING, false],
            [IrhpPermitType::IRHP_PERMIT_TYPE_ID_MULTILATERAL, IrhpInterface::STATUS_VALID, false],
        ];
    }

    /**
     * @dataProvider dpCanBeUpdated
     */
    public function testCanBeUpdated($status, $expectedCanBeUpdated)
    {
        $statusRefData = m::mock(RefData::class);
        $statusRefData->shouldReceive('getId')
            ->andReturn($status);

        $irhpApplication = new Entity();
        $irhpApplication->setStatus($statusRefData);

        $this->assertEquals(
            $expectedCanBeUpdated,
            $irhpApplication->canBeUpdated()
        );
    }

    public function dpCanBeUpdated()
    {
        return [
            [IrhpInterface::STATUS_CANCELLED, false],
            [IrhpInterface::STATUS_NOT_YET_SUBMITTED, true],
            [IrhpInterface::STATUS_UNDER_CONSIDERATION, true],
            [IrhpInterface::STATUS_WITHDRAWN, false],
            [IrhpInterface::STATUS_AWAITING_FEE, false],
            [IrhpInterface::STATUS_FEE_PAID, false],
            [IrhpInterface::STATUS_UNSUCCESSFUL, false],
            [IrhpInterface::STATUS_ISSUED, false],
            [IrhpInterface::STATUS_ISSUING, false],
            [IrhpInterface::STATUS_VALID, false],
        ];
    }

    public function testHasCheckedAnswers()
    {
        $this->assertFalse($this->sut->hasCheckedAnswers());

        $this->sut->setCheckedAnswers(true);
        $this->assertTrue($this->sut->hasCheckedAnswers());
    }

    public function testHasMadeDeclaration()
    {
        $this->assertFalse($this->sut->hasMadeDeclaration());

        $this->sut->setDeclaration(true);
        $this->assertTrue($this->sut->hasMadeDeclaration());
    }

    /**
     * @dataProvider dpCanBeSubmittedStatusIncorrect
     */
    public function testCanBeSubmittedStatusIncorrect(string $statusId)
    {
        $status = m::mock(RefData::class);
        $status->expects('getId')->withNoArgs()->andReturn($statusId);
        $entity = $this->createNewEntity(null, $status);
        self::assertFalse($entity->canBeSubmitted());
    }

    public function dpCanBeSubmittedStatusIncorrect()
    {
        return [
            [IrhpInterface::STATUS_CANCELLED],
            [IrhpInterface::STATUS_UNDER_CONSIDERATION],
            [IrhpInterface::STATUS_WITHDRAWN],
            [IrhpInterface::STATUS_AWAITING_FEE],
            [IrhpInterface::STATUS_FEE_PAID],
            [IrhpInterface::STATUS_UNSUCCESSFUL],
            [IrhpInterface::STATUS_ISSUED],
            [IrhpInterface::STATUS_ISSUING],
            [IrhpInterface::STATUS_VALID],
            [IrhpInterface::STATUS_EXPIRED],
        ];
    }

    /**
     * @dataProvider trueOrFalseProvider
     */
    public function testCanBeSubmittedMultiStock($allSectionsCompleted)
    {
        $status = m::mock(RefData::class);
        $status->expects()->getId()->withNoArgs()->andReturn(IrhpInterface::STATUS_NOT_YET_SUBMITTED);
        $irhpPermitType = m::mock(IrhpPermitType::class);
        $irhpPermitType->expects()->isMultiStock()->withNoArgs()->andReturnTrue();

        $this->sut->setStatus($status);
        $this->sut->setIrhpPermitType($irhpPermitType);

        $this->sut->expects()
            ->getSectionCompletion()
            ->withNoArgs()
            ->andReturn(['allCompleted' => $allSectionsCompleted]);

        $this->assertSame($allSectionsCompleted, $this->sut->canBeSubmitted());
    }

    /**
     * @dataProvider canBeSubmittedWithLicenceCheckProvider
     */
    public function testCanBeSubmittedWithLicenceCheck($licenceAllowed, $allSectionsCompleted, $result)
    {
        $status = m::mock(RefData::class);
        $status->expects('getId')->withNoArgs()->andReturn(IrhpInterface::STATUS_NOT_YET_SUBMITTED);

        $irhpPermitType = m::mock(IrhpPermitType::class);
        $irhpPermitType->expects('isMultiStock')->twice()->withNoArgs()->andReturnFalse();

        $irhpPermitStock = m::mock(IrhpPermitStock::class);

        $irhpPermitApplication = m::mock(IrhpPermitApplication::class);
        $irhpPermitApplication->expects('getIrhpPermitWindow->getIrhpPermitStock')
            ->withNoArgs()
            ->andReturn($irhpPermitStock);

        $licence = m::mock(Licence::class);

        $this->sut->setStatus($status);
        $this->sut->setIrhpPermitType($irhpPermitType);
        $this->sut->setLicence($licence);
        $this->sut->setIrhpPermitApplications(new ArrayCollection([$irhpPermitApplication]));

        $licence->expects()->canMakeIrhpApplication($irhpPermitStock, $this->sut)->andReturn($licenceAllowed);

        $this->sut->expects()
            ->getSectionCompletion()
            ->times($licenceAllowed ? 1 : 0)
            ->withNoArgs()
            ->andReturn(['allCompleted' => $allSectionsCompleted]);

        $this->assertSame($result, $this->sut->canBeSubmitted());
    }

    public function canBeSubmittedWithLicenceCheckProvider()
    {
        return [
            [false, false, false],
            [true, false, false],
            [false, true, false],
            [true, true, true]
        ];
    }

    /**
     * @dataProvider dpTestCanUpdateCountries
     */
    public function testCanUpdateCountries($canBeUpdated, $irhpPermitTypeId, $isFieldReadyToComplete, $expected)
    {
        $irhpPermitType = m::mock(IrhpPermitType::class);
        $irhpPermitType->shouldReceive('getId')
            ->andReturn($irhpPermitTypeId);

        $this->sut->shouldReceive('canBeUpdated')
            ->andReturn($canBeUpdated)
            ->shouldReceive('getIrhpPermitType')
            ->andReturn($irhpPermitType)
            ->shouldReceive('isFieldReadyToComplete')
            ->with('countries')
            ->andReturn($isFieldReadyToComplete);

        $this->assertSame($expected, $this->sut->canUpdateCountries());
    }

    public function dpTestCanUpdateCountries()
    {
        return [
            'cannot be updated' => [
                'canBeUpdated' => false,
                'irhpPermitTypeId' => IrhpPermitType::IRHP_PERMIT_TYPE_ID_BILATERAL,
                'isFieldReadyToComplete' => true,
                'expected' => false,
            ],
            'incorrect type' => [
                'canBeUpdated' => true,
                'irhpPermitTypeId' => IrhpPermitType::IRHP_PERMIT_TYPE_ID_MULTILATERAL,
                'isFieldReadyToComplete' => true,
                'expected' => false,
            ],
            'the field not ready to complete' => [
                'canBeUpdated' => true,
                'irhpPermitTypeId' => IrhpPermitType::IRHP_PERMIT_TYPE_ID_BILATERAL,
                'isFieldReadyToComplete' => false,
                'expected' => false,
            ],
            'can be updated' => [
                'canBeUpdated' => true,
                'irhpPermitTypeId' => IrhpPermitType::IRHP_PERMIT_TYPE_ID_BILATERAL,
                'isFieldReadyToComplete' => true,
                'expected' => true,
            ],
        ];
    }

    /**
     * @dataProvider dpIsReadyForNoOfPermits
     */
    public function testIsReadyForNoOfPermits(
        $canBeUpdated,
        $irhpPermitApplications,
        $expectedIsReadyForNoOfPermits
    ) {
        $irhpApplication = m::mock(Entity::class)->makePartial();

        $irhpApplication->shouldReceive('canBeUpdated')
            ->andReturn($canBeUpdated);

        $irhpApplication->setIrhpPermitApplications(
            new ArrayCollection($irhpPermitApplications)
        );

        $this->assertEquals(
            $expectedIsReadyForNoOfPermits,
            $irhpApplication->isReadyForNoOfPermits()
        );
    }

    public function dpIsReadyForNoOfPermits()
    {
        return [
            [
                true,
                [m::mock(IrhpPermitApplication::class), m::mock(IrhpPermitApplication::class)],
                true
            ],
            [
                true,
                [],
                false
            ],
            [
                false,
                [m::mock(IrhpPermitApplication::class), m::mock(IrhpPermitApplication::class)],
                false
            ],
            [
                false,
                [],
                false
            ],
        ];
    }

    /**
     * @dataProvider dpHasOutstandingFees
     */
    public function testHasOutstandingFees($feesData, $expectedResult)
    {
        $this->sut->setFees(
            $this->createFeesArrayCollectionFromArrayData($feesData)
        );

        $this->assertEquals($expectedResult, $this->sut->hasOutstandingFees());
    }

    public function dpHasOutstandingFees()
    {
        return [
            [
                'fees' => [
                    [
                        'invoicedDate' => '2019-01-04',
                        'isOutstanding' => false,
                        'feeTypeId' => FeeType::FEE_TYPE_IRHP_APP
                    ],
                    [
                        'invoicedDate' => '2019-01-04',
                        'isOutstanding' => false,
                        'feeTypeId' => FeeType::FEE_TYPE_IRHP_ISSUE
                    ],
                    [
                        'invoicedDate' => '2019-01-04',
                        'isOutstanding' => false,
                        'feeTypeId' => FeeType::FEE_TYPE_IRFOGVPERMIT
                    ]
                ],
                'expectedResult' => false
            ],
            [
                'fees' => [
                    [
                        'invoicedDate' => '2019-01-04',
                        'isOutstanding' => true,
                        'feeTypeId' => FeeType::FEE_TYPE_DUP
                    ],
                    [
                        'invoicedDate' => '2019-01-04',
                        'isOutstanding' => true,
                        'feeTypeId' => FeeType::FEE_TYPE_BUSAPP
                    ]
                ],
                'expectedResult' => false
            ],
            [
                'fees' => [
                    [
                        'invoicedDate' => '2019-01-04',
                        'isOutstanding' => true,
                        'feeTypeId' => FeeType::FEE_TYPE_IRHP_APP
                    ],
                    [
                        'invoicedDate' => '2019-01-04',
                        'isOutstanding' => false,
                        'feeTypeId' => FeeType::FEE_TYPE_IRHP_ISSUE
                    ],
                    [
                        'invoicedDate' => '2019-01-04',
                        'isOutstanding' => false,
                        'feeTypeId' => FeeType::FEE_TYPE_IRFOGVPERMIT
                    ]
                ],
                'expectedResult' => true
            ],
            [
                'fees' => [
                    [
                        'invoicedDate' => '2019-01-04',
                        'isOutstanding' => false,
                        'feeTypeId' => FeeType::FEE_TYPE_IRHP_APP
                    ],
                    [
                        'invoicedDate' => '2019-01-04',
                        'isOutstanding' => true,
                        'feeTypeId' => FeeType::FEE_TYPE_IRHP_ISSUE
                    ],
                    [
                        'invoicedDate' => '2019-01-04',
                        'isOutstanding' => false,
                        'feeTypeId' => FeeType::FEE_TYPE_IRFOGVPERMIT
                    ]
                ],
                'expectedResult' => true
            ],
            [
                'fees' => [
                    [
                        'invoicedDate' => '2019-01-04',
                        'isOutstanding' => false,
                        'feeTypeId' => FeeType::FEE_TYPE_IRHP_APP
                    ],
                    [
                        'invoicedDate' => '2019-01-04',
                        'isOutstanding' => false,
                        'feeTypeId' => FeeType::FEE_TYPE_IRHP_ISSUE
                    ],
                    [
                        'invoicedDate' => '2019-01-04',
                        'isOutstanding' => true,
                        'feeTypeId' => FeeType::FEE_TYPE_IRFOGVPERMIT
                    ]
                ],
                'expectedResult' => true
            ],
        ];
    }

    /**
     * Tests logic for finding overdue issue fees, and checks that the 4 fees over 10 days old are returned initially
     *
     * $fee1 isn't overdue, so is ignored
     * $fee2 is overdue, but doesn't need to be checked because $fee5 is more recent and will match
     * $fee3 is overdue, is outstanding, but isn't an issue fee
     * $fee4 would be overdue, but is not outstanding, so the fee type is not checked
     * $fee5 is overdue, outstanding and the correct fee type, causes the method to return true
     */
    public function testIssueFeeOverdue()
    {
        $dateTimeMinus9 = (new \DateTime('-9 weekdays'))->format(\DateTime::ISO8601);
        $dateTimeMinus10 = (new \DateTime('-10 weekdays'))->format(\DateTime::ISO8601);
        $dateTimeMinus11 = (new \DateTime('-11 weekdays'))->format(\DateTime::ISO8601);

        $fee1 = m::mock(Fee::class)->makePartial();
        $fee1->shouldReceive('isOutstanding')->never();
        $fee1->shouldReceive('getFeeType->isIrhpApplicationIssue')->never();
        $fee1->setInvoicedDate($dateTimeMinus9);

        $fee2 = m::mock(Fee::class)->makePartial();
        $fee2->shouldReceive('isOutstanding')->never();
        $fee2->shouldReceive('getFeeType->isIrhpApplicationIssue')->never();
        $fee2->setInvoicedDate($dateTimeMinus11);

        $fee3 = m::mock(Fee::class)->makePartial();
        $fee3->shouldReceive('isOutstanding')->once()->withNoArgs()->andReturn(true);
        $fee3->shouldReceive('getFeeType->isIrhpApplicationIssue')->once()->withNoArgs()->andReturn(false);
        $fee3->setInvoicedDate($dateTimeMinus10);

        $fee4 = m::mock(Fee::class)->makePartial();
        $fee4->shouldReceive('isOutstanding')->once()->withNoArgs()->andReturn(false);
        $fee4->shouldReceive('getFeeType->isIrhpApplicationIssue')->never();
        $fee4->setInvoicedDate($dateTimeMinus10);

        $fee5 = m::mock(Fee::class)->makePartial();
        $fee5->shouldReceive('isOutstanding')->once()->withNoArgs()->andReturn(true);
        $fee5->shouldReceive('getFeeType->isIrhpApplicationIssue')->once()->withNoArgs()->andReturn(true);
        $fee5->setInvoicedDate($dateTimeMinus10);

        $feesCollection = new ArrayCollection([$fee1, $fee2, $fee3, $fee4, $fee5]);

        $this->sut->setFees($feesCollection);

        $this->assertEquals(4, $this->sut->getFeesByAge()->count());
        $this->assertTrue($this->sut->issueFeeOverdue());
    }

    /**
     * @dataProvider dpIssueFeeOverdueProvider
     */
    public function testIssueFeeOverdueBoundary($days, $expected)
    {
        $invoiceDate = (new \DateTime('-' . $days . ' weekdays'))->format(\DateTime::ISO8601);

        $fee = m::mock(Fee::class)->makePartial();
        $fee->shouldReceive('isOutstanding')->times($expected)->andReturn(true);
        $fee->shouldReceive('getFeeType->isIrhpApplicationIssue')->times($expected)->andReturn(true);
        $fee->setInvoicedDate($invoiceDate);

        $feesCollection = new ArrayCollection([$fee]);

        $this->sut->setFees($feesCollection);

        $this->assertEquals($expected, $this->sut->getFeesByAge()->count());
        $this->assertEquals($expected, $this->sut->issueFeeOverdue());
    }

    public function dpIssueFeeOverdueProvider()
    {
        return [
            [9, 0],
            [10, 1],
            [11, 1],
        ];
    }

    /**
     * @dataProvider dpGetLatestOutstandingApplicationFee
     */
    public function testGetLatestOutstandingApplicationFee($feesData, $expectedIndex)
    {
        $fees = $this->createFeesArrayCollectionFromArrayData($feesData);
        $this->sut->setFees($fees);

        $latestOutstandingIssueFee = $this->sut->getLatestOutstandingApplicationFee();

        if (is_null($expectedIndex)) {
            $this->assertNull($latestOutstandingIssueFee);
        }

        $this->assertSame($fees[$expectedIndex], $latestOutstandingIssueFee);
    }

    public function dpGetLatestOutstandingApplicationFee()
    {
        return [
            [
                'fees' => [
                    [
                        'invoicedDate' => '2019-01-04',
                        'isOutstanding' => true,
                        'feeTypeId' => FeeType::FEE_TYPE_BUSAPP
                    ],
                    [
                        'invoicedDate' => '2019-01-04',
                        'isOutstanding' => true,
                        'feeTypeId' => FeeType::FEE_TYPE_BUSVAR
                    ]
                ],
                'expectedIndex' => null
            ],
            [
                'fees' => [
                    [
                        'invoicedDate' => '2019-01-04',
                        'isOutstanding' => true,
                        'feeTypeId' => FeeType::FEE_TYPE_IRHP_APP
                    ],
                    [
                        'invoicedDate' => '2019-01-08',
                        'isOutstanding' => true,
                        'feeTypeId' => FeeType::FEE_TYPE_IRHP_APP
                    ]
                ],
                'expectedIndex' => 1
            ],
            [
                'fees' => [
                    [
                        'invoicedDate' => '2019-01-08',
                        'isOutstanding' => true,
                        'feeTypeId' => FeeType::FEE_TYPE_IRHP_APP
                    ],
                    [
                        'invoicedDate' => '2019-01-04',
                        'isOutstanding' => true,
                        'feeTypeId' => FeeType::FEE_TYPE_IRHP_APP
                    ]
                ],
                'expectedIndex' => 0
            ],
            [
                'fees' => [
                    [
                        'invoicedDate' => '2019-01-04',
                        'isOutstanding' => true,
                        'feeTypeId' => FeeType::FEE_TYPE_IRHP_APP
                    ],
                    [
                        'invoicedDate' => '2019-01-08',
                        'isOutstanding' => false,
                        'feeTypeId' => FeeType::FEE_TYPE_IRHP_APP
                    ]
                ],
                'expectedIndex' => 0
            ],
            [
                'fees' => [
                    [
                        'invoicedDate' => '2019-01-04',
                        'isOutstanding' => true,
                        'feeTypeId' => FeeType::FEE_TYPE_IRHP_APP
                    ],
                    [
                        'invoicedDate' => '2019-01-08',
                        'isOutstanding' => true,
                        'feeTypeId' => FeeType::FEE_TYPE_IRHP_ISSUE
                    ]
                ],
                'expectedIndex' => 0
            ],
        ];
    }

    /**
     * @dataProvider dpGetLatestOutstandingIssueFee
     */
    public function testGetLatestOutstandingIssueFee($feesData, $expectedIndex)
    {
        $fees = $this->createFeesArrayCollectionFromArrayData($feesData);
        $this->sut->setFees($fees);

        $latestOutstandingIssueFee = $this->sut->getLatestOutstandingIssueFee();

        if (is_null($expectedIndex)) {
            $this->assertNull($latestOutstandingIssueFee);
        }

        $this->assertSame($fees[$expectedIndex], $latestOutstandingIssueFee);
    }

    public function dpGetLatestOutstandingIssueFee()
    {
        return [
            [
                'fees' => [
                    [
                        'invoicedDate' => '2019-01-04',
                        'isOutstanding' => true,
                        'feeTypeId' => FeeType::FEE_TYPE_BUSAPP
                    ],
                    [
                        'invoicedDate' => '2019-01-04',
                        'isOutstanding' => true,
                        'feeTypeId' => FeeType::FEE_TYPE_BUSVAR
                    ]
                ],
                'expectedIndex' => null
            ],
            [
                'fees' => [
                    [
                        'invoicedDate' => '2019-01-04',
                        'isOutstanding' => true,
                        'feeTypeId' => FeeType::FEE_TYPE_IRHP_ISSUE
                    ],
                    [
                        'invoicedDate' => '2019-01-08',
                        'isOutstanding' => true,
                        'feeTypeId' => FeeType::FEE_TYPE_IRHP_ISSUE
                    ]
                ],
                'expectedIndex' => 1
            ],
            [
                'fees' => [
                    [
                        'invoicedDate' => '2019-01-08',
                        'isOutstanding' => true,
                        'feeTypeId' => FeeType::FEE_TYPE_IRHP_ISSUE
                    ],
                    [
                        'invoicedDate' => '2019-01-04',
                        'isOutstanding' => true,
                        'feeTypeId' => FeeType::FEE_TYPE_IRHP_ISSUE
                    ]
                ],
                'expectedIndex' => 0
            ],
            [
                'fees' => [
                    [
                        'invoicedDate' => '2019-01-04',
                        'isOutstanding' => true,
                        'feeTypeId' => FeeType::FEE_TYPE_IRHP_ISSUE
                    ],
                    [
                        'invoicedDate' => '2019-01-08',
                        'isOutstanding' => false,
                        'feeTypeId' => FeeType::FEE_TYPE_IRHP_ISSUE
                    ]
                ],
                'expectedIndex' => 0
            ],
            [
                'fees' => [
                    [
                        'invoicedDate' => '2019-01-04',
                        'isOutstanding' => true,
                        'feeTypeId' => FeeType::FEE_TYPE_IRHP_ISSUE
                    ],
                    [
                        'invoicedDate' => '2019-01-08',
                        'isOutstanding' => true,
                        'feeTypeId' => FeeType::FEE_TYPE_IRHP_APP
                    ]
                ],
                'expectedIndex' => 0
            ],
        ];
    }

    /**
     * @dataProvider dpGetLatestIssueFee
     */
    public function testGetLatestIssueFee($feesData, $expectedIndex)
    {
        $fees = $this->createFeesArrayCollectionFromArrayData($feesData);
        $this->sut->setFees($fees);

        $latestOutstandingIssueFee = $this->sut->getLatestIssueFee();

        if (is_null($expectedIndex)) {
            $this->assertNull($latestOutstandingIssueFee);
        }

        $this->assertSame($fees[$expectedIndex], $latestOutstandingIssueFee);
    }

    public function dpGetLatestIssueFee()
    {
        return [
            [
                'fees' => [
                    [
                        'invoicedDate' => '2019-01-04',
                        'isOutstanding' => false,
                        'feeTypeId' => FeeType::FEE_TYPE_BUSAPP
                    ],
                    [
                        'invoicedDate' => '2019-01-04',
                        'isOutstanding' => false,
                        'feeTypeId' => FeeType::FEE_TYPE_BUSVAR
                    ]
                ],
                'expectedIndex' => null
            ],
            [
                'fees' => [
                    [
                        'invoicedDate' => '2019-01-04',
                        'isOutstanding' => false,
                        'feeTypeId' => FeeType::FEE_TYPE_IRHP_ISSUE
                    ],
                    [
                        'invoicedDate' => '2019-01-08',
                        'isOutstanding' => false,
                        'feeTypeId' => FeeType::FEE_TYPE_IRHP_ISSUE
                    ]
                ],
                'expectedIndex' => 1
            ],
            [
                'fees' => [
                    [
                        'invoicedDate' => '2019-01-08',
                        'isOutstanding' => false,
                        'feeTypeId' => FeeType::FEE_TYPE_IRHP_ISSUE
                    ],
                    [
                        'invoicedDate' => '2019-01-04',
                        'isOutstanding' => false,
                        'feeTypeId' => FeeType::FEE_TYPE_IRHP_ISSUE
                    ]
                ],
                'expectedIndex' => 0
            ],
            [
                'fees' => [
                    [
                        'invoicedDate' => '2019-01-04',
                        'isOutstanding' => false,
                        'feeTypeId' => FeeType::FEE_TYPE_IRHP_ISSUE
                    ],
                    [
                        'invoicedDate' => '2019-01-08',
                        'isOutstanding' => false,
                        'feeTypeId' => FeeType::FEE_TYPE_IRHP_APP
                    ]
                ],
                'expectedIndex' => 0
            ],
        ];
    }

    private function createFeesArrayCollectionFromArrayData($feesData)
    {
        $fees = [];
        foreach ($feesData as $feeData) {
            $fee = m::mock(Fee::class);
            $fee->shouldReceive('isOutstanding')
                ->andReturn($feeData['isOutstanding'])
                ->shouldReceive('getInvoicedDate')
                ->andReturn(new DateTime($feeData['invoicedDate']))
                ->shouldReceive('getFeeType->getFeeType->getId')
                ->andReturn($feeData['feeTypeId']);

            $fees[] = $fee;
        }

        return new ArrayCollection($fees);
    }

    public function testGetOutstandingFees()
    {
        $outstandingIrhpAppFee = m::mock(Fee::class);
        $outstandingIrhpAppFee->shouldReceive('isOutstanding')->once()->andReturn(true);
        $outstandingIrhpAppFee->shouldReceive('getFeeType->getFeeType->getId')
            ->once()
            ->andReturn(FeeType::FEE_TYPE_IRHP_APP);

        $outstandingIrhpIssueFee = m::mock(Fee::class);
        $outstandingIrhpIssueFee->shouldReceive('isOutstanding')->once()->andReturn(true);
        $outstandingIrhpIssueFee->shouldReceive('getFeeType->getFeeType->getId')
            ->once()
            ->andReturn(FeeType::FEE_TYPE_IRHP_ISSUE);

        $outstandingIrfoGvPermitFee = m::mock(Fee::class);
        $outstandingIrfoGvPermitFee->shouldReceive('isOutstanding')->once()->andReturn(true);
        $outstandingIrfoGvPermitFee->shouldReceive('getFeeType->getFeeType->getId')
            ->once()
            ->andReturn(FeeType::FEE_TYPE_IRFOGVPERMIT);

        $notOutstandingIrhpAppFee = m::mock(Fee::class);
        $notOutstandingIrhpAppFee->shouldReceive('isOutstanding')->once()->andReturn(false);
        $notOutstandingIrhpAppFee->shouldReceive('getFeeType->getFeeType->getId')->never();

        $notOutstandingIrhpIssueFee = m::mock(Fee::class);
        $notOutstandingIrhpIssueFee->shouldReceive('isOutstanding')->once()->andReturn(false);
        $notOutstandingIrhpIssueFee->shouldReceive('getFeeType->getFeeType->getId')->never();

        $notOutstandingIrfoGvPermitFee = m::mock(Fee::class);
        $notOutstandingIrfoGvPermitFee->shouldReceive('isOutstanding')->once()->andReturn(false);
        $notOutstandingIrfoGvPermitFee->shouldReceive('getFeeType->getFeeType->getId')->never();

        $allFees = [
            $outstandingIrhpAppFee,
            $outstandingIrhpIssueFee,
            $outstandingIrfoGvPermitFee,
            $notOutstandingIrhpAppFee,
            $notOutstandingIrhpIssueFee,
            $notOutstandingIrfoGvPermitFee,
        ];

        $outstandingFees = [
            $outstandingIrhpAppFee,
            $outstandingIrhpIssueFee,
            $outstandingIrfoGvPermitFee,
        ];

        $fees = new ArrayCollection($allFees);

        $this->sut->setFees($fees);

        $this->assertSame($outstandingFees, $this->sut->getOutstandingFees());
    }

    public function testGetOutstandingFeeAmount()
    {
        $outstandingIrhpAppFee = m::mock(Fee::class);
        $outstandingIrhpAppFee->shouldReceive('isOutstanding')->once()->andReturn(true);
        $outstandingIrhpAppFee->shouldReceive('getGrossAmount')->once()->andReturn(25.56);
        $outstandingIrhpAppFee->shouldReceive('getFeeType->getFeeType->getId')
            ->once()
            ->andReturn(FeeType::FEE_TYPE_IRHP_APP);

        $outstandingIrhpIssueFee = m::mock(Fee::class);
        $outstandingIrhpIssueFee->shouldReceive('isOutstanding')->once()->andReturn(true);
        $outstandingIrhpIssueFee->shouldReceive('getGrossAmount')->once()->andReturn(50);
        $outstandingIrhpIssueFee->shouldReceive('getFeeType->getFeeType->getId')
            ->once()
            ->andReturn(FeeType::FEE_TYPE_IRHP_ISSUE);

        $outstandingFees = [
            $outstandingIrhpAppFee,
            $outstandingIrhpIssueFee
        ];

        $fees = new ArrayCollection($outstandingFees);

        $this->sut->setFees($fees);

        $this->assertEquals(75.56, $this->sut->getOutstandingFeeAmount());
    }

    /**
     * @dataProvider dpTestGetSectionCompletion
     */
    public function testGetSectionCompletion($data, $expected)
    {
        $irhpPermitType = m::mock(IrhpPermitType::class)->makePartial();
        $irhpPermitType->setId($data['irhpPermitTypeId']);

        $this->sut->setIrhpPermitType($irhpPermitType);
        $this->sut->setLicence($data['licence']);
        $this->sut->setIrhpPermitApplications($data['irhpPermitApplications']);
        $this->sut->setCheckedAnswers($data['checkedAnswers']);
        $this->sut->setDeclaration($data['declaration']);

        $this->assertSame($expected, $this->sut->getSectionCompletion());
    }

    public function dpTestGetSectionCompletion()
    {
        $licence = m::mock(Licence::class);
        $irhpPermitAppWithoutPermits = m::mock(IrhpPermitApplication::class)->makePartial();

        $irhpPermitAppWithPermits = m::mock(IrhpPermitApplication::class)->makePartial();
        $irhpPermitAppWithPermits->setPermitsRequired(10);

        return [
            'Bilateral - no data set' => [
                'data' => [
                    'irhpPermitTypeId' => IrhpPermitType::IRHP_PERMIT_TYPE_ID_BILATERAL,
                    'licence' => null,
                    'irhpPermitApplications' => new ArrayCollection(),
                    'checkedAnswers' => false,
                    'declaration' => false,
                ],
                'expected' => [
                    'licence' => SectionableInterface::SECTION_COMPLETION_NOT_STARTED,
                    'countries' => SectionableInterface::SECTION_COMPLETION_NOT_STARTED,
                    'permitsRequired' => SectionableInterface::SECTION_COMPLETION_CANNOT_START,
                    'checkedAnswers' => SectionableInterface::SECTION_COMPLETION_CANNOT_START,
                    'declaration' => SectionableInterface::SECTION_COMPLETION_CANNOT_START,
                    'totalSections' => 5,
                    'totalCompleted' => 0,
                    'allCompleted' => false,
                ],
            ],
            'Bilateral - licence set' => [
                'data' => [
                    'irhpPermitTypeId' => IrhpPermitType::IRHP_PERMIT_TYPE_ID_BILATERAL,
                    'licence' => $licence,
                    'irhpPermitApplications' => new ArrayCollection(),
                    'checkedAnswers' => false,
                    'declaration' => false,
                ],
                'expected' => [
                    'licence' => SectionableInterface::SECTION_COMPLETION_COMPLETED,
                    'countries' => SectionableInterface::SECTION_COMPLETION_NOT_STARTED,
                    'permitsRequired' => SectionableInterface::SECTION_COMPLETION_CANNOT_START,
                    'checkedAnswers' => SectionableInterface::SECTION_COMPLETION_CANNOT_START,
                    'declaration' => SectionableInterface::SECTION_COMPLETION_CANNOT_START,
                    'totalSections' => 5,
                    'totalCompleted' => 1,
                    'allCompleted' => false,
                ],
            ],
            'Bilateral - IRHP permit apps with all apps without permits required set' => [
                'data' => [
                    'irhpPermitTypeId' => IrhpPermitType::IRHP_PERMIT_TYPE_ID_BILATERAL,
                    'licence' => $licence,
                    'irhpPermitApplications' => new ArrayCollection(
                        [
                            $irhpPermitAppWithoutPermits,
                            $irhpPermitAppWithoutPermits
                        ]
                    ),
                    'checkedAnswers' => false,
                    'declaration' => false,
                ],
                'expected' => [
                    'licence' => SectionableInterface::SECTION_COMPLETION_COMPLETED,
                    'countries' => SectionableInterface::SECTION_COMPLETION_COMPLETED,
                    'permitsRequired' => SectionableInterface::SECTION_COMPLETION_NOT_STARTED,
                    'checkedAnswers' => SectionableInterface::SECTION_COMPLETION_CANNOT_START,
                    'declaration' => SectionableInterface::SECTION_COMPLETION_CANNOT_START,
                    'totalSections' => 5,
                    'totalCompleted' => 2,
                    'allCompleted' => false,
                ],
            ],
            'Bilateral - IRHP permit apps with one app without permits required set' => [
                'data' => [
                    'irhpPermitTypeId' => IrhpPermitType::IRHP_PERMIT_TYPE_ID_BILATERAL,
                    'licence' => $licence,
                    'irhpPermitApplications' => new ArrayCollection(
                        [
                            $irhpPermitAppWithPermits,
                            $irhpPermitAppWithoutPermits
                        ]
                    ),
                    'checkedAnswers' => false,
                    'declaration' => false,
                ],
                'expected' => [
                    'licence' => SectionableInterface::SECTION_COMPLETION_COMPLETED,
                    'countries' => SectionableInterface::SECTION_COMPLETION_COMPLETED,
                    'permitsRequired' => SectionableInterface::SECTION_COMPLETION_NOT_STARTED,
                    'checkedAnswers' => SectionableInterface::SECTION_COMPLETION_CANNOT_START,
                    'declaration' => SectionableInterface::SECTION_COMPLETION_CANNOT_START,
                    'totalSections' => 5,
                    'totalCompleted' => 2,
                    'allCompleted' => false,
                ],
            ],
            'Bilateral - IRHP permit apps with all apps with permits required set' => [
                'data' => [
                    'irhpPermitTypeId' => IrhpPermitType::IRHP_PERMIT_TYPE_ID_BILATERAL,
                    'licence' => $licence,
                    'irhpPermitApplications' => new ArrayCollection(
                        [
                            $irhpPermitAppWithPermits,
                            $irhpPermitAppWithPermits
                        ]
                    ),
                    'checkedAnswers' => false,
                    'declaration' => false,
                ],
                'expected' => [
                    'licence' => SectionableInterface::SECTION_COMPLETION_COMPLETED,
                    'countries' => SectionableInterface::SECTION_COMPLETION_COMPLETED,
                    'permitsRequired' => SectionableInterface::SECTION_COMPLETION_COMPLETED,
                    'checkedAnswers' => SectionableInterface::SECTION_COMPLETION_NOT_STARTED,
                    'declaration' => SectionableInterface::SECTION_COMPLETION_CANNOT_START,
                    'totalSections' => 5,
                    'totalCompleted' => 3,
                    'allCompleted' => false,
                ],
            ],
            'Bilateral - checked answers set' => [
                'data' => [
                    'irhpPermitTypeId' => IrhpPermitType::IRHP_PERMIT_TYPE_ID_BILATERAL,
                    'licence' => $licence,
                    'irhpPermitApplications' => new ArrayCollection(
                        [
                            $irhpPermitAppWithPermits,
                            $irhpPermitAppWithPermits
                        ]
                    ),
                    'checkedAnswers' => true,
                    'declaration' => false,
                ],
                'expected' => [
                    'licence' => SectionableInterface::SECTION_COMPLETION_COMPLETED,
                    'countries' => SectionableInterface::SECTION_COMPLETION_COMPLETED,
                    'permitsRequired' => SectionableInterface::SECTION_COMPLETION_COMPLETED,
                    'checkedAnswers' => SectionableInterface::SECTION_COMPLETION_COMPLETED,
                    'declaration' => SectionableInterface::SECTION_COMPLETION_NOT_STARTED,
                    'totalSections' => 5,
                    'totalCompleted' => 4,
                    'allCompleted' => false,
                ],
            ],
            'Bilateral - declaration set' => [
                'data' => [
                    'irhpPermitTypeId' => IrhpPermitType::IRHP_PERMIT_TYPE_ID_BILATERAL,
                    'licence' => $licence,
                    'irhpPermitApplications' => new ArrayCollection(
                        [
                            $irhpPermitAppWithPermits,
                            $irhpPermitAppWithPermits
                        ]
                    ),
                    'checkedAnswers' => true,
                    'declaration' => true,
                ],
                'expected' => [
                    'licence' => SectionableInterface::SECTION_COMPLETION_COMPLETED,
                    'countries' => SectionableInterface::SECTION_COMPLETION_COMPLETED,
                    'permitsRequired' => SectionableInterface::SECTION_COMPLETION_COMPLETED,
                    'checkedAnswers' => SectionableInterface::SECTION_COMPLETION_COMPLETED,
                    'declaration' => SectionableInterface::SECTION_COMPLETION_COMPLETED,
                    'totalSections' => 5,
                    'totalCompleted' => 5,
                    'allCompleted' => true,
                ],
            ],
            'Multilateral - no data set' => [
                'data' => [
                    'irhpPermitTypeId' => IrhpPermitType::IRHP_PERMIT_TYPE_ID_MULTILATERAL,
                    'licence' => null,
                    'irhpPermitApplications' => new ArrayCollection(),
                    'checkedAnswers' => false,
                    'declaration' => false,
                ],
                'expected' => [
                    'licence' => SectionableInterface::SECTION_COMPLETION_NOT_STARTED,
                    'permitsRequired' => SectionableInterface::SECTION_COMPLETION_NOT_STARTED,
                    'checkedAnswers' => SectionableInterface::SECTION_COMPLETION_CANNOT_START,
                    'declaration' => SectionableInterface::SECTION_COMPLETION_CANNOT_START,
                    'totalSections' => 4,
                    'totalCompleted' => 0,
                    'allCompleted' => false,
                ],
            ],
            'Multilateral - licence set' => [
                'data' => [
                    'irhpPermitTypeId' => IrhpPermitType::IRHP_PERMIT_TYPE_ID_MULTILATERAL,
                    'licence' => $licence,
                    'irhpPermitApplications' => new ArrayCollection(),
                    'checkedAnswers' => false,
                    'declaration' => false,
                ],
                'expected' => [
                    'licence' => SectionableInterface::SECTION_COMPLETION_COMPLETED,
                    'permitsRequired' => SectionableInterface::SECTION_COMPLETION_NOT_STARTED,
                    'checkedAnswers' => SectionableInterface::SECTION_COMPLETION_CANNOT_START,
                    'declaration' => SectionableInterface::SECTION_COMPLETION_CANNOT_START,
                    'totalSections' => 4,
                    'totalCompleted' => 1,
                    'allCompleted' => false,
                ],
            ],
            'Multilateral - IRHP permit apps with all apps without permits required set' => [
                'data' => [
                    'irhpPermitTypeId' => IrhpPermitType::IRHP_PERMIT_TYPE_ID_MULTILATERAL,
                    'licence' => $licence,
                    'irhpPermitApplications' => new ArrayCollection(
                        [
                            $irhpPermitAppWithoutPermits,
                            $irhpPermitAppWithoutPermits
                        ]
                    ),
                    'checkedAnswers' => false,
                    'declaration' => false,
                ],
                'expected' => [
                    'licence' => SectionableInterface::SECTION_COMPLETION_COMPLETED,
                    'permitsRequired' => SectionableInterface::SECTION_COMPLETION_NOT_STARTED,
                    'checkedAnswers' => SectionableInterface::SECTION_COMPLETION_CANNOT_START,
                    'declaration' => SectionableInterface::SECTION_COMPLETION_CANNOT_START,
                    'totalSections' => 4,
                    'totalCompleted' => 1,
                    'allCompleted' => false,
                ],
            ],
            'Multilateral - IRHP permit apps with one app without permits required set' => [
                'data' => [
                    'irhpPermitTypeId' => IrhpPermitType::IRHP_PERMIT_TYPE_ID_MULTILATERAL,
                    'licence' => $licence,
                    'irhpPermitApplications' => new ArrayCollection(
                        [
                            $irhpPermitAppWithPermits,
                            $irhpPermitAppWithoutPermits
                        ]
                    ),
                    'checkedAnswers' => false,
                    'declaration' => false,
                ],
                'expected' => [
                    'licence' => SectionableInterface::SECTION_COMPLETION_COMPLETED,
                    'permitsRequired' => SectionableInterface::SECTION_COMPLETION_NOT_STARTED,
                    'checkedAnswers' => SectionableInterface::SECTION_COMPLETION_CANNOT_START,
                    'declaration' => SectionableInterface::SECTION_COMPLETION_CANNOT_START,
                    'totalSections' => 4,
                    'totalCompleted' => 1,
                    'allCompleted' => false,
                ],
            ],
            'Multilateral - IRHP permit apps with all apps with permits required set' => [
                'data' => [
                    'irhpPermitTypeId' => IrhpPermitType::IRHP_PERMIT_TYPE_ID_MULTILATERAL,
                    'licence' => $licence,
                    'irhpPermitApplications' => new ArrayCollection(
                        [
                            $irhpPermitAppWithPermits,
                            $irhpPermitAppWithPermits
                        ]
                    ),
                    'checkedAnswers' => false,
                    'declaration' => false,
                ],
                'expected' => [
                    'licence' => SectionableInterface::SECTION_COMPLETION_COMPLETED,
                    'permitsRequired' => SectionableInterface::SECTION_COMPLETION_COMPLETED,
                    'checkedAnswers' => SectionableInterface::SECTION_COMPLETION_NOT_STARTED,
                    'declaration' => SectionableInterface::SECTION_COMPLETION_CANNOT_START,
                    'totalSections' => 4,
                    'totalCompleted' => 2,
                    'allCompleted' => false,
                ],
            ],
            'Multilateral - checked answers set' => [
                'data' => [
                    'irhpPermitTypeId' => IrhpPermitType::IRHP_PERMIT_TYPE_ID_MULTILATERAL,
                    'licence' => $licence,
                    'irhpPermitApplications' => new ArrayCollection(
                        [
                            $irhpPermitAppWithPermits,
                            $irhpPermitAppWithPermits
                        ]
                    ),
                    'checkedAnswers' => true,
                    'declaration' => false,
                ],
                'expected' => [
                    'licence' => SectionableInterface::SECTION_COMPLETION_COMPLETED,
                    'permitsRequired' => SectionableInterface::SECTION_COMPLETION_COMPLETED,
                    'checkedAnswers' => SectionableInterface::SECTION_COMPLETION_COMPLETED,
                    'declaration' => SectionableInterface::SECTION_COMPLETION_NOT_STARTED,
                    'totalSections' => 4,
                    'totalCompleted' => 3,
                    'allCompleted' => false,
                ],
            ],
            'Multilateral - declaration set' => [
                'data' => [
                    'irhpPermitTypeId' => IrhpPermitType::IRHP_PERMIT_TYPE_ID_MULTILATERAL,
                    'licence' => $licence,
                    'irhpPermitApplications' => new ArrayCollection(
                        [
                            $irhpPermitAppWithPermits,
                            $irhpPermitAppWithPermits
                        ]
                    ),
                    'checkedAnswers' => true,
                    'declaration' => true,
                ],
                'expected' => [
                    'licence' => SectionableInterface::SECTION_COMPLETION_COMPLETED,
                    'permitsRequired' => SectionableInterface::SECTION_COMPLETION_COMPLETED,
                    'checkedAnswers' => SectionableInterface::SECTION_COMPLETION_COMPLETED,
                    'declaration' => SectionableInterface::SECTION_COMPLETION_COMPLETED,
                    'totalSections' => 4,
                    'totalCompleted' => 4,
                    'allCompleted' => true,
                ],
            ],
        ];
    }

    public function testGetSectionCompletionForUndefinedIrhpPermitType()
    {
        // undefined IRHP Permit Type id
        $irhpPermitTypeId = IrhpPermitType::IRHP_PERMIT_TYPE_ID_ECMT;

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage(
            'Missing required definition of sections for irhpPermitTypeId: '.$irhpPermitTypeId
        );

        $irhpPermitType = m::mock(IrhpPermitType::class)->makePartial();
        $irhpPermitType->setId($irhpPermitTypeId);

        $this->sut->setIrhpPermitType($irhpPermitType);

        $this->sut->getSectionCompletion();
    }

    /**
     * @dataProvider dpCanCheckAnswersForApplicationPathEnabled
     */
    public function testCanCheckAnswersForApplicationPathEnabled(
        $irhpPermitTypeId,
        $status,
        $questionAnswerData,
        $expected
    ) {
        $irhpPermitType = new IrhpPermitType();
        $irhpPermitType->setId($irhpPermitTypeId);
        $this->sut->setIrhpPermitType($irhpPermitType);

        $this->sut->setStatus(new RefData($status));

        $this->sut->shouldReceive('getQuestionAnswerData')
            ->andReturn($questionAnswerData);

        $this->assertEquals($expected, $this->sut->canCheckAnswers());
    }

    public function dpCanCheckAnswersForApplicationPathEnabled()
    {
        return [
            'ECMT Removal - not yet submitted - check answers cannot start' => [
                'irhpPermitTypeId' => IrhpPermitType::IRHP_PERMIT_TYPE_ID_ECMT_REMOVAL,
                'status' => IrhpInterface::STATUS_NOT_YET_SUBMITTED,
                'questionAnswerData' => [
                    [
                        'section' => 'checkedAnswers',
                        'slug' => 'custom-check-answers',
                        'questionShort' => 'section.name.application/check-answers',
                        'question' => 'section.name.application/check-answers',
                        'answer' => null,
                        'status' => SectionableInterface::SECTION_COMPLETION_CANNOT_START,
                    ],
                ],
                'expected' => false,
            ],
            'ECMT Removal - not yet submitted - check answers not started' => [
                'irhpPermitTypeId' => IrhpPermitType::IRHP_PERMIT_TYPE_ID_ECMT_REMOVAL,
                'status' => IrhpInterface::STATUS_NOT_YET_SUBMITTED,
                'questionAnswerData' => [
                    [
                        'section' => 'checkedAnswers',
                        'slug' => 'custom-check-answers',
                        'questionShort' => 'section.name.application/check-answers',
                        'question' => 'section.name.application/check-answers',
                        'answer' => null,
                        'status' => SectionableInterface::SECTION_COMPLETION_NOT_STARTED,
                    ],
                ],
                'expected' => true,
            ],
            'ECMT Removal - not yet submitted - check answers completed' => [
                'irhpPermitTypeId' => IrhpPermitType::IRHP_PERMIT_TYPE_ID_ECMT_REMOVAL,
                'status' => IrhpInterface::STATUS_NOT_YET_SUBMITTED,
                'questionAnswerData' => [
                    [
                        'section' => 'checkedAnswers',
                        'slug' => 'custom-check-answers',
                        'questionShort' => 'section.name.application/check-answers',
                        'question' => 'section.name.application/check-answers',
                        'answer' => 1,
                        'status' => SectionableInterface::SECTION_COMPLETION_COMPLETED,
                    ],
                ],
                'expected' => true,
            ],
            'ECMT Removal - under consideration - check answers not started' => [
                'irhpPermitTypeId' => IrhpPermitType::IRHP_PERMIT_TYPE_ID_ECMT_REMOVAL,
                'status' => IrhpInterface::STATUS_UNDER_CONSIDERATION,
                'questionAnswerData' => [
                    [
                        'section' => 'checkedAnswers',
                        'slug' => 'custom-check-answers',
                        'questionShort' => 'section.name.application/check-answers',
                        'question' => 'section.name.application/check-answers',
                        'answer' => null,
                        'status' => SectionableInterface::SECTION_COMPLETION_NOT_STARTED,
                    ],
                ],
                'expected' => true,
            ],
            'ECMT Removal - withdrawn - check answers not started' => [
                'irhpPermitTypeId' => IrhpPermitType::IRHP_PERMIT_TYPE_ID_ECMT_REMOVAL,
                'status' => IrhpInterface::STATUS_WITHDRAWN,
                'questionAnswerData' => [
                    [
                        'section' => 'checkedAnswers',
                        'slug' => 'custom-check-answers',
                        'questionShort' => 'section.name.application/check-answers',
                        'question' => 'section.name.application/check-answers',
                        'answer' => null,
                        'status' => SectionableInterface::SECTION_COMPLETION_NOT_STARTED,
                    ],
                ],
                'expected' => false,
            ],
            'ECMT Removal - cancelled - check answers not started' => [
                'irhpPermitTypeId' => IrhpPermitType::IRHP_PERMIT_TYPE_ID_ECMT_REMOVAL,
                'status' => IrhpInterface::STATUS_CANCELLED,
                'questionAnswerData' => [
                    [
                        'section' => 'checkedAnswers',
                        'slug' => 'custom-check-answers',
                        'questionShort' => 'section.name.application/check-answers',
                        'question' => 'section.name.application/check-answers',
                        'answer' => null,
                        'status' => SectionableInterface::SECTION_COMPLETION_NOT_STARTED,
                    ],
                ],
                'expected' => false,
            ],
            'ECMT Short Term - not yet submitted - check answers cannot start' => [
                'irhpPermitTypeId' => IrhpPermitType::IRHP_PERMIT_TYPE_ID_ECMT_SHORT_TERM,
                'status' => IrhpInterface::STATUS_NOT_YET_SUBMITTED,
                'questionAnswerData' => [
                    [
                        'section' => 'checkedAnswers',
                        'slug' => 'custom-check-answers',
                        'questionShort' => 'section.name.application/check-answers',
                        'question' => 'section.name.application/check-answers',
                        'answer' => null,
                        'status' => SectionableInterface::SECTION_COMPLETION_CANNOT_START,
                    ],
                ],
                'expected' => false,
            ],
            'ECMT Short Term - not yet submitted - check answers not started' => [
                'irhpPermitTypeId' => IrhpPermitType::IRHP_PERMIT_TYPE_ID_ECMT_SHORT_TERM,
                'status' => IrhpInterface::STATUS_NOT_YET_SUBMITTED,
                'questionAnswerData' => [
                    [
                        'section' => 'checkedAnswers',
                        'slug' => 'custom-check-answers',
                        'questionShort' => 'section.name.application/check-answers',
                        'question' => 'section.name.application/check-answers',
                        'answer' => null,
                        'status' => SectionableInterface::SECTION_COMPLETION_NOT_STARTED,
                    ],
                ],
                'expected' => true,
            ],
            'ECMT Short Term - not yet submitted - check answers completed' => [
                'irhpPermitTypeId' => IrhpPermitType::IRHP_PERMIT_TYPE_ID_ECMT_SHORT_TERM,
                'status' => IrhpInterface::STATUS_NOT_YET_SUBMITTED,
                'questionAnswerData' => [
                    [
                        'section' => 'checkedAnswers',
                        'slug' => 'custom-check-answers',
                        'questionShort' => 'section.name.application/check-answers',
                        'question' => 'section.name.application/check-answers',
                        'answer' => 1,
                        'status' => SectionableInterface::SECTION_COMPLETION_COMPLETED,
                    ],
                ],
                'expected' => true,
            ],
            'ECMT Short Term - under consideration - check answers not started' => [
                'irhpPermitTypeId' => IrhpPermitType::IRHP_PERMIT_TYPE_ID_ECMT_SHORT_TERM,
                'status' => IrhpInterface::STATUS_UNDER_CONSIDERATION,
                'questionAnswerData' => [
                    [
                        'section' => 'checkedAnswers',
                        'slug' => 'custom-check-answers',
                        'questionShort' => 'section.name.application/check-answers',
                        'question' => 'section.name.application/check-answers',
                        'answer' => null,
                        'status' => SectionableInterface::SECTION_COMPLETION_NOT_STARTED,
                    ],
                ],
                'expected' => true,
            ],
            'ECMT Short Term - withdrawn - check answers not started' => [
                'irhpPermitTypeId' => IrhpPermitType::IRHP_PERMIT_TYPE_ID_ECMT_SHORT_TERM,
                'status' => IrhpInterface::STATUS_WITHDRAWN,
                'questionAnswerData' => [
                    [
                        'section' => 'checkedAnswers',
                        'slug' => 'custom-check-answers',
                        'questionShort' => 'section.name.application/check-answers',
                        'question' => 'section.name.application/check-answers',
                        'answer' => null,
                        'status' => SectionableInterface::SECTION_COMPLETION_NOT_STARTED,
                    ],
                ],
                'expected' => false,
            ],
            'ECMT Short Term - cancelled - check answers not started' => [
                'irhpPermitTypeId' => IrhpPermitType::IRHP_PERMIT_TYPE_ID_ECMT_SHORT_TERM,
                'status' => IrhpInterface::STATUS_CANCELLED,
                'questionAnswerData' => [
                    [
                        'section' => 'checkedAnswers',
                        'slug' => 'custom-check-answers',
                        'questionShort' => 'section.name.application/check-answers',
                        'question' => 'section.name.application/check-answers',
                        'answer' => null,
                        'status' => SectionableInterface::SECTION_COMPLETION_NOT_STARTED,
                    ],
                ],
                'expected' => false,
            ],
        ];
    }

    /**
     * @dataProvider dpCanCheckAnswersForNonApplicationPathEnabled
     */
    public function testCanCheckAnswersForNonApplicationPathEnabled(
        $irhpPermitTypeId,
        $status,
        $permitsRequired,
        $expected
    ) {
        $this->sut->setStatus(new RefData($status));

        $irhpPermitType = new IrhpPermitType();
        $irhpPermitType->setId($irhpPermitTypeId);
        $this->sut->setIrhpPermitType($irhpPermitType);

        $licence = m::mock(Licence::class);
        $this->sut->setLicence($licence);

        $irhpPermitApp = m::mock(IrhpPermitApplication::class)->makePartial();
        $irhpPermitApp->setPermitsRequired($permitsRequired);

        $this->sut->setIrhpPermitApplications(
            new ArrayCollection([$irhpPermitApp])
        );

        $this->assertEquals($expected, $this->sut->canCheckAnswers());
    }

    public function dpCanCheckAnswersForNonApplicationPathEnabled()
    {
        return [
            'Bilateral - not yet submitted - permits required set' => [
                'irhpPermitTypeId' => IrhpPermitType::IRHP_PERMIT_TYPE_ID_BILATERAL,
                'status' => IrhpInterface::STATUS_NOT_YET_SUBMITTED,
                'permitsRequired' => 10,
                'expected' => true,
            ],
            'Bilateral - not yet submitted - permits required not set' => [
                'irhpPermitTypeId' => IrhpPermitType::IRHP_PERMIT_TYPE_ID_BILATERAL,
                'status' => IrhpInterface::STATUS_NOT_YET_SUBMITTED,
                'permitsRequired' => null,
                'expected' => false,
            ],
            'Bilateral - under consideration - permits required set' => [
                'irhpPermitTypeId' => IrhpPermitType::IRHP_PERMIT_TYPE_ID_BILATERAL,
                'status' => IrhpInterface::STATUS_UNDER_CONSIDERATION,
                'permitsRequired' => 10,
                'expected' => true,
            ],
            'Bilateral - withdrawn - permits required set' => [
                'irhpPermitTypeId' => IrhpPermitType::IRHP_PERMIT_TYPE_ID_BILATERAL,
                'status' => IrhpInterface::STATUS_WITHDRAWN,
                'permitsRequired' => 10,
                'expected' => false,
            ],
            'Bilateral - cancelled - permits required set' => [
                'irhpPermitTypeId' => IrhpPermitType::IRHP_PERMIT_TYPE_ID_BILATERAL,
                'status' => IrhpInterface::STATUS_CANCELLED,
                'permitsRequired' => 10,
                'expected' => false,
            ],
            'Multilateral - not yet submitted - permits required set' => [
                'irhpPermitTypeId' => IrhpPermitType::IRHP_PERMIT_TYPE_ID_MULTILATERAL,
                'status' => IrhpInterface::STATUS_NOT_YET_SUBMITTED,
                'permitsRequired' => 10,
                'expected' => true,
            ],
            'Multilateral - not yet submitted - permits required not set' => [
                'irhpPermitTypeId' => IrhpPermitType::IRHP_PERMIT_TYPE_ID_MULTILATERAL,
                'status' => IrhpInterface::STATUS_NOT_YET_SUBMITTED,
                'permitsRequired' => null,
                'expected' => false,
            ],
            'Multilateral - under consideration - permits required set' => [
                'irhpPermitTypeId' => IrhpPermitType::IRHP_PERMIT_TYPE_ID_MULTILATERAL,
                'status' => IrhpInterface::STATUS_UNDER_CONSIDERATION,
                'permitsRequired' => 10,
                'expected' => true,
            ],
            'Multilateral - withdrawn - permits required set' => [
                'irhpPermitTypeId' => IrhpPermitType::IRHP_PERMIT_TYPE_ID_MULTILATERAL,
                'status' => IrhpInterface::STATUS_WITHDRAWN,
                'permitsRequired' => 10,
                'expected' => false,
            ],
            'Multilateral - cancelled - permits required set' => [
                'irhpPermitTypeId' => IrhpPermitType::IRHP_PERMIT_TYPE_ID_MULTILATERAL,
                'status' => IrhpInterface::STATUS_CANCELLED,
                'permitsRequired' => 10,
                'expected' => false,
            ],
        ];
    }

    public function testUpdateCheckAnswers()
    {
        $irhpApplication = m::mock(Entity::class)->makePartial();
        $irhpApplication->shouldReceive('canCheckAnswers')
            ->once()
            ->andReturn(true);

        $irhpApplication->setCheckedAnswers(false);
        $irhpApplication->updateCheckAnswers();
        $this->assertTrue($irhpApplication->getCheckedAnswers());
    }

    public function testUpdateCheckAnswersException()
    {
        $this->expectException(ForbiddenException::class);
        $this->expectExceptionMessage(Entity::ERR_CANT_CHECK_ANSWERS);

        $irhpApplication = m::mock(Entity::class)->makePartial();
        $irhpApplication->shouldReceive('canCheckAnswers')
            ->once()
            ->andReturn(false);

        $irhpApplication->updateCheckAnswers();
    }

    public function testResetCheckAnswersAndDeclarationSuccess()
    {
        $irhpApplication = m::mock(Entity::class)->makePartial();
        $irhpApplication->shouldReceive('canBeUpdated')
            ->andReturn(true);

        $irhpApplication->setDeclaration(true);
        $irhpApplication->setCheckedAnswers(true);

        $irhpApplication->resetCheckAnswersAndDeclaration();
        $this->assertFalse($irhpApplication->getDeclaration());
        $this->assertFalse($irhpApplication->getCheckedAnswers());
    }

    public function testResetCheckAnswersAndDeclarationFail()
    {
        $irhpApplication = m::mock(Entity::class)->makePartial();
        $irhpApplication->shouldReceive('canBeUpdated')
            ->andReturn(false);

        $irhpApplication->setDeclaration(true);
        $irhpApplication->setCheckedAnswers(true);

        $irhpApplication->resetCheckAnswersAndDeclaration();
        $this->assertTrue($irhpApplication->getDeclaration());
        $this->assertTrue($irhpApplication->getCheckedAnswers());
    }

    /**
     * @dataProvider dpCanMakeDeclarationForApplicationPathEnabled
     */
    public function testCanMakeDeclarationForApplicationPathEnabled(
        $irhpPermitTypeId,
        $status,
        $questionAnswerData,
        $expected
    ) {
        $irhpPermitType = new IrhpPermitType();
        $irhpPermitType->setId($irhpPermitTypeId);
        $this->sut->setIrhpPermitType($irhpPermitType);

        $this->sut->setStatus(new RefData($status));

        $this->sut->shouldReceive('getQuestionAnswerData')
            ->andReturn($questionAnswerData);

        $this->assertEquals($expected, $this->sut->canMakeDeclaration());
    }

    public function dpCanMakeDeclarationForApplicationPathEnabled()
    {
        return [
            'ECMT Removal - not yet submitted - declaration cannot start' => [
                'irhpPermitTypeId' => IrhpPermitType::IRHP_PERMIT_TYPE_ID_ECMT_REMOVAL,
                'status' => IrhpInterface::STATUS_NOT_YET_SUBMITTED,
                'questionAnswerData' => [
                    [
                        'section' => 'declaration',
                        'slug' => 'custom-declaration',
                        'questionShort' => 'section.name.application/declaration',
                        'question' => 'section.name.application/declaration',
                        'answer' => null,
                        'status' => SectionableInterface::SECTION_COMPLETION_CANNOT_START,
                    ],
                ],
                'expected' => false,
            ],
            'ECMT Removal - not yet submitted - declaration not started' => [
                'irhpPermitTypeId' => IrhpPermitType::IRHP_PERMIT_TYPE_ID_ECMT_REMOVAL,
                'status' => IrhpInterface::STATUS_NOT_YET_SUBMITTED,
                'questionAnswerData' => [
                    [
                        'section' => 'declaration',
                        'slug' => 'custom-declaration',
                        'questionShort' => 'section.name.application/declaration',
                        'question' => 'section.name.application/declaration',
                        'answer' => null,
                        'status' => SectionableInterface::SECTION_COMPLETION_NOT_STARTED,
                    ],
                ],
                'expected' => true,
            ],
            'ECMT Removal - not yet submitted - declaration completed' => [
                'irhpPermitTypeId' => IrhpPermitType::IRHP_PERMIT_TYPE_ID_ECMT_REMOVAL,
                'status' => IrhpInterface::STATUS_NOT_YET_SUBMITTED,
                'questionAnswerData' => [
                    [
                        'section' => 'declaration',
                        'slug' => 'custom-declaration',
                        'questionShort' => 'section.name.application/declaration',
                        'question' => 'section.name.application/declaration',
                        'answer' => 1,
                        'status' => SectionableInterface::SECTION_COMPLETION_COMPLETED,
                    ],
                ],
                'expected' => true,
            ],
            'ECMT Removal - under consideration - declaration not started' => [
                'irhpPermitTypeId' => IrhpPermitType::IRHP_PERMIT_TYPE_ID_ECMT_REMOVAL,
                'status' => IrhpInterface::STATUS_UNDER_CONSIDERATION,
                'questionAnswerData' => [
                    [
                        'section' => 'declaration',
                        'slug' => 'custom-declaration',
                        'questionShort' => 'section.name.application/declaration',
                        'question' => 'section.name.application/declaration',
                        'answer' => null,
                        'status' => SectionableInterface::SECTION_COMPLETION_NOT_STARTED,
                    ],
                ],
                'expected' => true,
            ],
            'ECMT Removal - withdrawn - declaration not started' => [
                'irhpPermitTypeId' => IrhpPermitType::IRHP_PERMIT_TYPE_ID_ECMT_REMOVAL,
                'status' => IrhpInterface::STATUS_WITHDRAWN,
                'questionAnswerData' => [
                    [
                        'section' => 'declaration',
                        'slug' => 'custom-declaration',
                        'questionShort' => 'section.name.application/declaration',
                        'question' => 'section.name.application/declaration',
                        'answer' => null,
                        'status' => SectionableInterface::SECTION_COMPLETION_NOT_STARTED,
                    ],
                ],
                'expected' => false,
            ],
            'ECMT Removal - cancelled - declaration not started' => [
                'irhpPermitTypeId' => IrhpPermitType::IRHP_PERMIT_TYPE_ID_ECMT_REMOVAL,
                'status' => IrhpInterface::STATUS_CANCELLED,
                'questionAnswerData' => [
                    [
                        'section' => 'declaration',
                        'slug' => 'custom-declaration',
                        'questionShort' => 'section.name.application/declaration',
                        'question' => 'section.name.application/declaration',
                        'answer' => null,
                        'status' => SectionableInterface::SECTION_COMPLETION_NOT_STARTED,
                    ],
                ],
                'expected' => false,
            ],
        ];
    }

    /**
     * @dataProvider dpCanMakeDeclarationForNonApplicationPathEnabled
     */
    public function testCanMakeDeclarationForNonApplicationPathEnabled(
        $irhpPermitTypeId,
        $status,
        $permitsRequired,
        $checkedAnswers,
        $expected
    ) {
        $this->sut->setStatus(new RefData($status));

        $irhpPermitType = new IrhpPermitType();
        $irhpPermitType->setId($irhpPermitTypeId);
        $this->sut->setIrhpPermitType($irhpPermitType);

        $licence = m::mock(Licence::class);
        $this->sut->setLicence($licence);

        $irhpPermitApp = m::mock(IrhpPermitApplication::class)->makePartial();
        $irhpPermitApp->setPermitsRequired($permitsRequired);

        $this->sut->setIrhpPermitApplications(
            new ArrayCollection([$irhpPermitApp])
        );
        $this->sut->setCheckedAnswers($checkedAnswers);

        $this->assertEquals($expected, $this->sut->canMakeDeclaration());
    }

    public function dpCanMakeDeclarationForNonApplicationPathEnabled()
    {
        return [
            'Bilateral - not yet submitted - permits required set - answers checked' => [
                'irhpPermitTypeId' => IrhpPermitType::IRHP_PERMIT_TYPE_ID_BILATERAL,
                'status' => IrhpInterface::STATUS_NOT_YET_SUBMITTED,
                'permitsRequired' => 10,
                'checkedAnswers' => true,
                'expected' => true,
            ],
            'Bilateral - not yet submitted - permits required set - answers not checked' => [
                'irhpPermitTypeId' => IrhpPermitType::IRHP_PERMIT_TYPE_ID_BILATERAL,
                'status' => IrhpInterface::STATUS_NOT_YET_SUBMITTED,
                'permitsRequired' => 10,
                'checkedAnswers' => null,
                'expected' => false,
            ],
            'Bilateral - not yet submitted - permits required not set - answers not checked' => [
                'irhpPermitTypeId' => IrhpPermitType::IRHP_PERMIT_TYPE_ID_BILATERAL,
                'status' => IrhpInterface::STATUS_NOT_YET_SUBMITTED,
                'permitsRequired' => null,
                'checkedAnswers' => null,
                'expected' => false,
            ],
            'Bilateral - under consideration - permits required set - answers checked' => [
                'irhpPermitTypeId' => IrhpPermitType::IRHP_PERMIT_TYPE_ID_BILATERAL,
                'status' => IrhpInterface::STATUS_UNDER_CONSIDERATION,
                'permitsRequired' => 10,
                'checkedAnswers' => true,
                'expected' => true,
            ],
            'Bilateral - withdrawn - permits required set - answers checked' => [
                'irhpPermitTypeId' => IrhpPermitType::IRHP_PERMIT_TYPE_ID_BILATERAL,
                'status' => IrhpInterface::STATUS_WITHDRAWN,
                'permitsRequired' => 10,
                'checkedAnswers' => true,
                'expected' => false,
            ],
            'Bilateral - cancelled - permits required set - answers checked' => [
                'irhpPermitTypeId' => IrhpPermitType::IRHP_PERMIT_TYPE_ID_BILATERAL,
                'status' => IrhpInterface::STATUS_CANCELLED,
                'permitsRequired' => 10,
                'checkedAnswers' => true,
                'expected' => false,
            ],
            'Multilateral - not yet submitted - permits required set - answers checked' => [
                'irhpPermitTypeId' => IrhpPermitType::IRHP_PERMIT_TYPE_ID_MULTILATERAL,
                'status' => IrhpInterface::STATUS_NOT_YET_SUBMITTED,
                'permitsRequired' => 10,
                'checkedAnswers' => true,
                'expected' => true,
            ],
            'Multilateral - not yet submitted - permits required set - answers not checked' => [
                'irhpPermitTypeId' => IrhpPermitType::IRHP_PERMIT_TYPE_ID_MULTILATERAL,
                'status' => IrhpInterface::STATUS_NOT_YET_SUBMITTED,
                'permitsRequired' => 10,
                'checkedAnswers' => null,
                'expected' => false,
            ],
            'Multilateral - not yet submitted - permits required not set - answers not checked' => [
                'irhpPermitTypeId' => IrhpPermitType::IRHP_PERMIT_TYPE_ID_MULTILATERAL,
                'status' => IrhpInterface::STATUS_NOT_YET_SUBMITTED,
                'permitsRequired' => null,
                'checkedAnswers' => null,
                'expected' => false,
            ],
            'Multilateral - under consideration - permits required set - answers checked' => [
                'irhpPermitTypeId' => IrhpPermitType::IRHP_PERMIT_TYPE_ID_MULTILATERAL,
                'status' => IrhpInterface::STATUS_UNDER_CONSIDERATION,
                'permitsRequired' => 10,
                'checkedAnswers' => true,
                'expected' => true,
            ],
            'Multilateral - withdrawn - permits required set - answers checked' => [
                'irhpPermitTypeId' => IrhpPermitType::IRHP_PERMIT_TYPE_ID_MULTILATERAL,
                'status' => IrhpInterface::STATUS_WITHDRAWN,
                'permitsRequired' => 10,
                'checkedAnswers' => true,
                'expected' => false,
            ],
            'Multilateral - cancelled - permits required set - answers checked' => [
                'irhpPermitTypeId' => IrhpPermitType::IRHP_PERMIT_TYPE_ID_MULTILATERAL,
                'status' => IrhpInterface::STATUS_CANCELLED,
                'permitsRequired' => 10,
                'checkedAnswers' => true,
                'expected' => false,
            ],
        ];
    }

    public function testMakeDeclaration()
    {
        $irhpApplication = m::mock(Entity::class)->makePartial();
        $irhpApplication->shouldReceive('canMakeDeclaration')
            ->andReturn(true);

        $irhpApplication->makeDeclaration();

        $this->assertTrue($irhpApplication->getDeclaration());
    }

    public function testMakeDeclarationFail()
    {
        $this->expectException(ForbiddenException::class);
        $this->expectExceptionMessage(Entity::ERR_CANT_MAKE_DECLARATION);

        $irhpApplication = m::mock(Entity::class)->makePartial();
        $irhpApplication->shouldReceive('canMakeDeclaration')
            ->andReturn(false);

        $irhpApplication->makeDeclaration();
    }

    /**
     * @dataProvider dptestGetPermitsRequired
     */
    public function testGetPermitsRequired($irhpPermitApplications, $expected)
    {
        $irhpApplication = m::mock(Entity::class)->makePartial();

        $irhpApplication->setIrhpPermitApplications(
            new ArrayCollection($irhpPermitApplications)
        );

        $this->assertSame($expected, $irhpApplication->getPermitsRequired());
    }

    public function dpTestGetPermitsRequired()
    {
        $irhpPermitAppWithoutPermits = m::mock(IrhpPermitApplication::class)->makePartial();

        $irhpPermitAppWithPermits = m::mock(IrhpPermitApplication::class)->makePartial();
        $irhpPermitAppWithPermits->setPermitsRequired(10);

        return [
            'One Irhp Permit Application, 0 permits required' => [
                [$irhpPermitAppWithoutPermits],
                0
            ],
            'One Irhp Permit Application, 10 permits required' => [
                [$irhpPermitAppWithPermits],
                10
            ],
            'Two Irhp Permit Applications, 10 permits required on one and 0 on the other' => [
                [$irhpPermitAppWithPermits, $irhpPermitAppWithoutPermits],
                10
            ],
            'Two Irhp Permit Applications, 10 permits required on both' => [
                [$irhpPermitAppWithPermits, $irhpPermitAppWithPermits],
                20
            ]
        ];
    }

    public function testCanCreateOrReplaceIssueFeeTrue()
    {
        $irhpApplication = m::mock(Entity::class)->makePartial();
        $irhpApplication->shouldReceive('isNotYetSubmitted')
            ->andReturn(true);

        $this->assertTrue($irhpApplication->canCreateOrReplaceIssueFee());
    }

    public function testCanCreateOrReplaceIssueFeeFalse()
    {
        $irhpApplication = m::mock(Entity::class)->makePartial();
        $irhpApplication->shouldReceive('isNotYetSubmitted')
            ->andReturn(false);

        $this->assertFalse($irhpApplication->canCreateOrReplaceIssueFee());
    }

    public function testCanCreateOrReplaceApplicationFeeTrue()
    {
        $irhpApplication = m::mock(Entity::class)->makePartial();
        $irhpApplication->shouldReceive('isNotYetSubmitted')
            ->andReturn(true);

        $this->assertTrue($irhpApplication->canCreateOrReplaceApplicationFee());
    }

    public function testCanCreateOrReplaceApplicationFeeFalse()
    {
        $irhpApplication = m::mock(Entity::class)->makePartial();
        $irhpApplication->shouldReceive('isNotYetSubmitted')
            ->andReturn(false);

        $this->assertFalse($irhpApplication->canCreateOrReplaceApplicationFee());
    }

    public function testHaveFeesRequiredChangedExceptionWhenNothingStored()
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('storeFeesRequired must be called before haveFeesRequiredChanged');

        $irhpApplication = new Entity();
        $irhpApplication->haveFeesRequiredChanged();
    }

    /**
     * @dataProvider dpHaveFeesRequiredChanged
     */
    public function testHaveFeesRequiredChanged(
        $irhpPermitType,
        $stock1QuantityBefore,
        $stock2QuantityBefore,
        $stock1QuantityAfter,
        $stock2QuantityAfter,
        $expected
    ) {
        $irhpApplication = m::mock(Entity::class)->makePartial();
        $irhpApplication->shouldReceive('getIrhpPermitType->getId')
            ->andReturn($irhpPermitType);

        $irhpPermitApplication1 = m::mock(IrhpPermitApplication::class);
        $irhpPermitApplication1->shouldReceive('getIssueFeeProductReference')
            ->andReturn('BILATERAL_ISSUE_FEE_PRODUCT_REFERENCE');
        $irhpPermitApplication1->shouldReceive('getPermitsRequired')
            ->andReturn($stock1QuantityBefore);

        $irhpPermitApplication2 = m::mock(IrhpPermitApplication::class);
        $irhpPermitApplication2->shouldReceive('getIssueFeeProductReference')
            ->andReturn('BILATERAL_ISSUE_FEE_PRODUCT_REFERENCE');
        $irhpPermitApplication2->shouldReceive('getPermitsRequired')
            ->andReturn($stock2QuantityBefore);

        $irhpApplication->setIrhpPermitApplications(
            new ArrayCollection([$irhpPermitApplication1, $irhpPermitApplication2])
        );

        $irhpApplication->storeFeesRequired();

        $updatedIrhpPermitApplication1 = m::mock(IrhpPermitApplication::class);
        $updatedIrhpPermitApplication1->shouldReceive('getIssueFeeProductReference')
            ->andReturn('BILATERAL_ISSUE_FEE_PRODUCT_REFERENCE');
        $updatedIrhpPermitApplication1->shouldReceive('getPermitsRequired')
            ->andReturn($stock1QuantityAfter);

        $updatedIrhpPermitApplication2 = m::mock(IrhpPermitApplication::class);
        $updatedIrhpPermitApplication2->shouldReceive('getIssueFeeProductReference')
            ->andReturn('BILATERAL_ISSUE_FEE_PRODUCT_REFERENCE');
        $updatedIrhpPermitApplication2->shouldReceive('getPermitsRequired')
            ->andReturn($stock2QuantityAfter);

        $irhpApplication->setIrhpPermitApplications(
            new ArrayCollection([$updatedIrhpPermitApplication1, $updatedIrhpPermitApplication2])
        );

        $this->assertEquals($expected, $irhpApplication->haveFeesRequiredChanged());
    }

    public function dpHaveFeesRequiredChanged()
    {
        return [
            [IrhpPermitType::IRHP_PERMIT_TYPE_ID_BILATERAL, 7, 11, 7, 11, false],
            [IrhpPermitType::IRHP_PERMIT_TYPE_ID_BILATERAL, 7, 11, 9, 9, false],
            [IrhpPermitType::IRHP_PERMIT_TYPE_ID_BILATERAL, 7, 11, 9, 13, true],
            [IrhpPermitType::IRHP_PERMIT_TYPE_ID_BILATERAL, null, null, 9, 13, true],
            [IrhpPermitType::IRHP_PERMIT_TYPE_ID_MULTILATERAL, 7, 11, 7, 11, false],
            [IrhpPermitType::IRHP_PERMIT_TYPE_ID_MULTILATERAL, 7, 11, 9, 9, false],
            [IrhpPermitType::IRHP_PERMIT_TYPE_ID_MULTILATERAL, 7, 11, 9, 13, true],
            [IrhpPermitType::IRHP_PERMIT_TYPE_ID_MULTILATERAL, null, null, 9, 13, true],
        ];
    }

    public function testGetApplicationFeeProductRefsAndQuantities()
    {
        $productReference = 'PRODUCT_REFERENCE';
        $permitsRequired = 7;

        $irhpApplication = m::mock(Entity::class)->makePartial();
        $irhpApplication->shouldReceive('getApplicationFeeProductReference')
            ->andReturn($productReference);
        $irhpApplication->shouldReceive('getPermitsRequired')
            ->andReturn($permitsRequired);

        $this->assertEquals(
            [$productReference => $permitsRequired],
            $irhpApplication->getApplicationFeeProductRefsAndQuantities()
        );
    }

    /**
     * @dataProvider dpGetApplicationFeeProductReference
     */
    public function testGetApplicationFeeProductReference($irhpPermitTypeId, $productReference)
    {
        $irhpApplication = m::mock(Entity::class)->makePartial();
        $irhpApplication->shouldReceive('getIrhpPermitType->getId')
            ->andReturn($irhpPermitTypeId);

        $this->assertEquals(
            $productReference,
            $irhpApplication->getApplicationFeeProductReference()
        );
    }

    public function dpGetApplicationFeeProductReference()
    {
        return [
            [
                IrhpPermitType::IRHP_PERMIT_TYPE_ID_BILATERAL,
                FeeType::FEE_TYPE_IRHP_APP_BILATERAL_PRODUCT_REF
            ],
            [
                IrhpPermitType::IRHP_PERMIT_TYPE_ID_MULTILATERAL,
                FeeType::FEE_TYPE_IRHP_APP_MULTILATERAL_PRODUCT_REF
            ],
            [
                IrhpPermitType::IRHP_PERMIT_TYPE_ID_ECMT_SHORT_TERM,
                FeeType::FEE_TYPE_ECMT_APP_PRODUCT_REF
            ],
        ];
    }

    public function testGetApplicationFeeProductReferenceUnsupportedType()
    {
        $this->expectException(ForbiddenException::class);
        $this->expectExceptionMessage(
            'No application fee product reference available for permit type 7'
        );

        $irhpApplication = m::mock(Entity::class)->makePartial();
        $irhpApplication->shouldReceive('getIrhpPermitType->getId')
            ->andReturn(7);

        $irhpApplication->getApplicationFeeProductReference();
    }

    public function testGetIssueFeeProductReference()
    {
        $irhpApplication = m::mock(Entity::class)->makePartial();
        $irhpApplication->shouldReceive('getIrhpPermitType->getId')
            ->andReturn(IrhpPermitType::IRHP_PERMIT_TYPE_ID_ECMT_SHORT_TERM);

        $this->assertEquals(
            FeeType::FEE_TYPE_ECMT_SHORT_TERM_ISSUE_PRODUCT_REF,
            $irhpApplication->getIssueFeeProductReference()
        );
    }

    /**
     * @dataProvider dpGetIssueFeeProductReferenceUnsupportedType
     */
    public function testGetIssueFeeProductReferenceUnsupportedType($irhpPermitTypeId)
    {
        $this->expectException(ForbiddenException::class);
        $this->expectExceptionMessage(
            'No issue fee product reference available for permit type ' . $irhpPermitTypeId
        );

        $irhpApplication = m::mock(Entity::class)->makePartial();
        $irhpApplication->shouldReceive('getIrhpPermitType->getId')
            ->andReturn($irhpPermitTypeId);

        $irhpApplication->getIssueFeeProductReference();
    }

    public function dpGetIssueFeeProductReferenceUnsupportedType()
    {
        return [
            [IrhpPermitType::IRHP_PERMIT_TYPE_ID_BILATERAL],
            [IrhpPermitType::IRHP_PERMIT_TYPE_ID_MULTILATERAL],
            [IrhpPermitType::IRHP_PERMIT_TYPE_ID_ECMT_REMOVAL],
        ];
    }

    public function testGetIssueFeeProductRefsAndQuantities()
    {
        $irhpPermitApplication1 = m::mock(IrhpPermitApplication::class);
        $irhpPermitApplication1->shouldReceive('getIssueFeeProductReference')
            ->andReturn('PRODUCT_REFERENCE_1');
        $irhpPermitApplication1->shouldReceive('getPermitsRequired')
            ->andReturn(7);

        $irhpPermitApplication2 = m::mock(IrhpPermitApplication::class);
        $irhpPermitApplication2->shouldReceive('getIssueFeeProductReference')
            ->andReturn('PRODUCT_REFERENCE_2');
        $irhpPermitApplication2->shouldReceive('getPermitsRequired')
            ->andReturn(3);

        $irhpPermitApplication3 = m::mock(IrhpPermitApplication::class);
        $irhpPermitApplication3->shouldReceive('getIssueFeeProductReference')
            ->andReturn('PRODUCT_REFERENCE_2');
        $irhpPermitApplication3->shouldReceive('getPermitsRequired')
            ->andReturn(0);

        $irhpPermitApplication4 = m::mock(IrhpPermitApplication::class);
        $irhpPermitApplication4->shouldReceive('getIssueFeeProductReference')
            ->andReturn('PRODUCT_REFERENCE_3');
        $irhpPermitApplication4->shouldReceive('getPermitsRequired')
            ->andReturn(0);

        $irhpPermitApplication5 = m::mock(IrhpPermitApplication::class);
        $irhpPermitApplication5->shouldReceive('getIssueFeeProductReference')
            ->andReturn('PRODUCT_REFERENCE_4');
        $irhpPermitApplication5->shouldReceive('getPermitsRequired')
            ->andReturn(5);

        $irhpPermitApplication6 = m::mock(IrhpPermitApplication::class);
        $irhpPermitApplication6->shouldReceive('getIssueFeeProductReference')
            ->andReturn('PRODUCT_REFERENCE_4');
        $irhpPermitApplication6->shouldReceive('getPermitsRequired')
            ->andReturn(6);

        $irhpApplication = m::mock(Entity::class)->makePartial();
        $irhpApplication->setIrhpPermitApplications(
            new ArrayCollection(
                [
                    $irhpPermitApplication1,
                    $irhpPermitApplication2,
                    $irhpPermitApplication3,
                    $irhpPermitApplication4,
                    $irhpPermitApplication5,
                    $irhpPermitApplication6
                ]
            )
        );

        $expected = [
            'PRODUCT_REFERENCE_1' => 7,
            'PRODUCT_REFERENCE_2' => 3,
            'PRODUCT_REFERENCE_4' => 11
        ];

        $this->assertEquals(
            $expected,
            $irhpApplication->getIssueFeeProductRefsAndQuantities()
        );
    }

    public function testUpdateDateReceived()
    {
        $irhpApplication = m::mock(Entity::class)->makePartial();
        $dateString = '2019-01-01';
        $irhpApplication->updateDateReceived('2019-01-01');
        $this->assertEquals(new DateTime($dateString), $irhpApplication->getDateReceived());
    }

    public function testClearAnswers()
    {
        $entity = m::mock(Entity::class)->makePartial();

        $this->assertFalse($entity->hasCheckedAnswers());
        $this->assertFalse($entity->hasMadeDeclaration());

        $entity->setCheckedAnswers(true);
        $entity->setDeclaration(true);

        $this->assertTrue($entity->hasCheckedAnswers());
        $this->assertTrue($entity->hasMadeDeclaration());

        $entity
            ->shouldReceive('canBeUpdated')
            ->andReturn(true);

        $entity->clearAnswers();

        $this->assertFalse($entity->hasCheckedAnswers());
        $this->assertFalse($entity->hasMadeDeclaration());
    }

    public function testUpdateLicence()
    {
        $entity = m::mock(Entity::class)->makePartial();

        $licenceA = m::mock(Licence::class);
        $entity->setLicence($licenceA);

        $this->assertEquals($licenceA, $entity->getLicence());

        $entity
            ->shouldReceive('canBeUpdated')
            ->andReturn(true);

        $licenceB = m::mock(Licence::class);
        $entity->updateLicence($licenceB);

        $this->assertEquals($licenceB, $entity->getLicence());
    }

    /**
     * @dataProvider dptestIsReadyForIssuing
     */
    public function testIsReadyForIssuing($hasOutstandingFees, $expectedResult)
    {
        $entity = m::mock(Entity::class)->makePartial();
        $entity->shouldReceive('hasOutstandingFees')
            ->andReturn($hasOutstandingFees);

        $this->assertEquals($expectedResult, $entity->isReadyForIssuing());
    }

    public function dpTestIsReadyForIssuing()
    {
        return [
            [false, true],
            [true, false],
        ];
    }

    public function testSubmit()
    {
        $status = m::mock(RefData::class);

        $entity = m::mock(Entity::class)->makePartial();
        $entity->shouldReceive('canBeSubmitted')
            ->andReturn(true);
        $entity->shouldReceive('proceedToIssuing')
            ->with($status)
            ->once();

        $entity->shouldReceive('getIrhpPermitType->getId')
            ->withNoArgs()
            ->once()
            ->andReturn(IrhpPermitType::IRHP_PERMIT_TYPE_ID_MULTILATERAL);

        $entity->submit($status);
    }

    public function testSubmitShortTerm()
    {
        $status = m::mock(RefData::class);

        $entity = m::mock(Entity::class)->makePartial();
        $entity->shouldReceive('canBeSubmitted')
            ->andReturn(true);
        $entity->shouldReceive('proceedToUnderConsideration')
            ->with($status)
            ->once();

        $entity->shouldReceive('getIrhpPermitType->getId')
            ->withNoArgs()
            ->once()
            ->andReturn(IrhpPermitType::IRHP_PERMIT_TYPE_ID_ECMT_SHORT_TERM);

        $entity->submit($status);
    }

    /**
     * @dataProvider dpSubmitCertOfRoadworthiness
     */
    public function testSubmitCertOfRoadworthiness($irhpPermitTypeId)
    {
        $status = m::mock(RefData::class);

        $entity = m::mock(Entity::class)->makePartial();
        $entity->shouldReceive('canBeSubmitted')
            ->andReturn(true);
        $entity->shouldReceive('proceedToValid')
            ->with($status)
            ->once();

        $entity->shouldReceive('getIrhpPermitType->getId')
            ->withNoArgs()
            ->once()
            ->andReturn($irhpPermitTypeId);

        $entity->submit($status);
    }

    public function dpSubmitCertOfRoadworthiness()
    {
        return [
            [IrhpPermitType::IRHP_PERMIT_TYPE_ID_CERT_ROADWORTHINESS_VEHICLE],
            [IrhpPermitType::IRHP_PERMIT_TYPE_ID_CERT_ROADWORTHINESS_TRAILER],
        ];
    }

    public function testSubmitException()
    {
        $this->expectException(ForbiddenException::class);
        $this->expectExceptionMessage(Entity::ERR_CANT_SUBMIT);

        $entity = m::mock(Entity::class)->makePartial();
        $entity->shouldReceive('canBeSubmitted')
            ->andReturn(false);
        $entity->shouldReceive('proceedToIssuing')
            ->never();

        $entity->submit(m::mock(RefData::class));
    }

    public function testProceedToIssuing()
    {
        $entity = m::mock(Entity::class)->makePartial();
        $entity->shouldReceive('isReadyForIssuing')
            ->andReturn(true);

        $status = m::mock(RefData::class);

        $entity->proceedToIssuing($status);
        $this->assertSame($status, $entity->getStatus());
    }

    public function testProceedToIssuingException()
    {
        $this->expectException(ForbiddenException::class);
        $this->expectExceptionMessage(Entity::ERR_CANT_ISSUE);

        $entity = m::mock(Entity::class)->makePartial();
        $entity->shouldReceive('isReadyForIssuing')
            ->andReturn(false);

        $entity->proceedToIssuing(m::mock(RefData::class));
    }

    public function testProceedToValid()
    {
        $entity = m::mock(Entity::class)->makePartial();
        $entity->shouldReceive('isIssueInProgress')
            ->andReturn(true);

        $status = m::mock(RefData::class);

        $entity->proceedToValid($status);
        $this->assertSame($status, $entity->getStatus());
    }

    public function testProceedToValidException()
    {
        $this->expectException(ForbiddenException::class);
        $this->expectExceptionMessage(
            sprintf(
                'This application is not in the correct state to proceed to valid (status: %s, irhpPermitType: %d)',
                IrhpInterface::STATUS_EXPIRED,
                IrhpPermitType::IRHP_PERMIT_TYPE_ID_CERT_ROADWORTHINESS_VEHICLE
            )
        );

        $oldStatus = m::mock(RefData::class);
        $oldStatus->shouldReceive('getId')
            ->andReturn(IrhpInterface::STATUS_EXPIRED);

        $irhpPermitType = m::mock(IrhpPermitType::class);
        $irhpPermitType->shouldReceive('getId')->withNoArgs()
            ->andReturn(IrhpPermitType::IRHP_PERMIT_TYPE_ID_CERT_ROADWORTHINESS_VEHICLE);

        $entity = m::mock(Entity::class)->makePartial();
        $entity->setStatus($oldStatus);
        $entity->setIrhpPermitType($irhpPermitType);
        $entity->shouldReceive('isIssueInProgress')
            ->andReturn(false)
            ->shouldReceive('isCertificateOfRoadworthiness')
            ->andReturn(false);

        $entity->proceedToValid(m::mock(RefData::class));
    }

    public function testGetQuestionAnswerBilateral()
    {
        $licNo = 'OB1234567';

        $country1 = 'country1';
        $country2 = 'country2';
        $countriesAnswer = [
            $country1 => $country1,
            $country2 => $country2,
        ];

        $irhpPermitType = m::mock(IrhpPermitType::class);
        $irhpPermitType->shouldReceive('isBilateral')->once()->withNoArgs()->andReturn(true);

        $stock1ValidityDate = new DateTime('2019-12-31');
        $stock2ValidityDate = new DateTime('2019-12-31');
        $stock3ValidityDate = new DateTime('2020-12-31');

        $stock1ValidityYear = 2019;
        $stock2ValidityYear = 2019;
        $stock3ValidityYear = 2020;

        $stock1RequiredPermits = 6;
        $stock2RequiredPermits = 4;
        $stock3RequiredPermits = 0;

        $stock1 = m::mock(IrhpPermitStock::class);
        $stock1->shouldReceive('getCountry->getCountryDesc')->once()->withNoArgs()->andReturn($country1);
        $stock1->shouldReceive('getValidTo')->once()->with(true)->andReturn($stock1ValidityDate);

        $stock2 = m::mock(IrhpPermitStock::class);
        $stock2->shouldReceive('getCountry->getCountryDesc')->once()->withNoArgs()->andReturn($country2);
        $stock2->shouldReceive('getValidTo')->once()->with(true)->andReturn($stock2ValidityDate);

        $stock3 = m::mock(IrhpPermitStock::class);
        $stock3->shouldReceive('getCountry->getCountryDesc')->once()->withNoArgs()->andReturn($country2);
        $stock3->shouldReceive('getValidTo')->once()->with(true)->andReturn($stock3ValidityDate);

        $irhpPermitApplication1 = m::mock(IrhpPermitApplication::class);
        $irhpPermitApplication1->shouldReceive('getPermitsRequired')
            ->twice()
            ->withNoArgs()
            ->andReturn($stock1RequiredPermits);
        $irhpPermitApplication1->shouldReceive('getIrhpPermitWindow->getIrhpPermitStock')
            ->once()
            ->withNoArgs()
            ->andReturn($stock1);

        $irhpPermitApplication2 = m::mock(IrhpPermitApplication::class);
        $irhpPermitApplication2->shouldReceive('getPermitsRequired')
            ->twice()
            ->withNoArgs()
            ->andReturn($stock2RequiredPermits);
        $irhpPermitApplication2->shouldReceive('getIrhpPermitWindow->getIrhpPermitStock')
            ->once()
            ->withNoArgs()
            ->andReturn($stock2);

        //this permit application entry has a zero for number of permits, but is included
        $irhpPermitApplication3 = m::mock(IrhpPermitApplication::class);
        $irhpPermitApplication3->shouldReceive('getPermitsRequired')
            ->twice()
            ->withNoArgs()
            ->andReturn($stock3RequiredPermits);
        $irhpPermitApplication3->shouldReceive('getIrhpPermitWindow->getIrhpPermitStock')
            ->once()
            ->withNoArgs()
            ->andReturn($stock3);

        //this permit application entry has a null entry for number of permits, so is ignored
        $irhpPermitApplication4 = m::mock(IrhpPermitApplication::class);
        $irhpPermitApplication4->shouldReceive('getPermitsRequired')->twice()->withNoArgs()->andReturn(null);
        $irhpPermitApplication4->shouldReceive('getIrhpPermitWindow->getIrhpPermitStock')->never();

        $permitApplications = [
            $irhpPermitApplication1,
            $irhpPermitApplication2,
            $irhpPermitApplication3,
            $irhpPermitApplication4,
        ];

        $licence = m::mock(Licence::class);
        $licence->shouldReceive('getLicNo')->once()->withNoArgs()->andReturn($licNo);

        $entity = $this->createNewEntity(null, null, $irhpPermitType, $licence);
        $entity->setIrhpPermitApplications(new ArrayCollection($permitApplications));

        $data = [
            [
                'question' => 'permits.check-answers.page.question.licence',
                'answer' =>  $licNo,
                'questionType' => Question::QUESTION_TYPE_STRING,
            ],
            [
                'question' => 'permits.irhp.countries.transporting',
                'answer' =>  $countriesAnswer,
                'questionType' => Question::QUESTION_TYPE_STRING,
            ],
            [
                'question' => 'permits.snapshot.number.required',
                'answer' =>  10,
                'questionType' => Question::QUESTION_TYPE_INTEGER,
            ],
            [
                'question' => $country1 . ' for ' . $stock1ValidityYear,
                'answer' =>  $stock1RequiredPermits,
                'questionType' => Question::QUESTION_TYPE_INTEGER,
            ],
            [
                'question' => $country2 . ' for ' . $stock2ValidityYear,
                'answer' =>  $stock2RequiredPermits,
                'questionType' => Question::QUESTION_TYPE_INTEGER,
            ],
            [
                'question' => $country2 . ' for ' . $stock3ValidityYear,
                'answer' =>  $stock3RequiredPermits,
                'questionType' => Question::QUESTION_TYPE_INTEGER,
            ],
        ];

        $this->assertEquals($data, $entity->getQuestionAnswerData());
    }

    public function testGetQuestionAnswerMultilateral()
    {
        $licNo = 'OB1234567';

        $irhpPermitType = m::mock(IrhpPermitType::class);
        $irhpPermitType->shouldReceive('isBilateral')->once()->withNoArgs()->andReturn(false);
        $irhpPermitType->shouldReceive('isMultilateral')->once()->withNoArgs()->andReturn(true);

        $stock1ValidityDate = new DateTime('2019-12-31');
        $stock2ValidityDate = new DateTime('2020-12-31');
        $stock3ValidityDate = new DateTime('2021-12-31');

        $stock1ValidityYear = 2019;
        $stock2ValidityYear = 2020;
        $stock3ValidityYear = 2021;

        $stock1RequiredPermits = 6;
        $stock2RequiredPermits = 4;
        $stock3RequiredPermits = 0;

        $stock1 = m::mock(IrhpPermitStock::class);
        $stock1->shouldReceive('getValidTo')->once()->with(true)->andReturn($stock1ValidityDate);

        $stock2 = m::mock(IrhpPermitStock::class);
        $stock2->shouldReceive('getValidTo')->once()->with(true)->andReturn($stock2ValidityDate);

        $stock3 = m::mock(IrhpPermitStock::class);
        $stock3->shouldReceive('getValidTo')->once()->with(true)->andReturn($stock3ValidityDate);

        $irhpPermitApplication1 = m::mock(IrhpPermitApplication::class);
        $irhpPermitApplication1->shouldReceive('getPermitsRequired')
            ->twice()
            ->withNoArgs()
            ->andReturn($stock1RequiredPermits);
        $irhpPermitApplication1->shouldReceive('getIrhpPermitWindow->getIrhpPermitStock')
            ->once()
            ->withNoArgs()
            ->andReturn($stock1);

        $irhpPermitApplication2 = m::mock(IrhpPermitApplication::class);
        $irhpPermitApplication2->shouldReceive('getPermitsRequired')
            ->twice()
            ->withNoArgs()
            ->andReturn($stock2RequiredPermits);
        $irhpPermitApplication2->shouldReceive('getIrhpPermitWindow->getIrhpPermitStock')
            ->once()
            ->withNoArgs()
            ->andReturn($stock2);

        //this permit application entry has a zero for number of permits, but is included
        $irhpPermitApplication3 = m::mock(IrhpPermitApplication::class);
        $irhpPermitApplication3->shouldReceive('getPermitsRequired')
            ->twice()
            ->withNoArgs()
            ->andReturn($stock3RequiredPermits);
        $irhpPermitApplication3->shouldReceive('getIrhpPermitWindow->getIrhpPermitStock')
            ->once()
            ->withNoArgs()
            ->andReturn($stock3);

        //this permit application entry has a null entry for number of permits, so is ignored
        $irhpPermitApplication4 = m::mock(IrhpPermitApplication::class);
        $irhpPermitApplication4->shouldReceive('getPermitsRequired')->twice()->withNoArgs()->andReturn(null);
        $irhpPermitApplication4->shouldReceive('getIrhpPermitWindow->getIrhpPermitStock')->never();

        $permitApplications = [
            $irhpPermitApplication1,
            $irhpPermitApplication2,
            $irhpPermitApplication3,
            $irhpPermitApplication4,
        ];

        $licence = m::mock(Licence::class);
        $licence->shouldReceive('getLicNo')->once()->withNoArgs()->andReturn($licNo);

        $entity = $this->createNewEntity(null, null, $irhpPermitType, $licence);
        $entity->setIrhpPermitApplications(new ArrayCollection($permitApplications));

        $data = [
            [
                'question' => 'permits.check-answers.page.question.licence',
                'answer' =>  $licNo,
                'questionType' => Question::QUESTION_TYPE_STRING,
            ],
            [
                'question' => 'permits.snapshot.number.required',
                'answer' =>  10,
                'questionType' => Question::QUESTION_TYPE_INTEGER,
            ],
            [
                'question' => 'For ' . $stock1ValidityYear,
                'answer' =>  $stock1RequiredPermits,
                'questionType' => Question::QUESTION_TYPE_INTEGER,
            ],
            [
                'question' => 'For ' . $stock2ValidityYear,
                'answer' =>  $stock2RequiredPermits,
                'questionType' => Question::QUESTION_TYPE_INTEGER,
            ],
            [
                'question' => 'For ' . $stock3ValidityYear,
                'answer' =>  $stock3RequiredPermits,
                'questionType' => Question::QUESTION_TYPE_INTEGER,
            ],
        ];

        $this->assertEquals($data, $entity->getQuestionAnswerData());
    }

    public function testGetQuestionAnswerDataWithoutActiveApplicationPath()
    {
        $licNo = 'ABC123';

        $expected = [
            'custom-licence' => [
                'section' => 'licence',
                'slug' => 'custom-licence',
                'questionShort' => 'section.name.application/licence',
                'question' => 'section.name.application/licence',
                'questionType' => Question::QUESTION_TYPE_STRING,
                'answer' => $licNo,
                'status' => SectionableInterface::SECTION_COMPLETION_COMPLETED,
            ],
            'custom-check-answers' => [
                'section' => 'checkedAnswers',
                'slug' => 'custom-check-answers',
                'questionShort' => 'section.name.application/check-answers',
                'question' => 'section.name.application/check-answers',
                'answer' => null,
                'status' => SectionableInterface::SECTION_COMPLETION_NOT_STARTED,
            ],
            'custom-declaration' => [
                'section' => 'declaration',
                'slug' => 'custom-declaration',
                'questionShort' => 'section.name.application/declaration',
                'question' => 'section.name.application/declaration',
                'answer' => null,
                'status' => SectionableInterface::SECTION_COMPLETION_CANNOT_START,
            ],
        ];

        $createdOn = new DateTime();

        $licence = m::mock(Licence::class);
        $licence->shouldReceive('getLicNo')->once()->withNoArgs()->andReturn($licNo);

        $irhpPermitApplication = m::mock(IrhpPermitApplication::class);
        $irhpPermitApplication->shouldReceive(
            'getIrhpPermitWindow->getIrhpPermitStock->getApplicationPathGroup->getActiveApplicationPath'
        )->once()->with($createdOn)->andReturn(null);

        $irhpPermitType = m::mock(IrhpPermitType::class);
        $irhpPermitType->shouldReceive('isBilateral')->once()->withNoArgs()->andReturn(false);
        $irhpPermitType->shouldReceive('isMultilateral')->once()->withNoArgs()->andReturn(false);

        $entity = $this->createNewEntity(null, null, $irhpPermitType, $licence);
        $entity->addIrhpPermitApplications($irhpPermitApplication);
        $entity->setCreatedOn($createdOn);

        $this->assertEquals($expected, $entity->getQuestionAnswerData());
    }

    /**
     * @dataProvider dpGetQuestionAnswerDataWithActiveApplicationPath
     */
    public function testGetQuestionAnswerDataWithActiveApplicationPath($data, $applicationSteps, $expected, $isSnapshot)
    {
        $licence = m::mock(Licence::class);
        $licence->shouldReceive('getLicNo')->once()->withNoArgs()->andReturn($data['licNo']);

        $applicationPath = m::mock(ApplicationPath::class);
        $applicationPath->shouldReceive('getApplicationSteps')->once()->withNoArgs()->andReturn($applicationSteps);

        $irhpPermitApplication = m::mock(IrhpPermitApplication::class);
        $irhpPermitApplication->shouldReceive(
            'getIrhpPermitWindow->getIrhpPermitStock->getApplicationPathGroup->getActiveApplicationPath'
        )->once()->with($data['createdOn'])->andReturn($applicationPath);

        $irhpPermitType = m::mock(IrhpPermitType::class);
        $irhpPermitType->shouldReceive('isBilateral')->once()->withNoArgs()->andReturn(false);
        $irhpPermitType->shouldReceive('isMultilateral')->once()->withNoArgs()->andReturn(false);

        $entity = $this->createNewEntity(null, null, $irhpPermitType, $licence);
        $entity->addIrhpPermitApplications($irhpPermitApplication);
        $entity->setAnswers($data['answers']);
        $entity->setCreatedOn($data['createdOn']);
        $entity->setCheckedAnswers($data['checkedAnswers']);
        $entity->setDeclaration($data['declaration']);

        $this->assertEquals($expected, $entity->getQuestionAnswerData($isSnapshot));
    }

    public function dpGetQuestionAnswerDataWithActiveApplicationPath()
    {
        $createdOn = new DateTime();

        // q1
        $question1TextId = 1;
        $question1Text = m::mock(QuestionText::class);
        $question1Text->shouldReceive('getId')->withNoArgs()->andReturn($question1TextId);
        $question1Text->shouldReceive('getQuestionShortKey')->withNoArgs()->andReturn('q1-short-key');
        $question1Text->shouldReceive('getQuestionKey')->withNoArgs()->andReturn('{"translateableText": {"key": "q1-key"}}');
        $question1Text->shouldReceive('getQuestion->getQuestionType->getId')->withNoArgs()->andReturn('q1-type');

        $question1 = m::mock(Question::class);
        $question1->shouldReceive('getQuestion')->withNoArgs()->andReturn($question1);
        $question1->shouldReceive('getActiveQuestionText')->with($createdOn)->andReturn($question1Text);
        $question1->shouldReceive('getSlug')->withNoArgs()->andReturn('q1-slug');
        $question1->shouldReceive('isCustom')->withNoArgs()->andReturn(false);

        $step1 = m::mock(ApplicationStep::class);
        $step1->shouldReceive('getQuestion')->withNoArgs()->andReturn($question1);

        $answer1 = m::mock(Answer::class);
        $answer1->shouldReceive('getValue')->withNoArgs()->andReturn('q1-answer');

        // q2
        $question2TextId = 2;
        $question2Text = m::mock(QuestionText::class);
        $question2Text->shouldReceive('getId')->withNoArgs()->andReturn($question2TextId);
        $question2Text->shouldReceive('getQuestionShortKey')->withNoArgs()->andReturn('q2-short-key');
        $question2Text->shouldReceive('getQuestionKey')->withNoArgs()->andReturn('{"translateableText": {"key": "q2-key"}}');
        $question2Text->shouldReceive('getQuestion->getQuestionType->getId')->withNoArgs()->andReturn('q2-type');

        $question2 = m::mock(Question::class);
        $question2->shouldReceive('getQuestion')->withNoArgs()->andReturn($question2);
        $question2->shouldReceive('getActiveQuestionText')->with($createdOn)->andReturn($question2Text);
        $question2->shouldReceive('getSlug')->withNoArgs()->andReturn('q2-slug');
        $question2->shouldReceive('isCustom')->withNoArgs()->andReturn(false);

        $step2 = m::mock(ApplicationStep::class);
        $step2->shouldReceive('getQuestion')->withNoArgs()->andReturn($question2);

        $answer2 = m::mock(Answer::class);
        $answer2->shouldReceive('getValue')->withNoArgs()->andReturn('q2-answer');

        return [
            'licence not set' => [
                'data' => [
                    'licNo' => '',
                    'answers' => new ArrayCollection([]),
                    'checkedAnswers' => 0,
                    'declaration' => 0,
                    'createdOn' => $createdOn,
                ],
                'applicationSteps' => new ArrayCollection([$step1, $step2]),
                'expected' => [
                    'custom-licence' => [
                        'section' => 'licence',
                        'slug' => 'custom-licence',
                        'questionShort' => 'section.name.application/licence',
                        'question' => 'section.name.application/licence',
                        'questionType' => Question::QUESTION_TYPE_STRING,
                        'answer' => '',
                        'status' => SectionableInterface::SECTION_COMPLETION_NOT_STARTED,
                    ],
                    'q1-slug' => [
                        'section' => 'q1-slug',
                        'slug' => 'q1-slug',
                        'questionShort' => 'q1-short-key',
                        'question' => 'q1-key',
                        'questionType' => 'q1-type',
                        'answer' => null,
                        'status' => SectionableInterface::SECTION_COMPLETION_CANNOT_START,
                    ],
                    'q2-slug' => [
                        'section' => 'q2-slug',
                        'slug' => 'q2-slug',
                        'questionShort' => 'q2-short-key',
                        'question' => 'q2-key',
                        'questionType' => 'q2-type',
                        'answer' => null,
                        'status' => SectionableInterface::SECTION_COMPLETION_CANNOT_START,
                    ],
                    'custom-check-answers' => [
                        'section' => 'checkedAnswers',
                        'slug' => 'custom-check-answers',
                        'questionShort' => 'section.name.application/check-answers',
                        'question' => 'section.name.application/check-answers',
                        'answer' => null,
                        'status' => SectionableInterface::SECTION_COMPLETION_CANNOT_START,
                    ],
                    'custom-declaration' => [
                        'section' => 'declaration',
                        'slug' => 'custom-declaration',
                        'questionShort' => 'section.name.application/declaration',
                        'question' => 'section.name.application/declaration',
                        'answer' => null,
                        'status' => SectionableInterface::SECTION_COMPLETION_CANNOT_START,
                    ],
                ],
                false,
            ],
            'licence set' => [
                'data' => [
                    'licNo' => 'OB1234567',
                    'answers' => new ArrayCollection([]),
                    'checkedAnswers' => 0,
                    'declaration' => 0,
                    'createdOn' => $createdOn,
                ],
                'applicationSteps' => new ArrayCollection([$step1, $step2]),
                'expected' => [
                    'custom-licence' => [
                        'section' => 'licence',
                        'slug' => 'custom-licence',
                        'questionShort' => 'section.name.application/licence',
                        'question' => 'section.name.application/licence',
                        'questionType' => Question::QUESTION_TYPE_STRING,
                        'answer' => 'OB1234567',
                        'status' => SectionableInterface::SECTION_COMPLETION_COMPLETED,
                    ],
                    'q1-slug' => [
                        'section' => 'q1-slug',
                        'slug' => 'q1-slug',
                        'questionShort' => 'q1-short-key',
                        'question' => 'q1-key',
                        'questionType' => 'q1-type',
                        'answer' => null,
                        'status' => SectionableInterface::SECTION_COMPLETION_NOT_STARTED,
                    ],
                    'q2-slug' => [
                        'section' => 'q2-slug',
                        'slug' => 'q2-slug',
                        'questionShort' => 'q2-short-key',
                        'question' => 'q2-key',
                        'questionType' => 'q2-type',
                        'answer' => null,
                        'status' => SectionableInterface::SECTION_COMPLETION_CANNOT_START,
                    ],
                    'custom-check-answers' => [
                        'section' => 'checkedAnswers',
                        'slug' => 'custom-check-answers',
                        'questionShort' => 'section.name.application/check-answers',
                        'question' => 'section.name.application/check-answers',
                        'answer' => 0,
                        'status' => SectionableInterface::SECTION_COMPLETION_CANNOT_START,
                    ],
                    'custom-declaration' => [
                        'section' => 'declaration',
                        'slug' => 'custom-declaration',
                        'questionShort' => 'section.name.application/declaration',
                        'question' => 'section.name.application/declaration',
                        'answer' => null,
                        'status' => SectionableInterface::SECTION_COMPLETION_CANNOT_START,
                    ],
                ],
                false,
            ],
            'q1 answered' => [
                'data' => [
                    'licNo' => 'OB1234567',
                    'answers' => new ArrayCollection([$question1TextId => $answer1]),
                    'checkedAnswers' => 0,
                    'declaration' => 0,
                    'createdOn' => $createdOn,
                ],
                'applicationSteps' => new ArrayCollection([$step1, $step2]),
                'expected' => [
                    'custom-licence' => [
                        'section' => 'licence',
                        'slug' => 'custom-licence',
                        'questionShort' => 'section.name.application/licence',
                        'question' => 'section.name.application/licence',
                        'questionType' => Question::QUESTION_TYPE_STRING,
                        'answer' => 'OB1234567',
                        'status' => SectionableInterface::SECTION_COMPLETION_COMPLETED,
                    ],
                    'q1-slug' => [
                        'section' => 'q1-slug',
                        'slug' => 'q1-slug',
                        'questionShort' => 'q1-short-key',
                        'question' => 'q1-key',
                        'questionType' => 'q1-type',
                        'answer' => 'q1-answer',
                        'status' => SectionableInterface::SECTION_COMPLETION_COMPLETED,
                    ],
                    'q2-slug' => [
                        'section' => 'q2-slug',
                        'slug' => 'q2-slug',
                        'questionShort' => 'q2-short-key',
                        'question' => 'q2-key',
                        'questionType' => 'q2-type',
                        'answer' => null,
                        'status' => SectionableInterface::SECTION_COMPLETION_NOT_STARTED,
                    ],
                    'custom-check-answers' => [
                        'section' => 'checkedAnswers',
                        'slug' => 'custom-check-answers',
                        'questionShort' => 'section.name.application/check-answers',
                        'question' => 'section.name.application/check-answers',
                        'answer' => 0,
                        'status' => SectionableInterface::SECTION_COMPLETION_CANNOT_START,
                    ],
                    'custom-declaration' => [
                        'section' => 'declaration',
                        'slug' => 'custom-declaration',
                        'questionShort' => 'section.name.application/declaration',
                        'question' => 'section.name.application/declaration',
                        'answer' => null,
                        'status' => SectionableInterface::SECTION_COMPLETION_CANNOT_START,
                    ],
                ],
                false,
            ],
            'q2 answered' => [
                'data' => [
                    'licNo' => 'OB1234567',
                    'answers' => new ArrayCollection([$question1TextId => $answer1, $question2TextId => $answer2]),
                    'checkedAnswers' => 0,
                    'declaration' => 0,
                    'createdOn' => $createdOn,
                ],
                'applicationSteps' => new ArrayCollection([$step1, $step2]),
                'expected' => [
                    'custom-licence' => [
                        'section' => 'licence',
                        'slug' => 'custom-licence',
                        'questionShort' => 'section.name.application/licence',
                        'question' => 'section.name.application/licence',
                        'questionType' => Question::QUESTION_TYPE_STRING,
                        'answer' => 'OB1234567',
                        'status' => SectionableInterface::SECTION_COMPLETION_COMPLETED,
                    ],
                    'q1-slug' => [
                        'section' => 'q1-slug',
                        'slug' => 'q1-slug',
                        'questionShort' => 'q1-short-key',
                        'question' => 'q1-key',
                        'questionType' => 'q1-type',
                        'answer' => 'q1-answer',
                        'status' => SectionableInterface::SECTION_COMPLETION_COMPLETED,
                    ],
                    'q2-slug' => [
                        'section' => 'q2-slug',
                        'slug' => 'q2-slug',
                        'questionShort' => 'q2-short-key',
                        'question' => 'q2-key',
                        'questionType' => 'q2-type',
                        'answer' => 'q2-answer',
                        'status' => SectionableInterface::SECTION_COMPLETION_COMPLETED,
                    ],
                    'custom-check-answers' => [
                        'section' => 'checkedAnswers',
                        'slug' => 'custom-check-answers',
                        'questionShort' => 'section.name.application/check-answers',
                        'question' => 'section.name.application/check-answers',
                        'answer' => 0,
                        'status' => SectionableInterface::SECTION_COMPLETION_NOT_STARTED,
                    ],
                    'custom-declaration' => [
                        'section' => 'declaration',
                        'slug' => 'custom-declaration',
                        'questionShort' => 'section.name.application/declaration',
                        'question' => 'section.name.application/declaration',
                        'answer' => null,
                        'status' => SectionableInterface::SECTION_COMPLETION_CANNOT_START,
                    ],
                ],
                false
            ],
            'q2 answered snapshot version' => [
                'data' => [
                    'licNo' => 'OB1234567',
                    'answers' => new ArrayCollection([$question1TextId => $answer1, $question2TextId => $answer2]),
                    'checkedAnswers' => 0,
                    'declaration' => 0,
                    'createdOn' => $createdOn,
                ],
                'applicationSteps' => new ArrayCollection([$step1, $step2]),
                'expected' => [
                    'custom-licence' => [
                        'section' => 'licence',
                        'slug' => 'custom-licence',
                        'questionShort' => 'section.name.application/licence',
                        'question' => 'permits.check-answers.page.question.licence',
                        'questionType' => Question::QUESTION_TYPE_STRING,
                        'answer' => 'OB1234567',
                        'status' => SectionableInterface::SECTION_COMPLETION_COMPLETED,
                    ],
                    'q1-slug' => [
                        'section' => 'q1-slug',
                        'slug' => 'q1-slug',
                        'questionShort' => 'q1-short-key',
                        'question' => 'q1-key',
                        'questionType' => 'q1-type',
                        'answer' => 'q1-answer',
                        'status' => SectionableInterface::SECTION_COMPLETION_COMPLETED,
                    ],
                    'q2-slug' => [
                        'section' => 'q2-slug',
                        'slug' => 'q2-slug',
                        'questionShort' => 'q2-short-key',
                        'question' => 'q2-key',
                        'questionType' => 'q2-type',
                        'answer' => 'q2-answer',
                        'status' => SectionableInterface::SECTION_COMPLETION_COMPLETED,
                    ],
                ],
                true
            ],
            'answers checked' => [
                'data' => [
                    'licNo' => 'OB1234567',
                    'answers' => new ArrayCollection([$question1TextId => $answer1, $question2TextId => $answer2]),
                    'checkedAnswers' => 1,
                    'declaration' => 0,
                    'createdOn' => $createdOn,
                ],
                'applicationSteps' => new ArrayCollection([$step1, $step2]),
                'expected' => [
                    'custom-licence' => [
                        'section' => 'licence',
                        'slug' => 'custom-licence',
                        'questionShort' => 'section.name.application/licence',
                        'question' => 'section.name.application/licence',
                        'questionType' => Question::QUESTION_TYPE_STRING,
                        'answer' => 'OB1234567',
                        'status' => SectionableInterface::SECTION_COMPLETION_COMPLETED,
                    ],
                    'q1-slug' => [
                        'section' => 'q1-slug',
                        'slug' => 'q1-slug',
                        'questionShort' => 'q1-short-key',
                        'question' => 'q1-key',
                        'questionType' => 'q1-type',
                        'answer' => 'q1-answer',
                        'status' => SectionableInterface::SECTION_COMPLETION_COMPLETED,
                    ],
                    'q2-slug' => [
                        'section' => 'q2-slug',
                        'slug' => 'q2-slug',
                        'questionShort' => 'q2-short-key',
                        'question' => 'q2-key',
                        'questionType' => 'q2-type',
                        'answer' => 'q2-answer',
                        'status' => SectionableInterface::SECTION_COMPLETION_COMPLETED,
                    ],
                    'custom-check-answers' => [
                        'section' => 'checkedAnswers',
                        'slug' => 'custom-check-answers',
                        'questionShort' => 'section.name.application/check-answers',
                        'question' => 'section.name.application/check-answers',
                        'answer' => 1,
                        'status' => SectionableInterface::SECTION_COMPLETION_COMPLETED,
                    ],
                    'custom-declaration' => [
                        'section' => 'declaration',
                        'slug' => 'custom-declaration',
                        'questionShort' => 'section.name.application/declaration',
                        'question' => 'section.name.application/declaration',
                        'answer' => null,
                        'status' => SectionableInterface::SECTION_COMPLETION_NOT_STARTED,
                    ],
                ],
                false
            ],
            'declaration set' => [
                'data' => [
                    'licNo' => 'OB1234567',
                    'answers' => new ArrayCollection([$question1TextId => $answer1, $question2TextId => $answer2]),
                    'checkedAnswers' => 1,
                    'declaration' => 1,
                    'createdOn' => $createdOn,
                ],
                'applicationSteps' => new ArrayCollection([$step1, $step2]),
                'expected' => [
                    'custom-licence' => [
                        'section' => 'licence',
                        'slug' => 'custom-licence',
                        'questionShort' => 'section.name.application/licence',
                        'question' => 'section.name.application/licence',
                        'questionType' => Question::QUESTION_TYPE_STRING,
                        'answer' => 'OB1234567',
                        'status' => SectionableInterface::SECTION_COMPLETION_COMPLETED,
                    ],
                    'q1-slug' => [
                        'section' => 'q1-slug',
                        'slug' => 'q1-slug',
                        'questionShort' => 'q1-short-key',
                        'question' => 'q1-key',
                        'questionType' => 'q1-type',
                        'answer' => 'q1-answer',
                        'status' => SectionableInterface::SECTION_COMPLETION_COMPLETED,
                    ],
                    'q2-slug' => [
                        'section' => 'q2-slug',
                        'slug' => 'q2-slug',
                        'questionShort' => 'q2-short-key',
                        'question' => 'q2-key',
                        'questionType' => 'q2-type',
                        'answer' => 'q2-answer',
                        'status' => SectionableInterface::SECTION_COMPLETION_COMPLETED,
                    ],
                    'custom-check-answers' => [
                        'section' => 'checkedAnswers',
                        'slug' => 'custom-check-answers',
                        'questionShort' => 'section.name.application/check-answers',
                        'question' => 'section.name.application/check-answers',
                        'answer' => 1,
                        'status' => SectionableInterface::SECTION_COMPLETION_COMPLETED,
                    ],
                    'custom-declaration' => [
                        'section' => 'declaration',
                        'slug' => 'custom-declaration',
                        'questionShort' => 'section.name.application/declaration',
                        'question' => 'section.name.application/declaration',
                        'answer' => 1,
                        'status' => SectionableInterface::SECTION_COMPLETION_COMPLETED,
                    ],
                ],
                false,
            ],
        ];
    }

    public function testGetAnswerForCustomEcmtRemovalNoOfPermits()
    {
        $permitsRequired = 47;

        $irhpPermitApplication = m::mock(IrhpPermitApplication::class);
        $irhpPermitApplication->shouldReceive('getPermitsRequired')
            ->andReturn($permitsRequired);

        $question = m::mock(Question::class);
        $question->shouldReceive('isCustom')->withNoArgs()->once()->andReturn(true);
        $question->shouldReceive('getFormControlType')->andReturn(Question::FORM_CONTROL_ECMT_REMOVAL_NO_OF_PERMITS);

        $step = m::mock(ApplicationStep::class);
        $step->shouldReceive('getQuestion')->withNoArgs()->once()->andReturn($question);

        $entity = $this->createNewEntity();
        $entity->addIrhpPermitApplications($irhpPermitApplication);

        $this->assertEquals($permitsRequired, $entity->getAnswer($step));
    }

    /**
     * @dataProvider dpGetAnswerForCustomEcmtShortTermNoOfPermits
     */
    public function testGetAnswerForCustomEcmtShortTermNoOfPermits(
        $requiredEuro5,
        $requiredEuro6,
        $isSnapshot,
        $validityYear,
        $periodNameKey,
        $expectedAnswer
    ) {
        $question = m::mock(Question::class);
        $question->shouldReceive('isCustom')->withNoArgs()->once()->andReturn(true);
        $question->shouldReceive('getFormControlType')->andReturn(
            Question::FORM_CONTROL_ECMT_SHORT_TERM_NO_OF_PERMITS
        );

        $step = m::mock(ApplicationStep::class);
        $step->shouldReceive('getQuestion')->withNoArgs()->once()->andReturn($question);

        $irhpPermitStock = m::mock(IrhpPermitStock::class);
        $irhpPermitStock->shouldReceive('getValidityYear')
            ->andReturn($validityYear);
        $irhpPermitStock->shouldReceive('getPeriodNameKey')
            ->andReturn($periodNameKey);

        $irhpPermitApplication = m::mock(IrhpPermitApplication::class);
        $irhpPermitApplication->shouldReceive('getIrhpPermitWindow->getIrhpPermitStock')
            ->andReturn($irhpPermitStock);
        $irhpPermitApplication->shouldReceive('getRequiredEuro5')
            ->andReturn($requiredEuro5);
        $irhpPermitApplication->shouldReceive('getRequiredEuro6')
            ->andReturn($requiredEuro6);

        $entity = m::mock(Entity::class)->makePartial();
        $entity->shouldReceive('getFirstIrhpPermitApplication')
            ->andReturn($irhpPermitApplication);

        $this->assertSame(
            $expectedAnswer,
            $entity->getAnswer($step, $isSnapshot)
        );
    }

    public function dpGetAnswerForCustomEcmtShortTermNoOfPermits()
    {
        return [
            [
                'requiredEuro5' => 5,
                'requiredEuro6' => 7,
                'isSnapshot' => true,
                'validityYear' => 2019,
                'periodNameKey' => null,
                'expectedAnswer' => [
                    'Permits for 2019',
                    '5 permits for Euro 5 minimum emission standard',
                    '7 permits for Euro 6 minimum emission standard',
                ],
            ],
            [
                'requiredEuro5' => 5,
                'requiredEuro6' => 7,
                'isSnapshot' => true,
                'validityYear' => 2020,
                'periodNameKey' => 'period.name.key',
                'expectedAnswer' => [
                    'period.name.key',
                    '5 permits for Euro 5 minimum emission standard',
                    '7 permits for Euro 6 minimum emission standard',
                ],
            ],
            [
                'requiredEuro5' => 5,
                'requiredEuro6' => 0,
                'isSnapshot' => true,
                'validityYear' => 2020,
                'periodNameKey' => 'period.name.key',
                'expectedAnswer' => [
                    'period.name.key',
                    '5 permits for Euro 5 minimum emission standard',
                ],
            ],
            [
                'requiredEuro5' => null,
                'requiredEuro6' => 5,
                'isSnapshot' => true,
                'validityYear' => 2020,
                'periodNameKey' => 'period.name.key',
                'expectedAnswer' => null,
            ],
            [
                'requiredEuro5' => 5,
                'requiredEuro6' => 7,
                'isSnapshot' => false,
                'validityYear' => 2019,
                'periodNameKey' => null,
                'expectedAnswer' => [
                    '5 permits for Euro 5 minimum emission standard',
                    '7 permits for Euro 6 minimum emission standard',
                ],
            ],
            [
                'requiredEuro5' => 5,
                'requiredEuro6' => 7,
                'isSnapshot' => false,
                'validityYear' => 2020,
                'periodNameKey' => 'period.name.key',
                'expectedAnswer' => [
                    '5 permits for Euro 5 minimum emission standard',
                    '7 permits for Euro 6 minimum emission standard',
                ],
            ],
            [
                'requiredEuro5' => 5,
                'requiredEuro6' => 0,
                'isSnapshot' => false,
                'validityYear' => 2020,
                'periodNameKey' => 'period.name.key',
                'expectedAnswer' => [
                    '5 permits for Euro 5 minimum emission standard',
                ],
            ],
            [
                'requiredEuro5' => null,
                'requiredEuro6' => 5,
                'isSnapshot' => false,
                'validityYear' => 2020,
                'periodNameKey' => 'period.name.key',
                'expectedAnswer' => null,
            ],
        ];
    }

    public function testGetAnswerForCustomEcmtShortTermInternationalJourneys()
    {
        $question = m::mock(Question::class);
        $question->shouldReceive('isCustom')->withNoArgs()->once()->andReturn(true);
        $question->shouldReceive('getFormControlType')->andReturn(
            Question::FORM_CONTROL_ECMT_SHORT_TERM_INTERNATIONAL_JOURNEYS
        );

        $step = m::mock(ApplicationStep::class);
        $step->shouldReceive('getQuestion')->withNoArgs()->once()->andReturn($question);

        $internationalJourneysKey = 'int_journeys_ref_data_key';

        $refData = m::mock(RefData::class);
        $refData->shouldReceive('getId')
            ->withNoArgs()
            ->andReturn($internationalJourneysKey);

        $entity = $this->createNewEntity();
        $entity->setInternationalJourneys($refData);

        $this->assertSame(
            $internationalJourneysKey,
            $entity->getAnswer($step)
        );
    }

    public function testGetAnswerForCustomEcmtShortTermInternationalJourneysNull()
    {
        $question = m::mock(Question::class);
        $question->shouldReceive('isCustom')->withNoArgs()->once()->andReturn(true);
        $question->shouldReceive('getFormControlType')->andReturn(
            Question::FORM_CONTROL_ECMT_SHORT_TERM_INTERNATIONAL_JOURNEYS
        );

        $step = m::mock(ApplicationStep::class);
        $step->shouldReceive('getQuestion')->withNoArgs()->once()->andReturn($question);

        $entity = $this->createNewEntity();
        $entity->setInternationalJourneys(null);

        $this->assertNull(
            $entity->getAnswer($step)
        );
    }

    /**
     * @dataProvider dpTestGetAnswerForCustomEcmtShortTermRestrictedCountries
     */
    public function testGetAnswerForCustomEcmtShortTermRestrictedCountries($answerValue, $expectedAnswer)
    {
        $createdOn = new DateTime();
        $questionTextId = 1;

        $questionText = m::mock(QuestionText::class);
        $questionText->shouldReceive('getId')
            ->withNoArgs()
            ->andReturn($questionTextId);

        $question = m::mock(Question::class);
        $question->shouldReceive('isCustom')
            ->withNoArgs()
            ->andReturn(true);
        $question->shouldReceive('getFormControlType')
            ->andReturn(Question::FORM_CONTROL_ECMT_SHORT_TERM_RESTRICTED_COUNTRIES);
        $question->shouldReceive('getActiveQuestionText')
            ->with($createdOn)
            ->once()
            ->andReturn($questionText);

        $applicationStep = m::mock(ApplicationStep::class);
        $applicationStep->shouldReceive('getQuestion')
            ->withNoArgs()
            ->andReturn($question);

        $answer = m::mock(Answer::class);
        $answer->shouldReceive('getValue')
            ->withNoArgs()
            ->andReturn($answerValue);

        $country1 = m::mock(Country::class);
        $country1->shouldReceive('getCountryDesc')
            ->andReturn('Belgium');

        $country2 = m::mock(Country::class);
        $country2->shouldReceive('getCountryDesc')
            ->andReturn('France');

        $entity = $this->createNewEntity();
        $entity->setCreatedOn($createdOn);
        $entity->setAnswers(new ArrayCollection([$questionTextId => $answer]));
        $entity->setCountrys(new ArrayCollection([$country1, $country2]));

        $this->assertEquals(
            $expectedAnswer,
            $entity->getAnswer($applicationStep)
        );
    }

    public function dpTestGetAnswerForCustomEcmtShortTermRestrictedCountries()
    {
        return [
            [
                true,
                ['Yes', 'Belgium, France']
            ],
            [
                false,
                ['No']
            ],
        ];
    }

    public function testGetAnswerNullForCustomEcmtShortTermRestrictedCountries()
    {
        $createdOn = new DateTime();
        $answerValue = null;
        $questionTextId = 1;

        $questionText = m::mock(QuestionText::class);
        $questionText->shouldReceive('getId')
            ->withNoArgs()
            ->andReturn($questionTextId);

        $question = m::mock(Question::class);
        $question->shouldReceive('isCustom')
            ->withNoArgs()
            ->andReturn(true);
        $question->shouldReceive('getFormControlType')
            ->andReturn(Question::FORM_CONTROL_ECMT_SHORT_TERM_RESTRICTED_COUNTRIES);
        $question->shouldReceive('getActiveQuestionText')
            ->with($createdOn)
            ->once()
            ->andReturn($questionText);

        $applicationStep = m::mock(ApplicationStep::class);
        $applicationStep->shouldReceive('getQuestion')
            ->withNoArgs()
            ->andReturn($question);

        $answer = m::mock(Answer::class);
        $answer->shouldReceive('getValue')
            ->withNoArgs()
            ->andReturn($answerValue);

        $entity = $this->createNewEntity();
        $entity->setCreatedOn($createdOn);
        $entity->setAnswers(new ArrayCollection([$questionTextId => $answer]));

        $this->assertNull(
            $entity->getAnswer($applicationStep)
        );
    }

    /**
     * @dataProvider dpTestGetAnswerForCustomEcmtShortTermSectors
     */
    public function testGetAnswerForCustomEcmtShortTermSectors($sectorsEntity, $isSnapshot, $expectedAnswer)
    {
        $question = m::mock(Question::class);
        $question->shouldReceive('isCustom')->withNoArgs()->once()->andReturn(true);
        $question->shouldReceive('getFormControlType')->andReturn(
            Question::FORM_CONTROL_ECMT_SHORT_TERM_SECTORS
        );

        $step = m::mock(ApplicationStep::class);
        $step->shouldReceive('getQuestion')->withNoArgs()->once()->andReturn($question);

        $entity = $this->createNewEntity();
        $entity->setSectors($sectorsEntity);

        $this->assertEquals(
            $expectedAnswer,
            $entity->getAnswer($step, $isSnapshot)
        );
    }

    public function dpTestGetAnswerForCustomEcmtShortTermSectors()
    {
        $sectorId = 7;
        $sectorName = 'Wood';

        $sectors = m::mock(Sectors::class);
        $sectors->shouldReceive('getId')
            ->andReturn($sectorId);
        $sectors->shouldReceive('getName')
            ->andReturn($sectorName);

        return [
            [$sectors, false, $sectorId],
            [$sectors, true, $sectorName],
            [null, false, null],
            [null, true, null],
        ];
    }

    public function testGetAnswerForQuestionWithoutActiveQuestionText()
    {
        $createdOn = new DateTime();

        $question = m::mock(Question::class);
        $question->shouldReceive('isCustom')->withNoArgs()->once()->andReturn(false);
        $question->shouldReceive('getActiveQuestionText')->with($createdOn)->once()->andReturn(null);

        $step = m::mock(ApplicationStep::class);
        $step->shouldReceive('getQuestion')->withNoArgs()->once()->andReturn($question);

        $entity = $this->createNewEntity();
        $entity->setCreatedOn($createdOn);

        $this->assertNull($entity->getAnswer($step));
    }

    /**
     * @dataProvider dpGetAnswerForQuestion
     */
    public function testGetAnswerForQuestionWithoutAnswer($isCustom, $formControlType)
    {
        $createdOn = new DateTime();

        $questionTextId = 1;
        $questionText = m::mock(QuestionText::class);
        $questionText->shouldReceive('getId')->withNoArgs()->once()->andReturn($questionTextId);

        $question = m::mock(Question::class);
        $question->shouldReceive('isCustom')->withNoArgs()->once()->andReturn($isCustom);
        $question->shouldReceive('getFormControlType')->withNoArgs()->andReturn($formControlType);
        $question->shouldReceive('getActiveQuestionText')->with($createdOn)->once()->andReturn($questionText);

        $step = m::mock(ApplicationStep::class);
        $step->shouldReceive('getQuestion')->withNoArgs()->once()->andReturn($question);

        $entity = $this->createNewEntity();
        $entity->setCreatedOn($createdOn);

        $this->assertNull($entity->getAnswer($step));
    }

    /**
     * @dataProvider dpGetAnswerForQuestion
     */
    public function testGetAnswerForQuestionWithAnswer($isCustom, $formControlType)
    {
        $createdOn = new DateTime();
        $answer = 'answer';

        $questionTextId = 1;
        $questionText = m::mock(QuestionText::class);
        $questionText->shouldReceive('getId')->withNoArgs()->once()->andReturn($questionTextId);

        $question = m::mock(Question::class);
        $question->shouldReceive('isCustom')->withNoArgs()->once()->andReturn($isCustom);
        $question->shouldReceive('getFormControlType')->withNoArgs()->andReturn($formControlType);
        $question->shouldReceive('getActiveQuestionText')->with($createdOn)->once()->andReturn($questionText);

        $step = m::mock(ApplicationStep::class);
        $step->shouldReceive('getQuestion')->withNoArgs()->once()->andReturn($question);

        $answer = m::mock(Answer::class);
        $answer->shouldReceive('getValue')->withNoArgs()->once()->andReturn($answer);

        $entity = $this->createNewEntity();
        $entity->setCreatedOn($createdOn);
        $entity->setAnswers(new ArrayCollection([$questionTextId => $answer]));

        $this->assertEquals($answer, $entity->getAnswer($step));
    }

    public function dpGetAnswerForQuestion()
    {
        return [
            [false, null],
            [true, Question::FORM_CONTROL_ECMT_REMOVAL_PERMIT_START_DATE],
            [true, Question::FORM_CONTROL_ECMT_SHORT_TERM_ANNUAL_TRIPS_ABROAD],
            [true, Question::FORM_CONTROL_CERT_ROADWORTHINESS_MOT_EXPIRY_DATE],
            [true, Question::FORM_CONTROL_COMMON_CERTIFICATES],
        ];
    }

    public function testGetOutstandingApplicationFees()
    {
        $fee1 = $this->createMockFee(FeeType::FEE_TYPE_IRHP_APP, true);
        $fee2 = $this->createMockFee(FeeType::FEE_TYPE_IRHP_APP, false);
        $fee3 = $this->createMockFee(FeeType::FEE_TYPE_IRHP_ISSUE, false);
        $fee4 = $this->createMockFee(FeeType::FEE_TYPE_IRHP_APP, true);
        $fee5 = $this->createMockFee(FeeType::FEE_TYPE_IRHP_ISSUE, true);

        $this->sut->setFees(
            new ArrayCollection([$fee1, $fee2, $fee3, $fee4, $fee5])
        );

        $outstandingApplicationFees = $this->sut->getOutstandingApplicationFees();
        $this->assertCount(2, $outstandingApplicationFees);
        $this->assertSame($fee1, $outstandingApplicationFees[0]);
        $this->assertSame($fee4, $outstandingApplicationFees[1]);
    }

    public function testGetOutstandingIssueFees()
    {
        $fee1 = $this->createMockFee(FeeType::FEE_TYPE_IRHP_ISSUE, true);
        $fee2 = $this->createMockFee(FeeType::FEE_TYPE_IRHP_ISSUE, false);
        $fee3 = $this->createMockFee(FeeType::FEE_TYPE_IRHP_APP, false);
        $fee4 = $this->createMockFee(FeeType::FEE_TYPE_IRHP_ISSUE, true);
        $fee5 = $this->createMockFee(FeeType::FEE_TYPE_IRHP_APP, true);

        $this->sut->setFees(
            new ArrayCollection([$fee1, $fee2, $fee3, $fee4, $fee5])
        );

        $outstandingIssueFees = $this->sut->getOutstandingIssueFees();
        $this->assertCount(2, $outstandingIssueFees);
        $this->assertSame($fee1, $outstandingIssueFees[0]);
        $this->assertSame($fee4, $outstandingIssueFees[1]);
    }

    public function testGetOutstandingIrfoPermitFees()
    {
        $fee1 = $this->createMockFee(FeeType::FEE_TYPE_IRHP_ISSUE, true);
        $fee2 = $this->createMockFee(FeeType::FEE_TYPE_IRFOGVPERMIT, false);
        $fee3 = $this->createMockFee(FeeType::FEE_TYPE_IRHP_APP, false);
        $fee4 = $this->createMockFee(FeeType::FEE_TYPE_IRFOGVPERMIT, true);
        $fee5 = $this->createMockFee(FeeType::FEE_TYPE_IRHP_APP, true);
        $fee6 = $this->createMockFee(FeeType::FEE_TYPE_IRFOGVPERMIT, true);

        $this->sut->setFees(
            new ArrayCollection([$fee1, $fee2, $fee3, $fee4, $fee5, $fee6])
        );

        $outstandingIrfoPermitFees = $this->sut->getOutstandingIrfoPermitFees();
        $this->assertCount(2, $outstandingIrfoPermitFees);
        $this->assertSame($fee4, $outstandingIrfoPermitFees[0]);
        $this->assertSame($fee6, $outstandingIrfoPermitFees[1]);
    }

    public function testGetContextValue()
    {
        $irhpApplicationId = 87;

        $irhpApplication = m::mock(Entity::class)->makePartial();
        $irhpApplication->setId($irhpApplicationId);

        $this->assertEquals($irhpApplicationId, $irhpApplication->getContextValue());
    }

    private function createMockFee($feeTypeId, $isOutstanding)
    {
        $fee = m::mock(Fee::class);
        $fee->shouldReceive('getFeeType->getFeeType->getId')
            ->andReturn($feeTypeId);
        $fee->shouldReceive('isOutstanding')
            ->andReturn($isOutstanding);

        return $fee;
    }

    private function createNewEntity(
        $source = null,
        $status = null,
        $irhpPermitType = null,
        $licence = null,
        $dateReceived = null
    ): Entity {
        if (!isset($source)) {
            $source = m::mock(RefData::class);
        }

        if (!isset($status)) {
            $status = m::mock(RefData::class);
        }

        if (!isset($irhpPermitType)) {
            $irhpPermitType = m::mock(IrhpPermitType::class);
        }

        if (!isset($licence)) {
            $licence = m::mock(Licence::class);
        }

        return Entity::createNew($source, $status, $irhpPermitType, $licence, $dateReceived);
    }

    /**
     * @dataProvider dpTestGetFeePerPermitBilateralMultilateral
     */
    public function testGetFeePerPermitBilateralMultilateral($irhpPermitTypeId)
    {
        $applicationFeeType = m::mock(FeeType::class);
        $applicationFeeType->shouldReceive('getFixedValue')
            ->andReturn(43.20);

        $issueFeeType = m::mock(FeeType::class);
        $issueFeeType->shouldReceive('getFixedValue')
            ->andReturn(12.15);

        $entity = m::mock(Entity::class)->makePartial();
        $entity->shouldReceive('getIrhpPermitType->getId')
            ->andReturn($irhpPermitTypeId);

        $this->assertEquals(
            55.35,
            $entity->getFeePerPermit($applicationFeeType, $issueFeeType)
        );
    }

    public function dpTestGetFeePerPermitBilateralMultilateral()
    {
        return [
            [IrhpPermitType::IRHP_PERMIT_TYPE_ID_BILATERAL],
            [IrhpPermitType::IRHP_PERMIT_TYPE_ID_MULTILATERAL]
        ];
    }

    public function testGetFeePerPermitEcmtRemoval()
    {
        $issueFee = 14.20;

        $issueFeeType = m::mock(FeeType::class);
        $issueFeeType->shouldReceive('getFixedValue')
            ->andReturn($issueFee);

        $entity = m::mock(Entity::class)->makePartial();
        $entity->shouldReceive('getIrhpPermitType->getId')
            ->andReturn(IrhpPermitType::IRHP_PERMIT_TYPE_ID_ECMT_REMOVAL);

        $this->assertEquals(
            $issueFee,
            $entity->getFeePerPermit(null, $issueFeeType)
        );
    }

    public function testGetFeePerPermitUnsupported()
    {
        $this->expectException(ForbiddenException::class);
        $this->expectExceptionMessage(
            'Cannot get fee per permit for irhp permit type ' . IrhpPermitType::IRHP_PERMIT_TYPE_ID_ECMT
        );

        $applicationFeeType = m::mock(FeeType::class);
        $issueFeeType = m::mock(FeeType::class);

        $entity = m::mock(Entity::class)->makePartial();
        $entity->shouldReceive('getIrhpPermitType->getId')
            ->andReturn(IrhpPermitType::IRHP_PERMIT_TYPE_ID_ECMT);

        $entity->getFeePerPermit($applicationFeeType, $issueFeeType);
    }

    public function testGetFirstIrhpPermitApplication()
    {
        $irhpPermitApplication = m::mock(IrhpPermitApplication::class);

        $entity = $this->createNewEntity();
        $entity->addIrhpPermitApplications($irhpPermitApplication);

        $this->assertSame(
            $irhpPermitApplication,
            $entity->getFirstIrhpPermitApplication()
        );
    }

    public function testGetFirstIrhpPermitApplicationExceptionOnNone()
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage(
            'IrhpApplication has zero linked IrhpPermitApplication instances'
        );

        $entity = $this->createNewEntity();
        $entity->getFirstIrhpPermitApplication();
    }

    public function testGetFirstIrhpPermitApplicationExceptionOnMoreThanOne()
    {
        $irhpPermitApplication1 = m::mock(IrhpPermitApplication::class);
        $irhpPermitApplication2 = m::mock(IrhpPermitApplication::class);

        $entity = $this->createNewEntity();
        $entity->addIrhpPermitApplications($irhpPermitApplication1);
        $entity->addIrhpPermitApplications($irhpPermitApplication2);

        $this->assertSame(
            $irhpPermitApplication1,
            $entity->getFirstIrhpPermitApplication()
        );
    }

    public function testExpire()
    {
        $entity = m::mock(Entity::class)->makePartial();
        $entity->shouldReceive('canBeExpired')
            ->andReturn(true);

        $this->assertNull($entity->getExpiryDate());
        $status = m::mock(RefData::class);

        $entity->expire($status);
        $this->assertSame($status, $entity->getStatus());
        $this->assertInstanceOf(DateTime::class, $entity->getExpiryDate());
    }

    public function testExpireException()
    {
        $this->expectException(ForbiddenException::class);
        $this->expectExceptionMessage('This application cannot be expired.');

        $entity = m::mock(Entity::class)->makePartial();
        $entity->shouldReceive('canBeExpired')
            ->andReturn(false);

        $entity->expire(m::mock(RefData::class));
    }

    /**
     * @dataProvider dpCanBeExpired
     */
    public function testCanBeExpired($status, $hasValidPermits, $expected)
    {
        $entity = $this->createNewEntity();
        $entity->setStatus(new RefData($status));

        $irhpPermitApp1 = m::mock(IrhpPermitApplication::class);
        $irhpPermitApp1->shouldReceive('hasValidPermits')->andReturn(false);
        $entity->addIrhpPermitApplications($irhpPermitApp1);

        $irhpPermitApp2 = m::mock(IrhpPermitApplication::class);
        $irhpPermitApp2->shouldReceive('hasValidPermits')->andReturn($hasValidPermits);
        $entity->addIrhpPermitApplications($irhpPermitApp2);

        $this->assertEquals($expected, $entity->canBeExpired());
    }

    public function dpCanBeExpired()
    {
        return [
            [IrhpInterface::STATUS_VALID, true, false],
            [IrhpInterface::STATUS_VALID, false, true],
            [IrhpInterface::STATUS_CANCELLED, true, false],
            [IrhpInterface::STATUS_CANCELLED, false, false],
            [IrhpInterface::STATUS_NOT_YET_SUBMITTED, true, false],
            [IrhpInterface::STATUS_NOT_YET_SUBMITTED, false, false],
            [IrhpInterface::STATUS_UNDER_CONSIDERATION, true, false],
            [IrhpInterface::STATUS_UNDER_CONSIDERATION, false, false],
            [IrhpInterface::STATUS_WITHDRAWN, true, false],
            [IrhpInterface::STATUS_WITHDRAWN, false, false],
            [IrhpInterface::STATUS_AWAITING_FEE, true, false],
            [IrhpInterface::STATUS_AWAITING_FEE, false, false],
            [IrhpInterface::STATUS_FEE_PAID, true, false],
            [IrhpInterface::STATUS_FEE_PAID, false, false],
            [IrhpInterface::STATUS_UNSUCCESSFUL, true, false],
            [IrhpInterface::STATUS_UNSUCCESSFUL, false, false],
            [IrhpInterface::STATUS_ISSUED, true, false],
            [IrhpInterface::STATUS_ISSUED, false, false],
            [IrhpInterface::STATUS_ISSUING, true, false],
            [IrhpInterface::STATUS_ISSUING, false, false],
            [IrhpInterface::STATUS_EXPIRED, true, false],
            [IrhpInterface::STATUS_EXPIRED, false, false],
        ];
    }

    /**
     * @dataProvider dpTestCanViewCandidatePermits
     */
    public function testCanViewCandidatePermits($isAwaitingFee, $allocationMode, $expected)
    {
        $this->sut->shouldReceive('isAwaitingFee')
            ->once()
            ->withNoArgs()
            ->andReturn($isAwaitingFee);

        $this->sut->shouldReceive('getAllocationMode')
            ->withNoArgs()
            ->andReturn($allocationMode);

        $this->assertSame($expected, $this->sut->canViewCandidatePermits());
    }

    public function dpTestCanViewCandidatePermits()
    {
        return [
            [true, IrhpPermitStock::ALLOCATION_MODE_STANDARD, false],
            [true, IrhpPermitStock::ALLOCATION_MODE_EMISSIONS_CATEGORIES, false],
            [true, IrhpPermitStock::ALLOCATION_MODE_STANDARD_WITH_EXPIRY, false],
            [true, IrhpPermitStock::ALLOCATION_MODE_CANDIDATE_PERMITS, true],
            [false, IrhpPermitStock::ALLOCATION_MODE_STANDARD, false],
            [false, IrhpPermitStock::ALLOCATION_MODE_EMISSIONS_CATEGORIES, false],
            [false, IrhpPermitStock::ALLOCATION_MODE_STANDARD_WITH_EXPIRY, false],
            [false, IrhpPermitStock::ALLOCATION_MODE_CANDIDATE_PERMITS, false],
        ];
    }

    /**
     * @dataProvider dpCanBeGranted
     */
    public function testCanBeGranted($isUnderConsideration, $licenceValid, $businessProcess, $expected)
    {
        $this->sut->shouldReceive('isUnderConsideration')
            ->withNoArgs()
            ->andReturn($isUnderConsideration);

        $licence = m::mock(Licence::class);
        $this->sut->setLicence($licence);

        $licence->allows('isValid')
            ->andReturn($licenceValid);

        $this->sut->shouldReceive('getBusinessProcess')
            ->withNoArgs()
            ->andReturn(new RefData($businessProcess));

        $this->assertEquals($expected, $this->sut->canBeGranted());
    }

    public function dpCanBeGranted()
    {
        return [
            [true, true, RefData::BUSINESS_PROCESS_APGG, true],
            [false, true, RefData::BUSINESS_PROCESS_APGG, false],
            [true, false, RefData::BUSINESS_PROCESS_APGG, false],
            [true, true, RefData::BUSINESS_PROCESS_APSG, false],
            [true, true, RefData::BUSINESS_PROCESS_APG, false],
        ];
    }

    public function testUpdateInternationalJourneys()
    {
        $refData = m::mock(RefData::class);

        $entity = $this->createNewEntity();
        $entity->updateInternationalJourneys($refData);

        $this->assertSame(
            $refData,
            $entity->getInternationalJourneys()
        );
    }

    public function testClearInternationalJourneys()
    {
        $refData = m::mock(RefData::class);

        $entity = $this->createNewEntity();
        $entity->setInternationalJourneys($refData);
        $entity->clearInternationalJourneys();

        $this->assertNull($entity->getInternationalJourneys());
    }

    public function testUpdateSectors()
    {
        $sectors = m::mock(Sectors::class);

        $entity = $this->createNewEntity();
        $entity->updateSectors($sectors);

        $this->assertSame(
            $sectors,
            $entity->getSectors()
        );
    }

    public function testClearSectors()
    {
        $sectors = m::mock(Sectors::class);

        $entity = $this->createNewEntity();
        $entity->setSectors($sectors);
        $entity->clearSectors();

        $this->assertNull($entity->getSectors());
    }

    public function testGetBusinessProcess()
    {
        $businessProcess = m::mock(RefData::class);

        $irhpPermitApplication = m::mock(IrhpPermitApplication::class);
        $irhpPermitApplication->shouldReceive(
            'getIrhpPermitWindow->getIrhpPermitStock->getBusinessProcess'
        )->once()->withNoArgs()->andReturn($businessProcess);

        $entity = $this->createNewEntity();
        $entity->addIrhpPermitApplications($irhpPermitApplication);

        $this->assertEquals($businessProcess, $entity->getBusinessProcess());
    }

    public function testGetBusinessProcessWithoutIrhpPermitApplication()
    {
        $entity = $this->createNewEntity();

        $this->assertNull($entity->getBusinessProcess());
    }

    public function testGetAnswerValueByQuestionId()
    {
        $answer = 'qanda answer';

        $applicationSteps = $this->getAnswerValueByQuestionIdApplicationSteps();

        $entity = m::mock(Entity::class)->makePartial();
        $entity->shouldReceive('getActiveApplicationPath->getApplicationSteps')
            ->withNoArgs()
            ->once()
            ->andReturn($applicationSteps);
        $entity->shouldReceive('getAnswer')
            ->with($applicationSteps[1], false)
            ->once()
            ->andReturn($answer);

        $this->assertEquals(
            $answer,
            $entity->getAnswerValueByQuestionId(40)
        );
    }

    public function testGetAnswerValueByQuestionIdNull()
    {
        $applicationSteps = $this->getAnswerValueByQuestionIdApplicationSteps();

        $entity = m::mock(Entity::class)->makePartial();
        $entity->shouldReceive('getActiveApplicationPath->getApplicationSteps')
            ->withNoArgs()
            ->once()
            ->andReturn($applicationSteps);

        $this->assertNull(
            $entity->getAnswerValueByQuestionId(50)
        );
    }

    private function getAnswerValueByQuestionIdApplicationSteps()
    {
        $applicationStep1 = m::mock(ApplicationStep::class);
        $applicationStep1->shouldReceive('getQuestion->getId')
            ->withNoArgs()
            ->andReturn(38);

        $applicationStep2 = m::mock(ApplicationStep::class);
        $applicationStep2->shouldReceive('getQuestion->getId')
            ->withNoArgs()
            ->andReturn(40);

        $applicationStep3 = m::mock(ApplicationStep::class);
        $applicationStep3->shouldReceive('getQuestion->getId')
            ->withNoArgs()
            ->andReturn(42);

        return new ArrayCollection([$applicationStep1, $applicationStep2, $applicationStep3]);
    }

    /**
     * @dataProvider dpTestHasCountryId
     */
    public function testHasCountryId($countryId, $expected)
    {
        $country1Id = 'FR';
        $country1 = m::mock(Country::class);
        $country1->shouldReceive('getId')
            ->andReturn($country1Id);

        $country2Id = 'RU';
        $country2 = m::mock(Country::class);
        $country2->shouldReceive('getId')
            ->andReturn($country2Id);

        $country3Id = 'DE';
        $country3 = m::mock(Country::class);
        $country3->shouldReceive('getId')
            ->andReturn($country3Id);

        $countries = new ArrayCollection([$country1, $country2, $country3]);
        $entity = $this->createNewEntity();
        $entity->updateCountries($countries);

        $this->assertEquals($expected, $entity->hasCountryId($countryId));
    }

    public function dpTestHasCountryId()
    {
        return [
            ['FR', true],
            ['RU', true],
            ['DE', true],
            ['ES', false],
        ];
    }

    public function updateCountries()
    {
        $arrayCollection = m::mock(ArrayCollection::class);

        $entity = $this->createNewEntity();
        $entity->updateCountries($arrayCollection);

        $this->assertSame(
            $arrayCollection,
            $entity->getCountrys()
        );
    }

    /**
     * @dataProvider dpTestGetPermitIntensityOfUse
     */
    public function testGetPermitIntensityOfUse($emissionsCategoryId, $expectedIntensityOfUse)
    {
        $irhpPermitApplication = m::mock(IrhpPermitApplication::class);
        $irhpPermitApplication->shouldReceive('getRequiredEuro5')
            ->andReturn(2);
        $irhpPermitApplication->shouldReceive('getRequiredEuro6')
            ->andReturn(5);

        $entity = m::mock(Entity::class)->makePartial()->shouldAllowMockingProtectedMethods();
        $entity->shouldReceive('getFirstIrhpPermitApplication')
            ->andReturn($irhpPermitApplication);
        $entity->shouldReceive('calculateTotalPermitsRequired')
            ->andReturn(7);
        $entity->shouldReceive('getAnswerValueByQuestionId')
            ->with(Question::QUESTION_ID_SHORT_TERM_ANNUAL_TRIPS_ABROAD)
            ->andReturn(35);

        $this->assertEquals(
            $expectedIntensityOfUse,
            $entity->getPermitIntensityOfUse($emissionsCategoryId)
        );
    }

    /**
     * @dataProvider dpTestGetPermitIntensityOfUse
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function testGetPermitIntensityOfUseZeroPermitsRequested($emissionsCategoryId, $expectedIntensityOfUse)
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Permit intensity of use cannot be calculated with zero number of permits');

        $irhpPermitApplication = m::mock(IrhpPermitApplication::class);
        $irhpPermitApplication->shouldReceive('getRequiredEuro5')
            ->andReturn(0);
        $irhpPermitApplication->shouldReceive('getRequiredEuro6')
            ->andReturn(0);

        $entity = m::mock(Entity::class)->makePartial()->shouldAllowMockingProtectedMethods();
        $entity->shouldReceive('getFirstIrhpPermitApplication')
            ->andReturn($irhpPermitApplication);
        $entity->shouldReceive('calculateTotalPermitsRequired')
            ->andReturn(0);
        $entity->shouldReceive('getAnswerValueByQuestionId')
            ->with(Question::QUESTION_ID_SHORT_TERM_ANNUAL_TRIPS_ABROAD)
            ->andReturn(0);

        $entity->getPermitIntensityOfUse($emissionsCategoryId);
    }

    public function testGetPermitIntensityOfUseBadEmissionsCategory()
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Unexpected emissionsCategoryId parameter for getPermitIntensityOfUse: xyz');

        $irhpPermitApplication = m::mock(IrhpPermitApplication::class);

        $entity = m::mock(Entity::class)->makePartial();
        $entity->shouldReceive('getFirstIrhpPermitApplication')
            ->andReturn($irhpPermitApplication);
        $entity->getPermitIntensityOfUse('xyz');
    }

    public function dpTestGetPermitIntensityOfUse()
    {
        return [
            [null, 5],
            [RefData::EMISSIONS_CATEGORY_EURO5_REF, 17.5],
            [RefData::EMISSIONS_CATEGORY_EURO6_REF, 7],
        ];
    }

    /**
     * @dataProvider dpTestGetPermitApplicationScore
     */
    public function testGetPermitApplicationScore(
        $emissionsCategoryId,
        $internationalJourneys,
        $expectedPermitApplicationScore
    ) {
        $intensityOfUse = 5;

        $entity = m::mock(Entity::class)->makePartial()->shouldAllowMockingProtectedMethods();
        $entity->shouldReceive('getPermitIntensityOfUse')
            ->with($emissionsCategoryId)
            ->andReturn($intensityOfUse);

        $refData = m::mock(RefData::class);
        $refData->shouldReceive('getId')
            ->andReturn($internationalJourneys);
        $entity->setInternationalJourneys($refData);

        $this->assertEquals(
            $expectedPermitApplicationScore,
            $entity->getPermitApplicationScore($emissionsCategoryId)
        );
    }

    public function dpTestGetPermitApplicationScore()
    {
        return [
            [null, RefData::INTER_JOURNEY_LESS_60, 1.5],
            [null, RefData::INTER_JOURNEY_60_90, 3.75],
            [null, RefData::INTER_JOURNEY_MORE_90, 5],
            [RefData::EMISSIONS_CATEGORY_EURO5_REF, RefData::INTER_JOURNEY_LESS_60, 1.5],
            [RefData::EMISSIONS_CATEGORY_EURO5_REF, RefData::INTER_JOURNEY_60_90, 3.75],
            [RefData::EMISSIONS_CATEGORY_EURO5_REF, RefData::INTER_JOURNEY_MORE_90, 5],
            [RefData::EMISSIONS_CATEGORY_EURO6_REF, RefData::INTER_JOURNEY_LESS_60, 1.5],
            [RefData::EMISSIONS_CATEGORY_EURO6_REF, RefData::INTER_JOURNEY_60_90, 3.75],
            [RefData::EMISSIONS_CATEGORY_EURO6_REF, RefData::INTER_JOURNEY_MORE_90, 5],
        ];
    }

    public function testGetCamelCaseEntityName()
    {
        $application = $this->createNewEntity();

        $this->assertEquals(
            'irhpApplication',
            $application->getCamelCaseEntityName()
        );
    }

    public function testGetEmailCommandLookup()
    {
        $expectedEmailCommandLookup = [
            ApplicationAcceptConsts::SUCCESS_LEVEL_NONE => SendEcmtShortTermUnsuccessful::class,
            ApplicationAcceptConsts::SUCCESS_LEVEL_PARTIAL => SendEcmtShortTermApsgPartSuccessful::class,
            ApplicationAcceptConsts::SUCCESS_LEVEL_FULL => SendEcmtShortTermSuccessful::class
        ];

        $irhpPermitType = m::mock(IrhpPermitType::class);
        $irhpPermitType->shouldReceive('isEcmtShortTerm')
            ->andReturn(true);

        $application = $this->createNewEntity();
        $application->setIrhpPermitType($irhpPermitType);

        $this->assertEquals(
            $expectedEmailCommandLookup,
            $application->getEmailCommandLookup()
        );
    }

    public function testGetEmailCommandLookupException()
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('getEmailCommandLookup is only applicable to ECMT short term');

        $irhpPermitType = m::mock(IrhpPermitType::class);
        $irhpPermitType->shouldReceive('isEcmtShortTerm')
            ->andReturn(false);

        $application = $this->createNewEntity();
        $application->setIrhpPermitType($irhpPermitType);

        $application->getEmailCommandLookup();
    }

    /**
     * @dataProvider dpProvideOutcomeNotificationType
     */
    public function testGetOutcomeNotificationType($source, $expectedNotificationType)
    {
        $sourceRefData = m::mock(RefData::class);
        $sourceRefData->shouldReceive('getId')
            ->andReturn($source);

        $entity = Entity::createNew(
            $sourceRefData,
            m::mock(RefData::class),
            m::mock(IrhpPermitType::class),
            m::mock(Licence::class)
        );

        $this->assertEquals(
            $expectedNotificationType,
            $entity->getOutcomeNotificationType()
        );
    }

    /**
     * Pass array of app statuses to make sure an exception is thrown
     *
     * @return array
     */
    public function dpProvideOutcomeNotificationType()
    {
        return [
            [IrhpInterface::SOURCE_SELFSERVE, ApplicationAcceptConsts::NOTIFICATION_TYPE_EMAIL],
            [IrhpInterface::SOURCE_INTERNAL, ApplicationAcceptConsts::NOTIFICATION_TYPE_MANUAL]
        ];
    }

    /**
     * @dataProvider dpProvideSuccessLevel
     */
    public function testGetSuccessLevel($permitsRequired, $permitsAwarded, $expectedSuccessLevel)
    {
        $entity = m::mock(Entity::class)->makePartial();

        $entity->shouldReceive('calculateTotalPermitsRequired')
            ->andReturn($permitsRequired);
        $entity->shouldReceive('getPermitsAwarded')
            ->andReturn($permitsAwarded);

        $this->assertEquals(
            $expectedSuccessLevel,
            $entity->getSuccessLevel()
        );
    }

    /**
     * Pass array of app statuses to make sure an exception is thrown
     *
     * @return array
     */
    public function dpProvideSuccessLevel()
    {
        return [
            [10,  1, ApplicationAcceptConsts::SUCCESS_LEVEL_PARTIAL],
            [10,  9, ApplicationAcceptConsts::SUCCESS_LEVEL_PARTIAL],
            [10,  0, ApplicationAcceptConsts::SUCCESS_LEVEL_NONE],
            [ 1,  0, ApplicationAcceptConsts::SUCCESS_LEVEL_NONE],
            [ 1,  1, ApplicationAcceptConsts::SUCCESS_LEVEL_FULL],
            [10, 10, ApplicationAcceptConsts::SUCCESS_LEVEL_FULL]
        ];
    }

    public function testProceedToUnsuccessful()
    {
        $entity = m::mock(Entity::class)->makePartial();
        $entity->shouldReceive('isUnderConsideration')
            ->andReturn(true);

        $unsuccessfulStatus = m::mock(RefData::class);
        $entity->proceedToUnsuccessful($unsuccessfulStatus);

        $this->assertSame(
            $unsuccessfulStatus,
            $entity->getStatus()
        );
    }

    public function testProceedToUnsuccessfulException()
    {
        $this->expectException(ForbiddenException::class);
        $this->expectExceptionMessage(
            'This application is not in the correct state to proceed to unsuccessful (current_status)'
        );

        $entity = m::mock(Entity::class)->makePartial();
        $entity->shouldReceive('isUnderConsideration')
            ->andReturn(false);

        $currentStatus = m::mock(RefData::class);
        $currentStatus->shouldReceive('getId')
            ->andReturn('current_status');
        $entity->setStatus($currentStatus);

        $entity->proceedToUnsuccessful(m::mock(RefData::class));
    }

    public function testProceedToAwaitingFee()
    {
        $entity = m::mock(Entity::class)->makePartial();
        $entity->shouldReceive('isUnderConsideration')
            ->andReturn(true);

        $awaitingFeeStatus = m::mock(RefData::class);
        $entity->proceedToAwaitingFee($awaitingFeeStatus);

        $this->assertSame(
            $awaitingFeeStatus,
            $entity->getStatus()
        );
    }

    public function testProceedToAwaitingFeeException()
    {
        $this->expectException(ForbiddenException::class);
        $this->expectExceptionMessage(
            'This application is not in the correct state to proceed to awaiting fee (current_status)'
        );

        $entity = m::mock(Entity::class)->makePartial();
        $entity->shouldReceive('isUnderConsideration')
            ->andReturn(false);

        $currentStatus = m::mock(RefData::class);
        $currentStatus->shouldReceive('getId')
            ->andReturn('current_status');
        $entity->setStatus($currentStatus);

        $entity->proceedToAwaitingFee(m::mock(RefData::class));
    }

    public function testGetPermitsAwarded()
    {
        $permitsAwarded = 14;

        $entity = m::mock(Entity::class)->makePartial();
        $entity->shouldReceive('isUnderConsideration')
            ->andReturn(true);
        $entity->shouldReceive('getFirstIrhpPermitApplication->countPermitsAwarded')
            ->andReturn($permitsAwarded);

        $this->assertSame(
            $permitsAwarded,
            $entity->getPermitsAwarded()
        );
    }

    public function testGetPermitsAwardedException()
    {
        $this->expectException(ForbiddenException::class);
        $this->expectExceptionMessage(
            'This application is not in the correct state to return permits awarded (current_status)'
        );

        $entity = m::mock(Entity::class)->makePartial();
        $entity->shouldReceive('isUnderConsideration')
            ->andReturn(false);

        $currentStatus = m::mock(RefData::class);
        $currentStatus->shouldReceive('getId')
            ->andReturn('current_status');
        $entity->setStatus($currentStatus);

        $entity->getPermitsAwarded(m::mock(RefData::class));
    }

    /**
     * @dataProvider dpGetIntensityOfUseWarningThreshold
     */
    public function testGetIntensityOfUseWarningThreshold($requiredEuro5, $requiredEuro6, $expectedThreshold)
    {
        $irhpPermitType = m::mock(IrhpPermitType::class);
        $irhpPermitType->shouldReceive('isEcmtShortTerm')
            ->andReturn(true);

        $irhpPermitApplication = m::mock(IrhpPermitApplication::class);
        $irhpPermitApplication->shouldReceive('getRequiredEuro5')
            ->andReturn($requiredEuro5);
        $irhpPermitApplication->shouldReceive('getRequiredEuro6')
            ->andReturn($requiredEuro6);

        $application = $this->createNewEntity();
        $application->setIrhpPermitType($irhpPermitType);
        $application->addIrhpPermitApplications($irhpPermitApplication);

        $this->assertEquals(
            $expectedThreshold,
            $application->getIntensityOfUseWarningThreshold()
        );
    }

    public function dpGetIntensityOfUseWarningThreshold()
    {
        return [
            [5, 8, 800],
            [4, 2, 400],
        ];
    }

    public function testGetIntensityOfUseWarningThresholdException()
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('getIntensityOfUseWarningThreshold is only applicable to ECMT short term');

        $irhpPermitType = m::mock(IrhpPermitType::class);
        $irhpPermitType->shouldReceive('isEcmtShortTerm')
            ->andReturn(false);

        $application = $this->createNewEntity();
        $application->setIrhpPermitType($irhpPermitType);

        $application->getIntensityOfUseWarningThreshold();
    }

    /**
     * @dataProvider dpGetAppSubmittedEmailCommand
     */
    public function testGetAppSubmittedEmailCommand($isEcmtShortTerm, $businessProcessId, $expectedCommand)
    {
        $irhpPermitType = m::mock(IrhpPermitType::class);
        $irhpPermitType->shouldReceive('isEcmtShortTerm')
            ->andReturn($isEcmtShortTerm);

        $application = m::mock(Entity::class)->makePartial();
        $application->shouldReceive('getBusinessProcess->getId')
            ->withNoArgs()
            ->andReturn($businessProcessId);

        $application->setIrhpPermitType($irhpPermitType);

        $this->assertEquals(
            $expectedCommand,
            $application->getAppSubmittedEmailCommand()
        );
    }

    public function dpGetAppSubmittedEmailCommand()
    {
        return [
            [true, RefData::BUSINESS_PROCESS_APG, null],
            [true, RefData::BUSINESS_PROCESS_APGG, null],
            [true, RefData::BUSINESS_PROCESS_APSG, SendEcmtShortTermAppSubmitted::class],
            [false, RefData::BUSINESS_PROCESS_APG, null],
            [false, RefData::BUSINESS_PROCESS_APGG, null],
            [false, RefData::BUSINESS_PROCESS_APSG, null],
        ];
    }

    public function testGetAllocationMode()
    {
        $allocationMode = 'ALLOCATION_MODE';

        $irhpPermitStock = m::mock(IrhpPermitStock::class);
        $irhpPermitStock->shouldReceive('getAllocationMode')
            ->andReturn($allocationMode);

        $this->sut->shouldReceive('getAssociatedStock')
            ->andReturn($irhpPermitStock);

        $this->assertEquals(
            $allocationMode,
            $this->sut->getAllocationMode()
        );
    }

    /**
     * @dataProvider dpShouldAllocatePermitsOnSubmission
     */
    public function testShouldAllocatePermitsOnSubmission($businessProcessId, $expected)
    {
        $businessProcess = m::mock(RefData::class);
        $businessProcess->shouldReceive('getId')
            ->andReturn($businessProcessId);

        $this->sut->shouldReceive('getBusinessProcess')
            ->withNoArgs()
            ->andReturn($businessProcess);

        $this->assertEquals(
            $expected,
            $this->sut->shouldAllocatePermitsOnSubmission()
        );
    }

    /**
     * @dataProvider dpUpdateChecked
     */
    public function testUpdateChecked($checked)
    {
        $this->sut->updateChecked($checked);
        $this->assertEquals($checked, $this->sut->getChecked());
    }

    public function dpUpdateChecked()
    {
        return [
            [true],
            [false],
        ];
    }

    public function dpShouldAllocatePermitsOnSubmission()
    {
        return [
            [RefData::BUSINESS_PROCESS_APG, true],
            [RefData::BUSINESS_PROCESS_APGG, false],
            [RefData::BUSINESS_PROCESS_APSG, false],
        ];
    }

    /**
     * @dataProvider dpGetSubmissionTaskDescription
     */
    public function testGetSubmissionTaskDescription($irhpPermitTypeId, $expectedTaskDescription)
    {
        $irhpPermitType = m::mock(IrhpPermitType::class);
        $irhpPermitType->shouldReceive('getId')
            ->andReturn($irhpPermitTypeId);

        $this->sut->setIrhpPermitType($irhpPermitType);

        $this->assertEquals(
            $expectedTaskDescription,
            $this->sut->getSubmissionTaskDescription()
        );
    }

    public function dpGetSubmissionTaskDescription()
    {
        return [
            [
                IrhpPermitType::IRHP_PERMIT_TYPE_ID_ECMT_SHORT_TERM,
                Task::TASK_DESCRIPTION_SHORT_TERM_ECMT_RECEIVED
            ],
            [
                IrhpPermitType::IRHP_PERMIT_TYPE_ID_ECMT_REMOVAL,
                Task::TASK_DESCRIPTION_ECMT_INTERNATIONAL_REMOVALS_RECEIVED
            ],
            [
                IrhpPermitType::IRHP_PERMIT_TYPE_ID_BILATERAL,
                Task::TASK_DESCRIPTION_BILATERAL_RECEIVED
            ],
            [
                IrhpPermitType::IRHP_PERMIT_TYPE_ID_MULTILATERAL,
                Task::TASK_DESCRIPTION_MULTILATERAL_RECEIVED
            ],
            [
                IrhpPermitType::IRHP_PERMIT_TYPE_ID_CERT_ROADWORTHINESS_VEHICLE,
                Task::TASK_DESCRIPTION_CERT_ROADWORTHINESS_RECEIVED
            ],
            [
                IrhpPermitType::IRHP_PERMIT_TYPE_ID_CERT_ROADWORTHINESS_TRAILER,
                Task::TASK_DESCRIPTION_CERT_ROADWORTHINESS_RECEIVED
            ],
        ];
    }

    public function testGetSubmissionTaskDescriptionException()
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('No submission task description defined for permit type foo');

        $irhpPermitType = m::mock(IrhpPermitType::class);
        $irhpPermitType->shouldReceive('getId')
            ->andReturn('foo');

        $this->sut->setIrhpPermitType($irhpPermitType);
        $this->sut->getSubmissionTaskDescription();
    }

    /**
     * @dataProvider dpGetSubmissionStatus
     */
    public function testGetSubmissionStatus($businessProcessId, $expectedStatus)
    {
        $businessProcess = m::mock(RefData::class);
        $businessProcess->shouldReceive('getId')
            ->andReturn($businessProcessId);

        $this->sut->shouldReceive('getBusinessProcess')
            ->withNoArgs()
            ->andReturn($businessProcess);

        $this->assertEquals(
            $expectedStatus,
            $this->sut->getSubmissionStatus()
        );
    }

    public function dpGetSubmissionStatus()
    {
        return [
            [RefData::BUSINESS_PROCESS_AG, IrhpInterface::STATUS_VALID],
            [RefData::BUSINESS_PROCESS_APG, IrhpInterface::STATUS_ISSUING],
            [RefData::BUSINESS_PROCESS_APGG, IrhpInterface::STATUS_UNDER_CONSIDERATION],
            [RefData::BUSINESS_PROCESS_APSG, IrhpInterface::STATUS_UNDER_CONSIDERATION],
        ];
    }

    public function testGetSubmissionStatusException()
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('No submission status defined for business process foo');

        $businessProcess = m::mock(RefData::class);
        $businessProcess->shouldReceive('getId')
            ->andReturn('foo');

        $this->sut->shouldReceive('getBusinessProcess')
            ->withNoArgs()
            ->andReturn($businessProcess);

        $this->sut->getSubmissionStatus();
    }

    public function testGetCandidatePermitCreationModeMultiStock()
    {
        $this->sut->shouldReceive('isMultiStock')
            ->withNoArgs()
            ->andReturn(true);

        $this->assertEquals(
            IrhpPermitStock::CANDIDATE_MODE_NONE,
            $this->sut->getCandidatePermitCreationMode()
        );
    }

    public function testGetCandidatePermitCreationModeNotMultiStock()
    {
        $creationMode = 'CREATION_MODE';

        $this->sut->shouldReceive('isMultiStock')
            ->withNoArgs()
            ->andReturn(false);

        $this->sut->shouldReceive('getAssociatedStock->getCandidatePermitCreationMode')
            ->withNoArgs()
            ->andReturn($creationMode);

        $this->assertEquals(
            $creationMode,
            $this->sut->getCandidatePermitCreationMode()
        );
    }

    /**
     * @dataProvider dpRequiresPreAllocationCheck
     */
    public function testRequiresPreAllocationCheck($isEcmtShortTerm, $expected)
    {
        $irhpPermitType = m::mock(IrhpPermitType::class);
        $irhpPermitType->shouldReceive('isEcmtShortTerm')
            ->withNoArgs()
            ->andReturn($isEcmtShortTerm);

        $this->sut->setIrhpPermitType($irhpPermitType);

        $this->assertEquals(
            $expected,
            $this->sut->requiresPreAllocationCheck()
        );
    }

    public function dpRequiresPreAllocationCheck()
    {
        return [
            [true, true],
            [false, false],
        ];
    }

    public function testFetchOpenSubmissionTask()
    {
        $this->sut->shouldReceive('getSubmissionTaskDescription')
            ->withNoArgs()
            ->andReturn('submission task');

        $task1 = $this->createMockTask('description 1', 'N', Task::CATEGORY_PERMITS, Task::SUBCATEGORY_FEE_DUE);
        $task2 = $this->createMockTask('submission task', 'Y', Task::CATEGORY_PERMITS, Task::SUBCATEGORY_APPLICATION);
        $task3 = $this->createMockTask('submission task', 'N', Task::CATEGORY_BUS, Task::SUBCATEGORY_APPLICATION);
        $task4 = $this->createMockTask('submission task', 'N', Task::CATEGORY_PERMITS, Task::SUBCATEGORY_FEE_DUE);
        $task5 = $this->createMockTask('submission task', 'N', Task::CATEGORY_PERMITS, Task::SUBCATEGORY_APPLICATION);
        $task6 = $this->createMockTask('description 2', 'N', Task::CATEGORY_PERMITS, Task::SUBCATEGORY_APPLICATION);

        $this->sut->setTasks(
            new ArrayCollection([$task1, $task2, $task3, $task4, $task5, $task6])
        );

        $this->assertSame(
            $task5,
            $this->sut->fetchOpenSubmissionTask()
        );
    }

    public function testFetchOpenSubmissionTaskNull()
    {
        $this->sut->shouldReceive('getSubmissionTaskDescription')
            ->withNoArgs()
            ->andReturn('submission task');

        $task1 = $this->createMockTask('description 1', 'N', Task::CATEGORY_PERMITS, Task::SUBCATEGORY_FEE_DUE);
        $task2 = $this->createMockTask('submission task', 'Y', Task::CATEGORY_PERMITS, Task::SUBCATEGORY_APPLICATION);
        $task3 = $this->createMockTask('submission task', 'N', Task::CATEGORY_BUS, Task::SUBCATEGORY_APPLICATION);
        $task4 = $this->createMockTask('submission task', 'N', Task::CATEGORY_PERMITS, Task::SUBCATEGORY_FEE_DUE);
        $task5 = $this->createMockTask('description 2', 'N', Task::CATEGORY_PERMITS, Task::SUBCATEGORY_APPLICATION);

        $this->sut->setTasks(
            new ArrayCollection([$task1, $task2, $task3, $task4, $task5])
        );

        $this->assertNull(
            $this->sut->fetchOpenSubmissionTask()
        );
    }

    private function createMockTask($description, $isClosed, $categoryId, $subcategoryId)
    {
        $task = m::mock(Task::class);
        $task->shouldReceive('getDescription')
            ->withNoArgs()
            ->andReturn($description);
        $task->shouldReceive('getIsClosed')
            ->withNoArgs()
            ->andReturn($isClosed);
        $task->shouldReceive('getCategory->getId')
            ->withNoArgs()
            ->andReturn($categoryId);
        $task->shouldReceive('getSubcategory->getId')
            ->withNoArgs()
            ->andReturn($subcategoryId);

        return $task;
    }

    /**
     * @dataProvider dpCanBeRevivedFromWithdrawn
     */
    public function testCanBeRevivedFromWithdrawn($withdrawReason, $inScope, $businessProcessId, $expected)
    {
        $withdrawReasonRefData = m::mock(RefData::class);
        $withdrawReasonRefData->shouldReceive('getId')
            ->withNoArgs()
            ->andReturn($withdrawReason);

        $this->sut->setWithdrawReason($withdrawReasonRefData);

        $this->sut->shouldReceive('isWithdrawn')
            ->withNoArgs()
            ->andReturn(true);

        $this->sut->shouldReceive('getInScope')
            ->withNoArgs()
            ->andReturn($inScope);

        $businessProcessRefData = m::mock(RefData::class);
        $businessProcessRefData->shouldReceive('getId')
            ->withNoArgs()
            ->andReturn($businessProcessId);

        $this->sut->shouldReceive('getBusinessProcess')
            ->withNoArgs()
            ->andReturn($businessProcessRefData);

        $this->assertEquals(
            $expected,
            $this->sut->canBeRevivedFromWithdrawn()
        );
    }

    public function dpCanBeRevivedFromWithdrawn()
    {
        return [
            [
                WithdrawableInterface::WITHDRAWN_REASON_UNPAID,
                true,
                RefData::BUSINESS_PROCESS_APSG,
                true,
            ],
            [
                WithdrawableInterface::WITHDRAWN_REASON_DECLINED,
                true,
                RefData::BUSINESS_PROCESS_APSG,
                true,
            ],
            [
                WithdrawableInterface::WITHDRAWN_REASON_NOTSUCCESS,
                true,
                RefData::BUSINESS_PROCESS_APSG,
                false,
            ],
            [
                WithdrawableInterface::WITHDRAWN_REASON_DECLINED,
                false,
                RefData::BUSINESS_PROCESS_APSG,
                false,
            ],
            [
                WithdrawableInterface::WITHDRAWN_REASON_DECLINED,
                true,
                RefData::BUSINESS_PROCESS_APGG,
                false,
            ],
        ];
    }

    /**
     * @dataProvider dpCanBeRevivedFromWithdrawnNotWithdrawn
     */
    public function testCanBeRevivedFromWithdrawnNotWithdrawn($inScope, $businessProcessId)
    {
        $this->sut->setWithdrawReason(null);

        $this->sut->shouldReceive('isWithdrawn')
            ->withNoArgs()
            ->andReturn(false);

        $this->sut->shouldReceive('getInScope')
            ->withNoArgs()
            ->andReturn($inScope);

        $businessProcessRefData = m::mock(RefData::class);
        $businessProcessRefData->shouldReceive('getId')
            ->withNoArgs()
            ->andReturn($businessProcessId);

        $this->sut->shouldReceive('getBusinessProcess')
            ->withNoArgs()
            ->andReturn($businessProcessRefData);

        $this->assertFalse(
            $this->sut->canBeRevivedFromWithdrawn()
        );
    }

    public function dpCanBeRevivedFromWithdrawnNotWithdrawn()
    {
        return [
            [true, RefData::BUSINESS_PROCESS_APGG],
            [false, RefData::BUSINESS_PROCESS_APSG],
            [true, RefData::BUSINESS_PROCESS_APGG],
            [false, RefData::BUSINESS_PROCESS_APSG],
        ];
    }

    public function testReviveFromWithdrawn()
    {
        $withdrawnStatus = m::mock(RefData::class);
        $withdrawnDate = m::mock(DateTime::class);

        $underConsiderationStatus = m::mock(RefData::class);

        $this->sut->setStatus($withdrawnStatus);
        $this->sut->setWithdrawReason(WithdrawableInterface::WITHDRAWN_REASON_DECLINED);
        $this->sut->setWithdrawnDate = $withdrawnDate;

        $this->sut->shouldReceive('canBeRevivedFromWithdrawn')
            ->withNoArgs()
            ->andReturn(true);

        $this->sut->reviveFromWithdrawn($underConsiderationStatus);

        $this->assertSame($underConsiderationStatus, $this->sut->getStatus());
        $this->assertNull($this->sut->getWithdrawReason());
        $this->assertNull($this->sut->getWithdrawnDate());
    }

    public function testReviveFromWithdrawnNoBusinessProcess()
    {
        $this->sut->expects()->getBusinessProcess()
            ->withNoArgs()
            ->andReturnNull();

        self::assertFalse($this->sut->canBeRevivedFromWithdrawn());
    }

    public function testReviveFromWithdrawnException()
    {
        $this->expectException(ForbiddenException::class);
        $this->expectExceptionMessage('Unable to revive this application from a withdrawn state');

        $this->sut->shouldReceive('canBeRevivedFromWithdrawn')
            ->withNoArgs()
            ->andReturn(false);

        $this->sut->reviveFromWithdrawn(
            m::mock(RefData::class)
        );
    }

    /**
     * @dataProvider dpCanBeRevivedFromUnsuccessful
     */
    public function testCanBeRevivedFromUnsuccessful($businessProcessId, $statusId, $expected)
    {
        $statusRefData = m::mock(RefData::class);
        $statusRefData->shouldReceive('getId')
            ->withNoArgs()
            ->andReturn($statusId);

        $this->sut->setStatus($statusRefData);

        $businessProcessRefData = m::mock(RefData::class);
        $businessProcessRefData->shouldReceive('getId')
            ->withNoArgs()
            ->andReturn($businessProcessId);

        $this->sut->shouldReceive('getBusinessProcess')
            ->withNoArgs()
            ->andReturn($businessProcessRefData);

        $this->assertEquals(
            $expected,
            $this->sut->canBeRevivedFromUnsuccessful()
        );
    }

    public function dpCanBeRevivedFromUnsuccessful()
    {
        return [
            [
                RefData::BUSINESS_PROCESS_APSG,
                IrhpInterface::STATUS_CANCELLED,
                false
            ],
            [
                RefData::BUSINESS_PROCESS_APSG,
                IrhpInterface::STATUS_NOT_YET_SUBMITTED,
                false
            ],
            [
                RefData::BUSINESS_PROCESS_APSG,
                IrhpInterface::STATUS_UNDER_CONSIDERATION,
                false
            ],
            [
                RefData::BUSINESS_PROCESS_APSG,
                IrhpInterface::STATUS_WITHDRAWN,
                false
            ],
            [
                RefData::BUSINESS_PROCESS_APSG,
                IrhpInterface::STATUS_AWAITING_FEE,
                false
            ],
            [
                RefData::BUSINESS_PROCESS_APSG,
                IrhpInterface::STATUS_FEE_PAID,
                false
            ],
            [
                RefData::BUSINESS_PROCESS_APSG,
                IrhpInterface::STATUS_UNSUCCESSFUL,
                true
            ],
            [
                RefData::BUSINESS_PROCESS_APSG,
                IrhpInterface::STATUS_ISSUED,
                false
            ],
            [
                RefData::BUSINESS_PROCESS_APSG,
                IrhpInterface::STATUS_ISSUING,
                false
            ],
            [
                RefData::BUSINESS_PROCESS_APSG,
                IrhpInterface::STATUS_VALID,
                false
            ],
            [
                RefData::BUSINESS_PROCESS_APSG,
                IrhpInterface::STATUS_EXPIRED,
                false
            ],
            [
                RefData::BUSINESS_PROCESS_APGG,
                IrhpInterface::STATUS_CANCELLED,
                false
            ],
            [
                RefData::BUSINESS_PROCESS_APGG,
                IrhpInterface::STATUS_NOT_YET_SUBMITTED,
                false
            ],
            [
                RefData::BUSINESS_PROCESS_APGG,
                IrhpInterface::STATUS_UNDER_CONSIDERATION,
                false
            ],
            [
                RefData::BUSINESS_PROCESS_APGG,
                IrhpInterface::STATUS_WITHDRAWN,
                false
            ],
            [
                RefData::BUSINESS_PROCESS_APGG,
                IrhpInterface::STATUS_AWAITING_FEE,
                false
            ],
            [
                RefData::BUSINESS_PROCESS_APGG,
                IrhpInterface::STATUS_FEE_PAID,
                false
            ],
            [
                RefData::BUSINESS_PROCESS_APGG,
                IrhpInterface::STATUS_UNSUCCESSFUL,
                false
            ],
            [
                RefData::BUSINESS_PROCESS_APGG,
                IrhpInterface::STATUS_ISSUED,
                false
            ],
            [
                RefData::BUSINESS_PROCESS_APGG,
                IrhpInterface::STATUS_ISSUING,
                false
            ],
            [
                RefData::BUSINESS_PROCESS_APGG,
                IrhpInterface::STATUS_VALID,
                false
            ],
            [
                RefData::BUSINESS_PROCESS_APGG,
                IrhpInterface::STATUS_EXPIRED,
                false
            ],
        ];
    }

    public function testReviveFromUnsuccessfulNoBusinessProcess()
    {
        $this->sut->expects()->getBusinessProcess()
            ->withNoArgs()
            ->andReturnNull();

        self::assertFalse($this->sut->canBeRevivedFromUnsuccessful());
    }

    public function testReviveFromUnsuccessful()
    {
        $underConsiderationStatus = m::mock(RefData::class);

        $this->sut->setStatus(m::mock(RefData::class));
        $this->sut->shouldReceive('canBeRevivedFromUnsuccessful')
            ->withNoArgs()
            ->andReturnTrue();

        $this->sut->reviveFromUnsuccessful($underConsiderationStatus);

        $this->assertSame(
            $underConsiderationStatus,
            $this->sut->getStatus()
        );
    }

    public function testReviveFromUnsuccessfulException()
    {
        $this->expectException(ForbiddenException::class);
        $this->expectExceptionMessage('Unable to revive this application from an unsuccessful state');

        $this->sut->shouldReceive('canBeRevivedFromUnsuccessful')
            ->withNoArgs()
            ->andReturnFalse();

        $this->sut->reviveFromUnsuccessful(
            m::mock(RefData::class)
        );
    }
}
