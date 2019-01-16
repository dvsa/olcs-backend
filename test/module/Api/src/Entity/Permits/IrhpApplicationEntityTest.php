<?php

namespace Dvsa\OlcsTest\Api\Entity\Permits;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Dvsa\OlcsTest\Api\Entity\Abstracts\EntityTester;
use Dvsa\Olcs\Api\Entity\Fee\Fee;
use Dvsa\Olcs\Api\Entity\Fee\FeeType;
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
        $this->sut = m::mock(Entity::class)->makePartial();
        $this->sut->initCollections();

        parent::setUp();
    }

    public function testGetCalculatedBundleValues()
    {
        $this->sut->shouldReceive('getApplicationRef')
            ->andReturn('appRef')
            ->shouldReceive('canBeCancelled')
            ->andReturn(false)
            ->shouldReceive('canBeSubmitted')
            ->andReturn(false)
            ->shouldReceive('hasOutstandingFees')
            ->andReturn(false)
            ->shouldReceive('getSectionCompletion')
            ->andReturn([])
            ->shouldReceive('hasCheckedAnswers')
            ->andReturn(false)
            ->shouldReceive('hasMadeDeclaration')
            ->andReturn(false)
            ->shouldReceive('isNotYetSubmitted')
            ->andReturn(true)
            ->shouldReceive('isReadyForNoOfPermits')
            ->andReturn(false);

        $this->assertSame(
            [
                'applicationRef' => 'appRef',
                'canBeCancelled' => false,
                'canBeSubmitted' => false,
                'hasOutstandingFees' => false,
                'sectionCompletion' => [],
                'hasCheckedAnswers' => false,
                'hasMadeDeclaration' => false,
                'isNotYetSubmitted' => true,
                'isReadyForNoOfPermits' => false,
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
     * @dataProvider dpTestHasOutstandingFees
     */
    public function testHasOutstandingFees($fees, $expected)
    {
        $this->sut->setFees($fees);

        $this->assertSame($expected, $this->sut->hasOutstandingFees());
    }

    public function dpTestHasOutstandingFees()
    {
        $outstandingIrhpAppFee = m::mock(Fee::class);
        $outstandingIrhpAppFee->shouldReceive('isOutstanding')
            ->andReturn(true)
            ->shouldReceive('getInvoicedDate')
            ->andReturn(new DateTime('2018-02-01'))
            ->shouldReceive('getFeeType->getFeeType->getId')
            ->andReturn(FeeType::FEE_TYPE_IRHP_APP);

        $paidIrhpAppFee = m::mock(Fee::class);
        $paidIrhpAppFee->shouldReceive('isOutstanding')
            ->andReturn(false)
            ->shouldReceive('getInvoicedDate')
            ->andReturn(new DateTime('2018-03-01'))
            ->shouldReceive('getFeeType->getFeeType->getId')
            ->andReturn(FeeType::FEE_TYPE_IRHP_APP);

        $outstandingIrhpIssueFee = m::mock(Fee::class);
        $outstandingIrhpIssueFee->shouldReceive('isOutstanding')
            ->andReturn(true)
            ->shouldReceive('getInvoicedDate')
            ->andReturn(new DateTime('2018-04-01'))
            ->shouldReceive('getFeeType->getFeeType->getId')
            ->andReturn(FeeType::FEE_TYPE_IRHP_ISSUE);

        $paidIrhpIssueFee = m::mock(Fee::class);
        $paidIrhpIssueFee->shouldReceive('isOutstanding')
            ->andReturn(false)
            ->shouldReceive('getInvoicedDate')
            ->andReturn(new DateTime('2018-05-01'))
            ->shouldReceive('getFeeType->getFeeType->getId')
            ->andReturn(FeeType::FEE_TYPE_IRHP_ISSUE);

        return [
            'no fees' => [
                'fees' => new ArrayCollection(),
                'expected' => false,
            ],
            'outstanding IRHP app fee' => [
                'fees' => new ArrayCollection([$outstandingIrhpAppFee]),
                'expected' => true,
            ],
            'paid IRHP app fee' => [
                'fees' => new ArrayCollection([$paidIrhpAppFee]),
                'expected' => false,
            ],
            'outstanding IRHP issue fee' => [
                'fees' => new ArrayCollection([$outstandingIrhpIssueFee]),
                'expected' => true,
            ],
            'paid IRHP issue fee' => [
                'fees' => new ArrayCollection([$paidIrhpIssueFee]),
                'expected' => false,
            ],
            'multiple IRHP fees - all paid' => [
                'fees' => new ArrayCollection([$paidIrhpAppFee, $paidIrhpIssueFee]),
                'expected' => false,
            ],
            'multiple IRHP fees - some outstanding' => [
                'fees' => new ArrayCollection([$paidIrhpAppFee, $outstandingIrhpAppFee, $paidIrhpIssueFee]),
                'expected' => true,
            ],
            'multiple IRHP fees - multiple outstanding' => [
                'fees' => new ArrayCollection([$paidIrhpAppFee, $outstandingIrhpAppFee, $outstandingIrhpIssueFee]),
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
     * @dataProvider dpTestGetLatestOutstandingIrhpApplicationFee
     */
    public function testGetLatestOutstandingIrhpApplicationFee($fees, $expected)
    {
        $this->sut->setFees($fees);

        $this->assertSame($expected, $this->sut->getLatestOutstandingIrhpApplicationFee());
    }

    public function dpTestGetLatestOutstandingIrhpApplicationFee()
    {
        $outstandingIrhpAppFee = m::mock(Fee::class);
        $outstandingIrhpAppFee->shouldReceive('isOutstanding')
            ->andReturn(true)
            ->shouldReceive('getInvoicedDate')
            ->andReturn(new DateTime('2018-02-01'))
            ->shouldReceive('getFeeType->getFeeType->getId')
            ->andReturn(FeeType::FEE_TYPE_IRHP_APP);

        $paidIrhpAppFee = m::mock(Fee::class);
        $paidIrhpAppFee->shouldReceive('isOutstanding')
            ->andReturn(false)
            ->shouldReceive('getInvoicedDate')
            ->andReturn(new DateTime('2018-03-01'))
            ->shouldReceive('getFeeType->getFeeType->getId')
            ->andReturn(FeeType::FEE_TYPE_IRHP_APP);

        $outstandingIrhpIssueFee = m::mock(Fee::class);
        $outstandingIrhpIssueFee->shouldReceive('isOutstanding')
            ->andReturn(true)
            ->shouldReceive('getInvoicedDate')
            ->andReturn(new DateTime('2018-04-01'))
            ->shouldReceive('getFeeType->getFeeType->getId')
            ->andReturn(FeeType::FEE_TYPE_IRHP_ISSUE);

        $paidIrhpIssueFee = m::mock(Fee::class);
        $paidIrhpIssueFee->shouldReceive('isOutstanding')
            ->andReturn(false)
            ->shouldReceive('getInvoicedDate')
            ->andReturn(new DateTime('2018-05-01'))
            ->shouldReceive('getFeeType->getFeeType->getId')
            ->andReturn(FeeType::FEE_TYPE_IRHP_ISSUE);

        return [
            'no fees' => [
                'fees' => new ArrayCollection(),
                'expected' => null,
            ],
            'outstanding IRHP app fee' => [
                'fees' => new ArrayCollection([$outstandingIrhpAppFee]),
                'expected' => $outstandingIrhpAppFee,
            ],
            'paid IRHP app fee' => [
                'fees' => new ArrayCollection([$paidIrhpAppFee]),
                'expected' => null,
            ],
            'outstanding IRHP issue fee' => [
                'fees' => new ArrayCollection([$outstandingIrhpIssueFee]),
                'expected' => $outstandingIrhpIssueFee,
            ],
            'paid IRHP issue fee' => [
                'fees' => new ArrayCollection([$paidIrhpIssueFee]),
                'expected' => null,
            ],
            'multiple IRHP fees - all paid' => [
                'fees' => new ArrayCollection([$paidIrhpAppFee, $paidIrhpIssueFee]),
                'expected' => null,
            ],
            'multiple IRHP fees - some outstanding' => [
                'fees' => new ArrayCollection([$paidIrhpAppFee, $outstandingIrhpAppFee, $paidIrhpIssueFee]),
                'expected' => $outstandingIrhpAppFee,
            ],
            'multiple IRHP fees - multiple outstanding' => [
                'fees' => new ArrayCollection([$paidIrhpAppFee, $outstandingIrhpAppFee, $outstandingIrhpIssueFee]),
                'expected' => $outstandingIrhpIssueFee,
            ],
        ];
    }

    /**
     * @dataProvider dpTestGetSectionCompletion
     */
    public function testGetSectionCompletion($data, $expected)
    {
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
            'no data set' => [
                'data' => [
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
            'licence set' => [
                'data' => [
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
            'IRHP permit apps with all apps without permits required set' => [
                'data' => [
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
            'IRHP permit apps with one app without permits required set' => [
                'data' => [
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
            'IRHP permit apps with all apps with permits required set' => [
                'data' => [
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
            'checked answers set' => [
                'data' => [
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
            'declaration set' => [
                'data' => [
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
        ];
    }

    public function testUpdateCheckAnswers()
    {
        $irhpApplication = m::mock(Entity::class)->makePartial();

        $irhpApplication->updateCheckAnswers();
        $this->assertTrue($irhpApplication->getCheckedAnswers());
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
}
