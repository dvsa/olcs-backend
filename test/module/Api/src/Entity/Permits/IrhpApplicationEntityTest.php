<?php

namespace Dvsa\OlcsTest\Api\Entity\Permits;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Dvsa\Olcs\Api\Domain\Exception\ForbiddenException;
use Dvsa\Olcs\Api\Domain\Exception\RuntimeException;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitStock;
use Dvsa\Olcs\Api\Entity\WithdrawableInterface;
use Dvsa\OlcsTest\Api\Entity\Abstracts\EntityTester;
use Dvsa\Olcs\Api\Entity\Fee\Fee;
use Dvsa\Olcs\Api\Entity\Fee\FeeType;
use Dvsa\Olcs\Api\Entity\Generic\Answer;
use Dvsa\Olcs\Api\Entity\Generic\ApplicationPath;
use Dvsa\Olcs\Api\Entity\Generic\ApplicationStep;
use Dvsa\Olcs\Api\Entity\Generic\Question;
use Dvsa\Olcs\Api\Entity\Generic\QuestionText;
use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Dvsa\Olcs\Api\Entity\Organisation\Organisation;
use Dvsa\Olcs\Api\Entity\Permits\IrhpApplication as Entity;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitApplication;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitType;
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
     * @var Entity
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
            ->shouldReceive('canBeSubmitted')
            ->once()
            ->withNoArgs()
            ->andReturn(false)
            ->shouldReceive('canBeUpdated')
            ->once()
            ->withNoArgs()
            ->andReturn(true)
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
            ->andReturn([]);

        $this->assertSame(
            [
                'applicationRef' => 'appRef',
                'canBeCancelled' => false,
                'canBeWithdrawn' => false,
                'canBeSubmitted' => false,
                'canBeUpdated' => true,
                'hasOutstandingFees' => false,
                'outstandingFeeAmount' => 0,
                'sectionCompletion' => [],
                'hasCheckedAnswers' => false,
                'hasMadeDeclaration' => false,
                'isNotYetSubmitted' => true,
                'isValid' => false,
                'isFeePaid' => false,
                'isIssueInProgress' => false,
                'isAwaitingFee' => false,
                'isUnderConsideration' => false,
                'isReadyForNoOfPermits' => false,
                'isCancelled' => false,
                'isWithdrawn' => false,
                'isBilateral' => false,
                'isMultilateral' => false,
                'canCheckAnswers' => true,
                'canMakeDeclaration' => true,
                'permitsRequired' => 0,
                'canUpdateCountries' => true,
                'questionAnswerData' => [],
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
            [IrhpInterface::STATUS_DECLINED, false],
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
            [IrhpInterface::STATUS_DECLINED, false],
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
            [IrhpInterface::STATUS_DECLINED, false],
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
            [IrhpInterface::STATUS_DECLINED, false],
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
            [IrhpInterface::STATUS_DECLINED, false],
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
            [IrhpInterface::STATUS_ISSUING, false],
            [IrhpInterface::STATUS_VALID, false],
            [IrhpInterface::STATUS_DECLINED, false],
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
            [IrhpInterface::STATUS_DECLINED],
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
            [IrhpInterface::STATUS_DECLINED, false],
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
            [IrhpInterface::STATUS_DECLINED, false],
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
            [IrhpInterface::STATUS_DECLINED, false],
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
            [IrhpInterface::STATUS_DECLINED, false],
        ];
    }

    /**
     * Tests withdrawal of an application
     */
    public function testWithdraw()
    {
        $entity = $this->createNewEntity(null, new RefData(IrhpInterface::STATUS_UNDER_CONSIDERATION));
        $entity->withdraw(
            new RefData(IrhpInterface::STATUS_WITHDRAWN),
            new RefData(WithdrawableInterface::WITHDRAWN_REASON_BY_USER)
        );
        $this->assertEquals(IrhpInterface::STATUS_WITHDRAWN, $entity->getStatus()->getId());
        $this->assertEquals(WithdrawableInterface::WITHDRAWN_REASON_BY_USER, $entity->getWithdrawReason()->getId());
        $this->assertEquals(date('Y-m-d'), $entity->getWithdrawnDate()->format('Y-m-d'));
    }

    /**
     * @dataProvider dpWithdrawException
     */
    public function testWithdrawException($status)
    {
        $this->expectException(ForbiddenException::class);
        $this->expectExceptionMessage(Entity::ERR_CANT_WITHDRAW);
        $entity = $this->createNewEntity(null, new RefData($status));
        $entity->withdraw(
            new RefData(IrhpInterface::STATUS_WITHDRAWN),
            new RefData(WithdrawableInterface::WITHDRAWN_REASON_BY_USER)
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
            [IrhpInterface::STATUS_CANCELLED],
            [IrhpInterface::STATUS_NOT_YET_SUBMITTED],
            [IrhpInterface::STATUS_WITHDRAWN],
            [IrhpInterface::STATUS_AWAITING_FEE],
            [IrhpInterface::STATUS_FEE_PAID],
            [IrhpInterface::STATUS_UNSUCCESSFUL],
            [IrhpInterface::STATUS_ISSUED],
            [IrhpInterface::STATUS_ISSUING],
            [IrhpInterface::STATUS_VALID],
            [IrhpInterface::STATUS_DECLINED],
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
    public function testIsMultilateral($isMultilateral)
    {
        $irhpPermitType = m::mock(IrhpPermitType::class);
        $irhpPermitType->shouldReceive('isMultilateral')->once()->withNoArgs()->andReturn($isMultilateral);
        $entity = $this->createNewEntity(null, null, $irhpPermitType);
        $this->assertEquals($isMultilateral, $entity->isMultilateral());
    }

    public function trueOrFalseProvider()
    {
        return [
            [true],
            [false],
        ];
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
            [IrhpInterface::STATUS_DECLINED, false]
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
            [IrhpInterface::STATUS_UNDER_CONSIDERATION, false],
            [IrhpInterface::STATUS_WITHDRAWN, false],
            [IrhpInterface::STATUS_AWAITING_FEE, false],
            [IrhpInterface::STATUS_FEE_PAID, false],
            [IrhpInterface::STATUS_UNSUCCESSFUL, false],
            [IrhpInterface::STATUS_ISSUED, false],
            [IrhpInterface::STATUS_ISSUING, false],
            [IrhpInterface::STATUS_VALID, false],
            [IrhpInterface::STATUS_DECLINED, false],
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
     * @dataProvider dpTestCanBeSubmitted
     */
    public function testCanBeSubmitted($isNotYetSubmitted, $allSectionsCompleted, $canMakeIrhpApplication, $expected)
    {
        $irhpPermitType = m::mock(IrhpPermitType::class);

        $licence = m::mock(Licence::class);
        $licence->shouldReceive('canMakeIrhpApplication')
            ->with($irhpPermitType, $this->sut)
            ->andReturn($canMakeIrhpApplication);

        $this->sut->shouldReceive('isNotYetSubmitted')
            ->andReturn($isNotYetSubmitted)
            ->shouldReceive('getSectionCompletion')
            ->andReturn(['allCompleted' => $allSectionsCompleted])
            ->shouldReceive('getLicence')
            ->andReturn($licence)
            ->shouldReceive('getIrhpPermitType')
            ->andReturn($irhpPermitType);

        $this->assertSame($expected, $this->sut->canBeSubmitted());
    }

    public function dpTestCanBeSubmitted()
    {
        return [
            'already active application' => [
                'isNotYetSubmitted' => false,
                'allSectionsCompleted' => false,
                'canMakeIrhpApplication' => false,
                'expected' => false,
            ],
            'some incomplete sections' => [
                'isNotYetSubmitted' => true,
                'allSectionsCompleted' => false,
                'canMakeIrhpApplication' => false,
                'expected' => false,
            ],
            'cannot make IRHP application' => [
                'isNotYetSubmitted' => true,
                'allSectionsCompleted' => true,
                'canMakeIrhpApplication' => false,
                'expected' => false,
            ],
            'can be submitted' => [
                'isNotYetSubmitted' => true,
                'allSectionsCompleted' => true,
                'canMakeIrhpApplication' => true,
                'expected' => true,
            ],
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
                'expected' => false,
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
                'expected' => false,
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
                'expected' => false,
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
                'expected' => false,
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
                'expected' => false,
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
                'expected' => false,
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
                'expected' => false,
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
        $this->assertEmpty($entity->getIrhpPermitApplications());

        $entity->setIrhpPermitApplications(
            new ArrayCollection(
                [
                    0 => m::mock(IrhpPermitApplication::class),
                    1 => m::mock(IrhpPermitApplication::class),
                ]
            )
        );
        $entity->setCheckedAnswers(true);
        $entity->setDeclaration(true);

        $this->assertTrue($entity->hasCheckedAnswers());
        $this->assertTrue($entity->hasMadeDeclaration());
        $this->assertNotEmpty($entity->getIrhpPermitApplications());

        $entity
            ->shouldReceive('canBeUpdated')
            ->andReturn(true);

        $entity->clearAnswers();

        $this->assertFalse($entity->hasCheckedAnswers());
        $this->assertFalse($entity->hasMadeDeclaration());
        $this->assertEmpty($entity->getIrhpPermitApplications());
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

        $entity->submit($status);
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
            'This application is not in the correct state to proceed to valid (permit_app_declined)'
        );

        $oldStatus = m::mock(RefData::class);
        $oldStatus->shouldReceive('getId')
            ->andReturn('permit_app_declined');

        $entity = m::mock(Entity::class)->makePartial();
        $entity->setStatus($oldStatus);
        $entity->shouldReceive('isIssueInProgress')
            ->andReturn(false);

        $entity->proceedToValid(m::mock(RefData::class));
    }

    public function testGetQuestionAnswerBilateral()
    {
        $licNo = 'OB1234567';

        $country1 = 'country1';
        $country2 = 'country2';
        $joinedCountries = 'country1, country2';

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
            ],
            [
                'question' => 'permits.irhp.countries.transporting',
                'answer' =>  $joinedCountries,
            ],
            [
                'question' => 'permits.snapshot.number.required',
                'answer' =>  10,
            ],
            [
                'question' => $country1 . ' for ' . $stock1ValidityYear,
                'answer' =>  $stock1RequiredPermits,
            ],
            [
                'question' => $country2 . ' for ' . $stock2ValidityYear,
                'answer' =>  $stock2RequiredPermits,
            ],
            [
                'question' => $country2 . ' for ' . $stock3ValidityYear,
                'answer' =>  $stock3RequiredPermits,
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
            ],
            [
                'question' => 'permits.snapshot.number.required',
                'answer' =>  10,
            ],
            [
                'question' => 'For ' . $stock1ValidityYear,
                'answer' =>  $stock1RequiredPermits,
            ],
            [
                'question' => 'For ' . $stock2ValidityYear,
                'answer' =>  $stock2RequiredPermits,
            ],
            [
                'question' => 'For ' . $stock3ValidityYear,
                'answer' =>  $stock3RequiredPermits,
            ],
        ];

        $this->assertEquals($data, $entity->getQuestionAnswerData());
    }

    public function testGetQuestionAnswerDataWithoutActiveApplicationPath()
    {
        $licNo = 'ABC123';

        $expected = [
            [
                'section' => 'licence',
                'slug' => 'custom-licence',
                'questionShort' => 'section.name.application/licence',
                'question' => 'section.name.application/licence',
                'answer' => $licNo,
                'status' => SectionableInterface::SECTION_COMPLETION_COMPLETED,
            ],
            [
                'section' => 'checkedAnswers',
                'slug' => 'custom-check-answers',
                'questionShort' => 'section.name.application/check-answers',
                'question' => 'section.name.application/check-answers',
                'answer' => null,
                'status' => SectionableInterface::SECTION_COMPLETION_NOT_STARTED,
            ],
            [
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

        $irhpPermitType = m::mock(IrhpPermitType::class);
        $irhpPermitType->shouldReceive('isBilateral')->once()->withNoArgs()->andReturn(false);
        $irhpPermitType->shouldReceive('isMultilateral')->once()->withNoArgs()->andReturn(false);
        $irhpPermitType->shouldReceive('getActiveApplicationPath')->once()->with($createdOn)->andReturn(null);

        $entity = $this->createNewEntity(null, null, $irhpPermitType, $licence);
        $entity->setCreatedOn($createdOn);

        $this->assertEquals($expected, $entity->getQuestionAnswerData());
    }

    /**
     * @dataProvider dpGetQuestionAnswerDataWithActiveApplicationPath
     */
    public function testGetQuestionAnswerDataWithActiveApplicationPath($data, $applicationSteps, $expected)
    {
        $licence = m::mock(Licence::class);
        $licence->shouldReceive('getLicNo')->once()->withNoArgs()->andReturn($data['licNo']);

        $applicationPath = m::mock(ApplicationPath::class);
        $applicationPath->shouldReceive('getApplicationSteps')->once()->withNoArgs()->andReturn($applicationSteps);

        $irhpPermitType = m::mock(IrhpPermitType::class);
        $irhpPermitType->shouldReceive('isBilateral')->once()->withNoArgs()->andReturn(false);
        $irhpPermitType->shouldReceive('isMultilateral')->once()->withNoArgs()->andReturn(false);
        $irhpPermitType->shouldReceive('getActiveApplicationPath')
            ->with($data['createdOn'])
            ->once()
            ->andReturn($applicationPath);

        $entity = $this->createNewEntity(null, null, $irhpPermitType, $licence);
        $entity->setAnswers($data['answers']);
        $entity->setCreatedOn($data['createdOn']);
        $entity->setCheckedAnswers($data['checkedAnswers']);
        $entity->setDeclaration($data['declaration']);

        $this->assertEquals($expected, $entity->getQuestionAnswerData());
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
                    [
                        'section' => 'licence',
                        'slug' => 'custom-licence',
                        'questionShort' => 'section.name.application/licence',
                        'question' => 'section.name.application/licence',
                        'answer' => '',
                        'status' => SectionableInterface::SECTION_COMPLETION_NOT_STARTED,
                    ],
                    [
                        'section' => 'q1-slug',
                        'slug' => 'q1-slug',
                        'questionShort' => 'q1-short-key',
                        'question' => 'q1-key',
                        'questionType' => 'q1-type',
                        'answer' => null,
                        'status' => SectionableInterface::SECTION_COMPLETION_CANNOT_START,
                    ],
                    [
                        'section' => 'q2-slug',
                        'slug' => 'q2-slug',
                        'questionShort' => 'q2-short-key',
                        'question' => 'q2-key',
                        'questionType' => 'q2-type',
                        'answer' => null,
                        'status' => SectionableInterface::SECTION_COMPLETION_CANNOT_START,
                    ],
                    [
                        'section' => 'checkedAnswers',
                        'slug' => 'custom-check-answers',
                        'questionShort' => 'section.name.application/check-answers',
                        'question' => 'section.name.application/check-answers',
                        'answer' => null,
                        'status' => SectionableInterface::SECTION_COMPLETION_CANNOT_START,
                    ],
                    [
                        'section' => 'declaration',
                        'slug' => 'custom-declaration',
                        'questionShort' => 'section.name.application/declaration',
                        'question' => 'section.name.application/declaration',
                        'answer' => null,
                        'status' => SectionableInterface::SECTION_COMPLETION_CANNOT_START,
                    ],
                ],
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
                    [
                        'section' => 'licence',
                        'slug' => 'custom-licence',
                        'questionShort' => 'section.name.application/licence',
                        'question' => 'section.name.application/licence',
                        'answer' => 'OB1234567',
                        'status' => SectionableInterface::SECTION_COMPLETION_COMPLETED,
                    ],
                    [
                        'section' => 'q1-slug',
                        'slug' => 'q1-slug',
                        'questionShort' => 'q1-short-key',
                        'question' => 'q1-key',
                        'questionType' => 'q1-type',
                        'answer' => null,
                        'status' => SectionableInterface::SECTION_COMPLETION_NOT_STARTED,
                    ],
                    [
                        'section' => 'q2-slug',
                        'slug' => 'q2-slug',
                        'questionShort' => 'q2-short-key',
                        'question' => 'q2-key',
                        'questionType' => 'q2-type',
                        'answer' => null,
                        'status' => SectionableInterface::SECTION_COMPLETION_CANNOT_START,
                    ],
                    [
                        'section' => 'checkedAnswers',
                        'slug' => 'custom-check-answers',
                        'questionShort' => 'section.name.application/check-answers',
                        'question' => 'section.name.application/check-answers',
                        'answer' => 0,
                        'status' => SectionableInterface::SECTION_COMPLETION_CANNOT_START,
                    ],
                    [
                        'section' => 'declaration',
                        'slug' => 'custom-declaration',
                        'questionShort' => 'section.name.application/declaration',
                        'question' => 'section.name.application/declaration',
                        'answer' => null,
                        'status' => SectionableInterface::SECTION_COMPLETION_CANNOT_START,
                    ],
                ],
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
                    [
                        'section' => 'licence',
                        'slug' => 'custom-licence',
                        'questionShort' => 'section.name.application/licence',
                        'question' => 'section.name.application/licence',
                        'answer' => 'OB1234567',
                        'status' => SectionableInterface::SECTION_COMPLETION_COMPLETED,
                    ],
                    [
                        'section' => 'q1-slug',
                        'slug' => 'q1-slug',
                        'questionShort' => 'q1-short-key',
                        'question' => 'q1-key',
                        'questionType' => 'q1-type',
                        'answer' => 'q1-answer',
                        'status' => SectionableInterface::SECTION_COMPLETION_COMPLETED,
                    ],
                    [
                        'section' => 'q2-slug',
                        'slug' => 'q2-slug',
                        'questionShort' => 'q2-short-key',
                        'question' => 'q2-key',
                        'questionType' => 'q2-type',
                        'answer' => null,
                        'status' => SectionableInterface::SECTION_COMPLETION_NOT_STARTED,
                    ],
                    [
                        'section' => 'checkedAnswers',
                        'slug' => 'custom-check-answers',
                        'questionShort' => 'section.name.application/check-answers',
                        'question' => 'section.name.application/check-answers',
                        'answer' => 0,
                        'status' => SectionableInterface::SECTION_COMPLETION_CANNOT_START,
                    ],
                    [
                        'section' => 'declaration',
                        'slug' => 'custom-declaration',
                        'questionShort' => 'section.name.application/declaration',
                        'question' => 'section.name.application/declaration',
                        'answer' => null,
                        'status' => SectionableInterface::SECTION_COMPLETION_CANNOT_START,
                    ],
                ],
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
                    [
                        'section' => 'licence',
                        'slug' => 'custom-licence',
                        'questionShort' => 'section.name.application/licence',
                        'question' => 'section.name.application/licence',
                        'answer' => 'OB1234567',
                        'status' => SectionableInterface::SECTION_COMPLETION_COMPLETED,
                    ],
                    [
                        'section' => 'q1-slug',
                        'slug' => 'q1-slug',
                        'questionShort' => 'q1-short-key',
                        'question' => 'q1-key',
                        'questionType' => 'q1-type',
                        'answer' => 'q1-answer',
                        'status' => SectionableInterface::SECTION_COMPLETION_COMPLETED,
                    ],
                    [
                        'section' => 'q2-slug',
                        'slug' => 'q2-slug',
                        'questionShort' => 'q2-short-key',
                        'question' => 'q2-key',
                        'questionType' => 'q2-type',
                        'answer' => 'q2-answer',
                        'status' => SectionableInterface::SECTION_COMPLETION_COMPLETED,
                    ],
                    [
                        'section' => 'checkedAnswers',
                        'slug' => 'custom-check-answers',
                        'questionShort' => 'section.name.application/check-answers',
                        'question' => 'section.name.application/check-answers',
                        'answer' => 0,
                        'status' => SectionableInterface::SECTION_COMPLETION_NOT_STARTED,
                    ],
                    [
                        'section' => 'declaration',
                        'slug' => 'custom-declaration',
                        'questionShort' => 'section.name.application/declaration',
                        'question' => 'section.name.application/declaration',
                        'answer' => null,
                        'status' => SectionableInterface::SECTION_COMPLETION_CANNOT_START,
                    ],
                ],
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
                    [
                        'section' => 'licence',
                        'slug' => 'custom-licence',
                        'questionShort' => 'section.name.application/licence',
                        'question' => 'section.name.application/licence',
                        'answer' => 'OB1234567',
                        'status' => SectionableInterface::SECTION_COMPLETION_COMPLETED,
                    ],
                    [
                        'section' => 'q1-slug',
                        'slug' => 'q1-slug',
                        'questionShort' => 'q1-short-key',
                        'question' => 'q1-key',
                        'questionType' => 'q1-type',
                        'answer' => 'q1-answer',
                        'status' => SectionableInterface::SECTION_COMPLETION_COMPLETED,
                    ],
                    [
                        'section' => 'q2-slug',
                        'slug' => 'q2-slug',
                        'questionShort' => 'q2-short-key',
                        'question' => 'q2-key',
                        'questionType' => 'q2-type',
                        'answer' => 'q2-answer',
                        'status' => SectionableInterface::SECTION_COMPLETION_COMPLETED,
                    ],
                    [
                        'section' => 'checkedAnswers',
                        'slug' => 'custom-check-answers',
                        'questionShort' => 'section.name.application/check-answers',
                        'question' => 'section.name.application/check-answers',
                        'answer' => 1,
                        'status' => SectionableInterface::SECTION_COMPLETION_COMPLETED,
                    ],
                    [
                        'section' => 'declaration',
                        'slug' => 'custom-declaration',
                        'questionShort' => 'section.name.application/declaration',
                        'question' => 'section.name.application/declaration',
                        'answer' => null,
                        'status' => SectionableInterface::SECTION_COMPLETION_NOT_STARTED,
                    ],
                ],
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
                    [
                        'section' => 'licence',
                        'slug' => 'custom-licence',
                        'questionShort' => 'section.name.application/licence',
                        'question' => 'section.name.application/licence',
                        'answer' => 'OB1234567',
                        'status' => SectionableInterface::SECTION_COMPLETION_COMPLETED,
                    ],
                    [
                        'section' => 'q1-slug',
                        'slug' => 'q1-slug',
                        'questionShort' => 'q1-short-key',
                        'question' => 'q1-key',
                        'questionType' => 'q1-type',
                        'answer' => 'q1-answer',
                        'status' => SectionableInterface::SECTION_COMPLETION_COMPLETED,
                    ],
                    [
                        'section' => 'q2-slug',
                        'slug' => 'q2-slug',
                        'questionShort' => 'q2-short-key',
                        'question' => 'q2-key',
                        'questionType' => 'q2-type',
                        'answer' => 'q2-answer',
                        'status' => SectionableInterface::SECTION_COMPLETION_COMPLETED,
                    ],
                    [
                        'section' => 'checkedAnswers',
                        'slug' => 'custom-check-answers',
                        'questionShort' => 'section.name.application/check-answers',
                        'question' => 'section.name.application/check-answers',
                        'answer' => 1,
                        'status' => SectionableInterface::SECTION_COMPLETION_COMPLETED,
                    ],
                    [
                        'section' => 'declaration',
                        'slug' => 'custom-declaration',
                        'questionShort' => 'section.name.application/declaration',
                        'question' => 'section.name.application/declaration',
                        'answer' => 1,
                        'status' => SectionableInterface::SECTION_COMPLETION_COMPLETED,
                    ],
                ],
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

    public function testGetAnswerNullForCustomEcmtRemovalNoOfPermits()
    {
        $question = m::mock(Question::class);
        $question->shouldReceive('isCustom')->withNoArgs()->once()->andReturn(true);
        $question->shouldReceive('getFormControlType')->andReturn(Question::FORM_CONTROL_ECMT_REMOVAL_NO_OF_PERMITS);

        $step = m::mock(ApplicationStep::class);
        $step->shouldReceive('getQuestion')->withNoArgs()->once()->andReturn($question);

        $entity = $this->createNewEntity();

        $this->assertNull($entity->getAnswer($step));
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

    public function testGetAnswerForQuestionWithoutAnswer()
    {
        $createdOn = new DateTime();

        $questionTextId = 1;
        $questionText = m::mock(QuestionText::class);
        $questionText->shouldReceive('getId')->withNoArgs()->once()->andReturn($questionTextId);

        $question = m::mock(Question::class);
        $question->shouldReceive('isCustom')->withNoArgs()->once()->andReturn(false);
        $question->shouldReceive('getActiveQuestionText')->with($createdOn)->once()->andReturn($questionText);

        $step = m::mock(ApplicationStep::class);
        $step->shouldReceive('getQuestion')->withNoArgs()->once()->andReturn($question);

        $entity = $this->createNewEntity();
        $entity->setCreatedOn($createdOn);

        $this->assertNull($entity->getAnswer($step));
    }

    public function testGetAnswerForQuestionWithAnswer()
    {
        $createdOn = new DateTime();
        $answer = 'answer';

        $questionTextId = 1;
        $questionText = m::mock(QuestionText::class);
        $questionText->shouldReceive('getId')->withNoArgs()->once()->andReturn($questionTextId);

        $question = m::mock(Question::class);
        $question->shouldReceive('isCustom')->withNoArgs()->once()->andReturn(false);
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
            'IrhpApplication has either zero or more than one linked IrhpPermitApplication instances'
        );

        $entity = $this->createNewEntity();
        $entity->getFirstIrhpPermitApplication();
    }

    public function testGetFirstIrhpPermitApplicationExceptionOnMoreThanOne()
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage(
            'IrhpApplication has either zero or more than one linked IrhpPermitApplication instances'
        );

        $entity = $this->createNewEntity();
        $entity->addIrhpPermitApplications(m::mock(IrhpPermitApplication::class));
        $entity->addIrhpPermitApplications(m::mock(IrhpPermitApplication::class));

        $entity->getFirstIrhpPermitApplication();
    }
}
