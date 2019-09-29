<?php

namespace Dvsa\OlcsTest\Api\Entity\Permits;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Dvsa\Olcs\Api\Entity\Generic\ApplicationPathGroup;
use Dvsa\OlcsTest\Api\Entity\Abstracts\EntityTester;
use Dvsa\Olcs\Api\Domain\Exception\ForbiddenException;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitStock as Entity;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitType;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitRange;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitWindow;
use Dvsa\Olcs\Api\Entity\System\RefData;
use Dvsa\Olcs\Api\Entity\Traits\ProcessDateTrait;
use Mockery as m;
use RuntimeException;

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
        $validFrom = '2019-01-01';
        $expectedFrom = $this->processDate($validFrom, 'Y-m-d');
        $validTo = '2019-02-01';
        $expectedTo = $this->processDate($validTo, 'Y-m-d');
        $initialStock = 1400;

        $updateValidFrom = '2019-02-01';
        $updateExpectedFrom = $this->processDate($updateValidFrom, 'Y-m-d');
        $updateValidTo = '2019-02-02';
        $updateExpectedTo = $this->processDate($updateValidTo, 'Y-m-d');
        $updateInitialStock = 1401;
        $status = m::mock(RefData::class);

        $irhpPermitType->shouldReceive('getId')
            ->andReturn(3);

        $entity = Entity::create($irhpPermitType, null, $initialStock, $status, null, null, null, $validFrom, $validTo);

        $this->assertEquals($irhpPermitType, $entity->getIrhpPermitType());
        $this->assertEquals($expectedFrom, $entity->getValidFrom());
        $this->assertEquals($expectedTo, $entity->getValidTo());
        $this->assertEquals($initialStock, $entity->getInitialStock());
        $this->assertEquals($status, $entity->getStatus());

        $entity->update($irhpPermitType, null, $updateInitialStock, null, $updateValidFrom, $updateValidTo);

        $this->assertEquals($updateExpectedFrom, $entity->getValidFrom());
        $this->assertEquals($updateExpectedTo, $entity->getValidTo());
        $this->assertEquals($updateInitialStock, $entity->getInitialStock());
    }

    public function testCreateUpdateAppPath()
    {
        $irhpPermitType = m::mock(IrhpPermitType::class)->makePartial();
        $validFrom = '2019-01-01';
        $expectedFrom = $this->processDate($validFrom, 'Y-m-d');
        $validTo = '2019-02-01';
        $expectedTo = $this->processDate($validTo, 'Y-m-d');
        $initialStock = 1400;
        $applicationPathGroup = m::mock(ApplicationPathGroup::class);
        $businessProcess = m::mock(RefData::class);
        $periodNameKey = 'initial.period.name.key';

        $updateValidFrom = '2019-02-01';
        $updateExpectedFrom = $this->processDate($updateValidFrom, 'Y-m-d');
        $updateValidTo = '2019-02-02';
        $updateExpectedTo = $this->processDate($updateValidTo, 'Y-m-d');
        $updateInitialStock = 1401;
        $status = m::mock(RefData::class);
        $updatePeriodNameKey = 'updated.period.name.key';

        $irhpPermitType->shouldReceive('getId')
            ->andReturn(3);

        $entity = Entity::create($irhpPermitType, null, $initialStock, $status, $applicationPathGroup, $businessProcess, $periodNameKey, $validFrom, $validTo);

        $this->assertEquals($irhpPermitType, $entity->getIrhpPermitType());
        $this->assertEquals($expectedFrom, $entity->getValidFrom());
        $this->assertEquals($expectedTo, $entity->getValidTo());
        $this->assertEquals($initialStock, $entity->getInitialStock());
        $this->assertEquals($status, $entity->getStatus());

        $entity->update($irhpPermitType, null, $updateInitialStock, $updatePeriodNameKey, $updateValidFrom, $updateValidTo);

        $this->assertEquals($updateExpectedFrom, $entity->getValidFrom());
        $this->assertEquals($updateExpectedTo, $entity->getValidTo());
        $this->assertEquals($updateInitialStock, $entity->getInitialStock());
        $this->assertEquals($updatePeriodNameKey, $entity->getPeriodNameKey());
    }

    public function testGetStatusDescription()
    {
        $statusDescription = 'status description';

        $status = m::mock(RefData::class);
        $irhpPermitType = m::mock(IrhpPermitType::class)->makePartial();
        $status->shouldReceive('getDescription')
            ->andReturn($statusDescription);

        $stock = Entity::create(
            $irhpPermitType,
            null,
            1400,
            $status,
            null,
            null,
            null,
            '2019-01-01',
            '2019-02-01'
        );

        $irhpPermitType->shouldReceive('getId')
            ->andReturn(3);

        $this->assertEquals(
            $statusDescription,
            $stock->getStatusDescription()
        );
    }

    /**
     * @dataProvider canDeleteProvider
     */
    public function testCanDelete($data, $expected)
    {
        $status = m::mock(RefData::class);
        $irhpPermitType = m::mock(IrhpPermitType::class)->makePartial();

        $stock = Entity::create(
            $irhpPermitType,
            null,
            1400,
            $status,
            null,
            null,
            null,
            '2019-01-01',
            '2019-02-01'
        );

        $irhpPermitType->shouldReceive('getId')
            ->andReturn(3);

        $stock->setIrhpPermitRanges($data['irhpPermitRanges']);
        $stock->setIrhpPermitWindows($data['irhpPermitWindows']);

        $this->assertEquals($expected, $stock->canDelete($data));
    }

    /**
     * @dataProvider emissionsRangeProvider
     */
    public function testHasEuro5Range($data, $expected)
    {
        $status = m::mock(RefData::class);
        $irhpPermitType = m::mock(IrhpPermitType::class)->makePartial();

        $stock = Entity::create(
            $irhpPermitType,
            null,
            1400,
            $status,
            null,
            null,
            null,
            '2019-01-01',
            '2019-02-01'
        );

        $stock->setIrhpPermitRanges($data);

        $this->assertEquals($expected['euro5'], $stock->hasEuro5Range());
    }

    /**
     * @dataProvider emissionsRangeProvider
     */
    public function testHasEuro6Range($data, $expected)
    {
        $status = m::mock(RefData::class);
        $irhpPermitType = m::mock(IrhpPermitType::class)->makePartial();

        $stock = Entity::create(
            $irhpPermitType,
            null,
            1400,
            $status,
            null,
            null,
            null,
            '2019-01-01',
            '2019-02-01'
        );

        $stock->setIrhpPermitRanges($data);

        $this->assertEquals($expected['euro6'], $stock->hasEuro6Range());
    }

    /**
     * Data provider
     *
     * @return array
     */
    public function emissionsRangeProvider()
    {
        $euro5Range = m::mock(IrhpPermitRange::class)->makePartial();
        $euro5Range->setEmissionsCategory(new RefData(RefData::EMISSIONS_CATEGORY_EURO5_REF));

        $euro6Range = m::mock(IrhpPermitRange::class)->makePartial();
        $euro6Range->setEmissionsCategory(new RefData(RefData::EMISSIONS_CATEGORY_EURO6_REF));

        $naRange = m::mock(IrhpPermitRange::class)->makePartial();
        $naRange->setEmissionsCategory(new RefData(RefData::EMISSIONS_CATEGORY_NA_REF));

        return [
            'both' => [
                [$euro5Range, $euro6Range],
                ['euro5' => true, 'euro6' => true],
            ],
            'euro5' => [
                [$euro5Range],
                ['euro5' => true, 'euro6' => false],
            ],
            'euro6' => [
                [$euro6Range],
                ['euro5' => false, 'euro6' => true],
            ],
            'na' => [
                [$naRange],
                ['euro5' => false, 'euro6' => false],
            ],
        ];
    }

    /**
     * Data provider
     *
     * @return array
     */
    public function canDeleteProvider()
    {
        return [
            'valid delete' => [
                [
                    'irhpPermitRanges' => [],
                    'irhpPermitWindows' => [],
                ],
                true,
            ],
            'existing range' => [
                [
                    'irhpPermitRanges' => [m::mock(IrhpPermitRange::class)],
                    'irhpPermitWindows' => [],
                ],
                false,
            ],
            'existing window' => [
                [
                    'irhpPermitRanges' => [],
                    'irhpPermitWindows' => [m::mock(IrhpPermitWindow::class)],
                ],
                false,
            ],
            'existing window and range' => [
                [
                    'irhpPermitRanges' => [m::mock(IrhpPermitRange::class)],
                    'irhpPermitWindows' => [m::mock(IrhpPermitWindow::class)],
                ],
                false,
            ]
        ];
    }

    /**
     * @dataProvider statusAllowsQueueRunScoringProvider
     */
    public function testStatusAllowsQueueRunScoring($statusId, $expectedResult)
    {
        $stock = $this->createEntityWithStatusId($statusId);

        $this->assertEquals(
            $expectedResult,
            $stock->statusAllowsQueueRunScoring()
        );
    }

    public function statusAllowsQueueRunScoringProvider()
    {
        return [
            [Entity::STATUS_SCORING_NEVER_RUN, true],
            [Entity::STATUS_SCORING_PENDING, false],
            [Entity::STATUS_SCORING_IN_PROGRESS, false],
            [Entity::STATUS_SCORING_SUCCESSFUL, true],
            [Entity::STATUS_SCORING_PREREQUISITE_FAIL, true],
            [Entity::STATUS_SCORING_UNEXPECTED_FAIL, true],
            [Entity::STATUS_ACCEPT_PENDING, false],
            [Entity::STATUS_ACCEPT_IN_PROGRESS, false],
            [Entity::STATUS_ACCEPT_SUCCESSFUL, true],
            [Entity::STATUS_ACCEPT_PREREQUISITE_FAIL, true],
            [Entity::STATUS_ACCEPT_UNEXPECTED_FAIL, false],
        ];
    }

    /**
     * @dataProvider statusAllowsRunScoringProvider
     */
    public function testStatusAllowsRunScoring($statusId, $expectedResult)
    {
        $stock = $this->createEntityWithStatusId($statusId);

        $this->assertEquals(
            $expectedResult,
            $stock->statusAllowsRunScoring()
        );
    }

    public function statusAllowsRunScoringProvider()
    {
        return [
            [Entity::STATUS_SCORING_NEVER_RUN, false],
            [Entity::STATUS_SCORING_PENDING, true],
            [Entity::STATUS_SCORING_IN_PROGRESS, false],
            [Entity::STATUS_SCORING_SUCCESSFUL, false],
            [Entity::STATUS_SCORING_PREREQUISITE_FAIL, false],
            [Entity::STATUS_SCORING_UNEXPECTED_FAIL, false],
            [Entity::STATUS_ACCEPT_PENDING, false],
            [Entity::STATUS_ACCEPT_IN_PROGRESS, false],
            [Entity::STATUS_ACCEPT_SUCCESSFUL, false],
            [Entity::STATUS_ACCEPT_PREREQUISITE_FAIL, false],
            [Entity::STATUS_ACCEPT_UNEXPECTED_FAIL, false],
        ];
    }

    /**
     * @dataProvider statusAllowsQueueAcceptScoringProvider
     */
    public function testStatusAllowsQueueAcceptScoring($statusId, $expectedResult)
    {
        $stock = $this->createEntityWithStatusId($statusId);

        $this->assertEquals(
            $expectedResult,
            $stock->statusAllowsQueueAcceptScoring()
        );
    }

    public function statusAllowsQueueAcceptScoringProvider()
    {
        return [
            [Entity::STATUS_SCORING_NEVER_RUN, false],
            [Entity::STATUS_SCORING_PENDING, false],
            [Entity::STATUS_SCORING_IN_PROGRESS, false],
            [Entity::STATUS_SCORING_SUCCESSFUL, true],
            [Entity::STATUS_SCORING_PREREQUISITE_FAIL, false],
            [Entity::STATUS_SCORING_UNEXPECTED_FAIL, false],
            [Entity::STATUS_ACCEPT_PENDING, false],
            [Entity::STATUS_ACCEPT_IN_PROGRESS, false],
            [Entity::STATUS_ACCEPT_SUCCESSFUL, false],
            [Entity::STATUS_ACCEPT_PREREQUISITE_FAIL, true],
            [Entity::STATUS_ACCEPT_UNEXPECTED_FAIL, true],
        ];
    }

    /**
     * @dataProvider statusAllowsAcceptScoringProvider
     */
    public function testStatusAllowsAcceptScoring($statusId, $expectedResult)
    {
        $stock = $this->createEntityWithStatusId($statusId);

        $this->assertEquals(
            $expectedResult,
            $stock->statusAllowsAcceptScoring()
        );
    }

    public function statusAllowsAcceptScoringProvider()
    {
        return [
            [Entity::STATUS_SCORING_NEVER_RUN, false],
            [Entity::STATUS_SCORING_PENDING, false],
            [Entity::STATUS_SCORING_IN_PROGRESS, false],
            [Entity::STATUS_SCORING_SUCCESSFUL, false],
            [Entity::STATUS_SCORING_PREREQUISITE_FAIL, false],
            [Entity::STATUS_SCORING_UNEXPECTED_FAIL, false],
            [Entity::STATUS_ACCEPT_PENDING, true],
            [Entity::STATUS_ACCEPT_IN_PROGRESS, false],
            [Entity::STATUS_ACCEPT_SUCCESSFUL, false],
            [Entity::STATUS_ACCEPT_PREREQUISITE_FAIL, false],
            [Entity::STATUS_ACCEPT_UNEXPECTED_FAIL, false],
        ];
    }

    /**
     * @dataProvider proceedToScoringPendingProvider
     */
    public function testProceedToScoringPending($statusId)
    {
        $stock = $this->createEntityWithStatusId($statusId);
        $newStatus = m::mock(RefData::class);

        $stock->proceedToScoringPending($newStatus);
        $this->assertSame($newStatus, $stock->getStatus());
    }

    public function proceedToScoringPendingProvider()
    {
        return [
            [Entity::STATUS_SCORING_NEVER_RUN],
            [Entity::STATUS_SCORING_SUCCESSFUL],
            [Entity::STATUS_SCORING_PREREQUISITE_FAIL],
            [Entity::STATUS_SCORING_UNEXPECTED_FAIL],
            [Entity::STATUS_ACCEPT_PREREQUISITE_FAIL],
            [Entity::STATUS_ACCEPT_SUCCESSFUL],
        ];
    }

    /**
     * @dataProvider proceedToScoringPendingExceptionProvider
     */
    public function testProceedToScoringPendingException($statusId, $statusDescription, $expectedExceptionMessages)
    {
        $stock = $this->createEntityWithStatusIdAndDescription($statusId, $statusDescription);
        $exceptionThrown = false;

        try {
            $stock->proceedToScoringPending(m::mock(RefData::class));
        } catch (ForbiddenException $e) {
            $exceptionThrown = true;
            $this->assertEquals($expectedExceptionMessages, $e->getMessages());
        }

        $this->assertTrue($exceptionThrown);
    }

    public function proceedToScoringPendingExceptionProvider()
    {
        return [
            [
                Entity::STATUS_SCORING_PENDING,
                'Scoring pending',
                ['This stock is not in the correct status to proceed to scoring pending (Scoring pending)']
            ],
            [
                Entity::STATUS_SCORING_IN_PROGRESS,
                'Scoring in progress',
                ['This stock is not in the correct status to proceed to scoring pending (Scoring in progress)']
            ],
            [
                Entity::STATUS_ACCEPT_PENDING,
                'Accept pending',
                ['This stock is not in the correct status to proceed to scoring pending (Accept pending)']
            ],
            [
                Entity::STATUS_ACCEPT_IN_PROGRESS,
                'Accept in progress',
                ['This stock is not in the correct status to proceed to scoring pending (Accept in progress)']
            ],
            [
                Entity::STATUS_ACCEPT_UNEXPECTED_FAIL,
                'Accept unexpected fail',
                ['This stock is not in the correct status to proceed to scoring pending (Accept unexpected fail)']
            ],
        ];
    }

    public function testProceedToScoringPrerequisiteFail()
    {
        $stock = $this->createEntityWithStatusId(Entity::STATUS_SCORING_PENDING);
        $newStatus = m::mock(RefData::class);

        $stock->proceedToScoringPrerequisiteFail($newStatus);
        $this->assertSame($newStatus, $stock->getStatus());
    }

    /**
     * @dataProvider proceedToScoringPrerequisiteFailExceptionProvider
     */
    public function testProceedToScoringPrerequisiteFailException($statusId, $statusDescription, $expectedExceptionMessages)
    {
        $stock = $this->createEntityWithStatusIdAndDescription($statusId, $statusDescription);
        $exceptionThrown = false;

        try {
            $stock->proceedToScoringPrerequisiteFail(m::mock(RefData::class));
        } catch (ForbiddenException $e) {
            $exceptionThrown = true;
            $this->assertEquals($expectedExceptionMessages, $e->getMessages());
        }

        $this->assertTrue($exceptionThrown);
    }

    public function proceedToScoringPrerequisiteFailExceptionProvider()
    {
        return [
            [
                Entity::STATUS_SCORING_NEVER_RUN,
                'Scoring never run',
                ['This stock is not in the correct status to proceed to scoring prerequisite fail (Scoring never run)']
            ],
            [
                Entity::STATUS_SCORING_IN_PROGRESS,
                'Scoring in progress',
                ['This stock is not in the correct status to proceed to scoring prerequisite fail (Scoring in progress)']
            ],
            [
                Entity::STATUS_SCORING_SUCCESSFUL,
                'Scoring successful',
                ['This stock is not in the correct status to proceed to scoring prerequisite fail (Scoring successful)']
            ],
            [
                Entity::STATUS_SCORING_PREREQUISITE_FAIL,
                'Scoring prerequisite fail',
                ['This stock is not in the correct status to proceed to scoring prerequisite fail (Scoring prerequisite fail)']
            ],
            [
                Entity::STATUS_SCORING_UNEXPECTED_FAIL,
                'Scoring unexpected fail',
                ['This stock is not in the correct status to proceed to scoring prerequisite fail (Scoring unexpected fail)']
            ],
            [
                Entity::STATUS_ACCEPT_PENDING,
                'Accept pending',
                ['This stock is not in the correct status to proceed to scoring prerequisite fail (Accept pending)']
            ],
            [
                Entity::STATUS_ACCEPT_IN_PROGRESS,
                'Accept in progress',
                ['This stock is not in the correct status to proceed to scoring prerequisite fail (Accept in progress)']
            ],
            [
                Entity::STATUS_ACCEPT_SUCCESSFUL,
                'Accept successful',
                ['This stock is not in the correct status to proceed to scoring prerequisite fail (Accept successful)']
            ],
            [
                Entity::STATUS_ACCEPT_PREREQUISITE_FAIL,
                'Accept prerequisite fail',
                ['This stock is not in the correct status to proceed to scoring prerequisite fail (Accept prerequisite fail)']
            ],
            [
                Entity::STATUS_ACCEPT_UNEXPECTED_FAIL,
                'Accept unexpected fail',
                ['This stock is not in the correct status to proceed to scoring prerequisite fail (Accept unexpected fail)']
            ],
        ];
    }

    public function testProceedToScoringInProgress()
    {
        $stock = $this->createEntityWithStatusId(Entity::STATUS_SCORING_PENDING);
        $newStatus = m::mock(RefData::class);

        $stock->proceedToScoringInProgress($newStatus);
        $this->assertSame($newStatus, $stock->getStatus());
    }

    /**
     * @dataProvider proceedToScoringInProgressExceptionProvider
     */
    public function testProceedToScoringInProgressException($statusId, $statusDescription, $expectedExceptionMessages)
    {
        $stock = $this->createEntityWithStatusIdAndDescription($statusId, $statusDescription);
        $exceptionThrown = false;

        try {
            $stock->proceedToScoringInProgress(m::mock(RefData::class));
        } catch (ForbiddenException $e) {
            $exceptionThrown = true;
            $this->assertEquals($expectedExceptionMessages, $e->getMessages());
        }

        $this->assertTrue($exceptionThrown);
    }

    public function proceedToScoringInProgressExceptionProvider()
    {
        return [
            [
                Entity::STATUS_SCORING_NEVER_RUN,
                'Scoring never run',
                ['This stock is not in the correct status to proceed to scoring in progress (Scoring never run)']
            ],
            [
                Entity::STATUS_SCORING_IN_PROGRESS,
                'Scoring in progress',
                ['This stock is not in the correct status to proceed to scoring in progress (Scoring in progress)']
            ],
            [
                Entity::STATUS_SCORING_SUCCESSFUL,
                'Scoring successful',
                ['This stock is not in the correct status to proceed to scoring in progress (Scoring successful)']
            ],
            [
                Entity::STATUS_SCORING_PREREQUISITE_FAIL,
                'Scoring prerequisite fail',
                ['This stock is not in the correct status to proceed to scoring in progress (Scoring prerequisite fail)']
            ],
            [
                Entity::STATUS_SCORING_UNEXPECTED_FAIL,
                'Scoring unexpected fail',
                ['This stock is not in the correct status to proceed to scoring in progress (Scoring unexpected fail)']
            ],
            [
                Entity::STATUS_ACCEPT_PENDING,
                'Accept pending',
                ['This stock is not in the correct status to proceed to scoring in progress (Accept pending)']
            ],
            [
                Entity::STATUS_ACCEPT_IN_PROGRESS,
                'Accept in progress',
                ['This stock is not in the correct status to proceed to scoring in progress (Accept in progress)']
            ],
            [
                Entity::STATUS_ACCEPT_SUCCESSFUL,
                'Accept successful',
                ['This stock is not in the correct status to proceed to scoring in progress (Accept successful)']
            ],
            [
                Entity::STATUS_ACCEPT_PREREQUISITE_FAIL,
                'Accept prerequisite fail',
                ['This stock is not in the correct status to proceed to scoring in progress (Accept prerequisite fail)']
            ],
            [
                Entity::STATUS_ACCEPT_UNEXPECTED_FAIL,
                'Accept unexpected fail',
                ['This stock is not in the correct status to proceed to scoring in progress (Accept unexpected fail)']
            ],
        ];
    }

    public function testProceedToScoringSuccessful()
    {
        $stock = $this->createEntityWithStatusId(Entity::STATUS_SCORING_IN_PROGRESS);
        $newStatus = m::mock(RefData::class);

        $stock->proceedToScoringSuccessful($newStatus);
        $this->assertSame($newStatus, $stock->getStatus());
    }

    /**
     * @dataProvider proceedToScoringSuccessfulExceptionProvider
     */
    public function testProceedToScoringSuccessfulException($statusId, $statusDescription, $expectedExceptionMessages)
    {
        $stock = $this->createEntityWithStatusIdAndDescription($statusId, $statusDescription);
        $exceptionThrown = false;

        try {
            $stock->proceedToScoringSuccessful(m::mock(RefData::class));
        } catch (ForbiddenException $e) {
            $exceptionThrown = true;
            $this->assertEquals($expectedExceptionMessages, $e->getMessages());
        }

        $this->assertTrue($exceptionThrown);
    }

    public function proceedToScoringSuccessfulExceptionProvider()
    {
        return [
            [
                Entity::STATUS_SCORING_NEVER_RUN,
                'Scoring never run',
                ['This stock is not in the correct status to proceed to scoring successful (Scoring never run)']
            ],
            [
                Entity::STATUS_SCORING_PENDING,
                'Scoring pending',
                ['This stock is not in the correct status to proceed to scoring successful (Scoring pending)']
            ],
            [
                Entity::STATUS_SCORING_SUCCESSFUL,
                'Scoring successful',
                ['This stock is not in the correct status to proceed to scoring successful (Scoring successful)']
            ],
            [
                Entity::STATUS_SCORING_PREREQUISITE_FAIL,
                'Scoring prerequisite fail',
                ['This stock is not in the correct status to proceed to scoring successful (Scoring prerequisite fail)']
            ],
            [
                Entity::STATUS_SCORING_UNEXPECTED_FAIL,
                'Scoring unexpected fail',
                ['This stock is not in the correct status to proceed to scoring successful (Scoring unexpected fail)']
            ],
            [
                Entity::STATUS_ACCEPT_PENDING,
                'Accept pending',
                ['This stock is not in the correct status to proceed to scoring successful (Accept pending)']
            ],
            [
                Entity::STATUS_ACCEPT_IN_PROGRESS,
                'Accept in progress',
                ['This stock is not in the correct status to proceed to scoring successful (Accept in progress)']
            ],
            [
                Entity::STATUS_ACCEPT_SUCCESSFUL,
                'Accept successful',
                ['This stock is not in the correct status to proceed to scoring successful (Accept successful)']
            ],
            [
                Entity::STATUS_ACCEPT_PREREQUISITE_FAIL,
                'Accept prerequisite fail',
                ['This stock is not in the correct status to proceed to scoring successful (Accept prerequisite fail)']
            ],
            [
                Entity::STATUS_ACCEPT_UNEXPECTED_FAIL,
                'Accept unexpected fail',
                ['This stock is not in the correct status to proceed to scoring successful (Accept unexpected fail)']
            ],
        ];
    }

    public function testProceedToScoringUnexpectedFail()
    {
        $stock = $this->createEntityWithStatusId(Entity::STATUS_SCORING_IN_PROGRESS);
        $newStatus = m::mock(RefData::class);

        $stock->proceedToScoringUnexpectedFail($newStatus);
        $this->assertSame($newStatus, $stock->getStatus());
    }

    /**
     * @dataProvider proceedToScoringUnexpectedFailExceptionProvider
     */
    public function testProceedToScoringUnexpectedFailException($statusId, $statusDescription, $expectedExceptionMessages)
    {
        $stock = $this->createEntityWithStatusIdAndDescription($statusId, $statusDescription);
        $exceptionThrown = false;

        try {
            $stock->proceedToScoringUnexpectedFail(m::mock(RefData::class));
        } catch (ForbiddenException $e) {
            $exceptionThrown = true;
            $this->assertEquals($expectedExceptionMessages, $e->getMessages());
        }

        $this->assertTrue($exceptionThrown);
    }

    public function proceedToScoringUnexpectedFailExceptionProvider()
    {
        return [
            [
                Entity::STATUS_SCORING_NEVER_RUN,
                'Scoring never run',
                ['This stock is not in the correct status to proceed to scoring unexpected fail (Scoring never run)']
            ],
            [
                Entity::STATUS_SCORING_PENDING,
                'Scoring pending',
                ['This stock is not in the correct status to proceed to scoring unexpected fail (Scoring pending)']
            ],
            [
                Entity::STATUS_SCORING_SUCCESSFUL,
                'Scoring successful',
                ['This stock is not in the correct status to proceed to scoring unexpected fail (Scoring successful)']
            ],
            [
                Entity::STATUS_SCORING_PREREQUISITE_FAIL,
                'Scoring prerequisite fail',
                ['This stock is not in the correct status to proceed to scoring unexpected fail (Scoring prerequisite fail)']
            ],
            [
                Entity::STATUS_SCORING_UNEXPECTED_FAIL,
                'Scoring unexpected fail',
                ['This stock is not in the correct status to proceed to scoring unexpected fail (Scoring unexpected fail)']
            ],
            [
                Entity::STATUS_ACCEPT_PENDING,
                'Accept pending',
                ['This stock is not in the correct status to proceed to scoring unexpected fail (Accept pending)']
            ],
            [
                Entity::STATUS_ACCEPT_IN_PROGRESS,
                'Accept in progress',
                ['This stock is not in the correct status to proceed to scoring unexpected fail (Accept in progress)']
            ],
            [
                Entity::STATUS_ACCEPT_SUCCESSFUL,
                'Accept successful',
                ['This stock is not in the correct status to proceed to scoring unexpected fail (Accept successful)']
            ],
            [
                Entity::STATUS_ACCEPT_PREREQUISITE_FAIL,
                'Accept prerequisite fail',
                ['This stock is not in the correct status to proceed to scoring unexpected fail (Accept prerequisite fail)']
            ],
            [
                Entity::STATUS_ACCEPT_UNEXPECTED_FAIL,
                'Accept unexpected fail',
                ['This stock is not in the correct status to proceed to scoring unexpected fail (Accept unexpected fail)']
            ],
        ];
    }

    /**
     * @dataProvider proceedToAcceptPendingProvider
     */
    public function testProceedToAcceptPending($statusId)
    {
        $stock = $this->createEntityWithStatusId($statusId);
        $newStatus = m::mock(RefData::class);

        $stock->proceedToAcceptPending($newStatus);
        $this->assertSame($newStatus, $stock->getStatus());
    }

    public function proceedToAcceptPendingProvider()
    {
        return [
            [Entity::STATUS_SCORING_SUCCESSFUL],
            [Entity::STATUS_ACCEPT_PREREQUISITE_FAIL],
            [Entity::STATUS_ACCEPT_UNEXPECTED_FAIL],
        ];
    }

    /**
     * @dataProvider proceedToAcceptPendingExceptionProvider
     */
    public function testProceedToAcceptPendingException($statusId, $statusDescription, $expectedExceptionMessages)
    {
        $stock = $this->createEntityWithStatusIdAndDescription($statusId, $statusDescription);
        $exceptionThrown = false;

        try {
            $stock->proceedToAcceptPending(m::mock(RefData::class));
        } catch (ForbiddenException $e) {
            $exceptionThrown = true;
            $this->assertEquals($expectedExceptionMessages, $e->getMessages());
        }

        $this->assertTrue($exceptionThrown);
    }

    public function proceedToAcceptPendingExceptionProvider()
    {
        return [
            [
                Entity::STATUS_SCORING_NEVER_RUN,
                'Scoring never run',
                ['This stock is not in the correct status to proceed to accept pending (Scoring never run)']
            ],
            [
                Entity::STATUS_SCORING_PENDING,
                'Scoring pending',
                ['This stock is not in the correct status to proceed to accept pending (Scoring pending)']
            ],
            [
                Entity::STATUS_SCORING_IN_PROGRESS,
                'Scoring in progress',
                ['This stock is not in the correct status to proceed to accept pending (Scoring in progress)']
            ],
            [
                Entity::STATUS_SCORING_PREREQUISITE_FAIL,
                'Scoring prerequisite fail',
                ['This stock is not in the correct status to proceed to accept pending (Scoring prerequisite fail)']
            ],
            [
                Entity::STATUS_SCORING_UNEXPECTED_FAIL,
                'Scoring unexpected fail',
                ['This stock is not in the correct status to proceed to accept pending (Scoring unexpected fail)']
            ],
            [
                Entity::STATUS_ACCEPT_PENDING,
                'Accept pending',
                ['This stock is not in the correct status to proceed to accept pending (Accept pending)']
            ],
            [
                Entity::STATUS_ACCEPT_IN_PROGRESS,
                'Accept in progress',
                ['This stock is not in the correct status to proceed to accept pending (Accept in progress)']
            ],
            [
                Entity::STATUS_ACCEPT_SUCCESSFUL,
                'Accept successful',
                ['This stock is not in the correct status to proceed to accept pending (Accept successful)']
            ],
        ];
    }

    public function testProceedToAcceptPrerequisiteFail()
    {
        $stock = $this->createEntityWithStatusId(Entity::STATUS_ACCEPT_PENDING);
        $newStatus = m::mock(RefData::class);

        $stock->proceedToAcceptPrerequisiteFail($newStatus);
        $this->assertSame($newStatus, $stock->getStatus());
    }

    /**
     * @dataProvider proceedToAcceptPrerequisiteFailExceptionProvider
     */
    public function testProceedToAcceptPrerequisiteFailException($statusId, $statusDescription, $expectedExceptionMessages)
    {
        $stock = $this->createEntityWithStatusIdAndDescription($statusId, $statusDescription);
        $exceptionThrown = false;

        try {
            $stock->proceedToAcceptPrerequisiteFail(m::mock(RefData::class));
        } catch (ForbiddenException $e) {
            $exceptionThrown = true;
            $this->assertEquals($expectedExceptionMessages, $e->getMessages());
        }

        $this->assertTrue($exceptionThrown);
    }

    public function proceedToAcceptPrerequisiteFailExceptionProvider()
    {
        return [
            [
                Entity::STATUS_SCORING_NEVER_RUN,
                'Scoring never run',
                ['This stock is not in the correct status to proceed to accept prerequisite fail (Scoring never run)']
            ],
            [
                Entity::STATUS_SCORING_PENDING,
                'Scoring pending',
                ['This stock is not in the correct status to proceed to accept prerequisite fail (Scoring pending)']
            ],
            [
                Entity::STATUS_SCORING_IN_PROGRESS,
                'Scoring in progress',
                ['This stock is not in the correct status to proceed to accept prerequisite fail (Scoring in progress)']
            ],
            [
                Entity::STATUS_SCORING_SUCCESSFUL,
                'Scoring successful',
                ['This stock is not in the correct status to proceed to accept prerequisite fail (Scoring successful)']
            ],
            [
                Entity::STATUS_SCORING_PREREQUISITE_FAIL,
                'Scoring prerequisite fail',
                ['This stock is not in the correct status to proceed to accept prerequisite fail (Scoring prerequisite fail)']
            ],
            [
                Entity::STATUS_SCORING_UNEXPECTED_FAIL,
                'Scoring unexpected fail',
                ['This stock is not in the correct status to proceed to accept prerequisite fail (Scoring unexpected fail)']
            ],
            [
                Entity::STATUS_ACCEPT_IN_PROGRESS,
                'Accept in progress',
                ['This stock is not in the correct status to proceed to accept prerequisite fail (Accept in progress)']
            ],
            [
                Entity::STATUS_ACCEPT_SUCCESSFUL,
                'Accept successful',
                ['This stock is not in the correct status to proceed to accept prerequisite fail (Accept successful)']
            ],
            [
                Entity::STATUS_ACCEPT_PREREQUISITE_FAIL,
                'Accept prerequisite fail',
                ['This stock is not in the correct status to proceed to accept prerequisite fail (Accept prerequisite fail)']
            ],
            [
                Entity::STATUS_ACCEPT_UNEXPECTED_FAIL,
                'Accept unexpected fail',
                ['This stock is not in the correct status to proceed to accept prerequisite fail (Accept unexpected fail)']
            ],
        ];
    }

    public function testProceedToAcceptInProgress()
    {
        $stock = $this->createEntityWithStatusId(Entity::STATUS_ACCEPT_PENDING);
        $newStatus = m::mock(RefData::class);

        $stock->proceedToAcceptInProgress($newStatus);
        $this->assertSame($newStatus, $stock->getStatus());
    }

    /**
     * @dataProvider proceedToAcceptInProgressExceptionProvider
     */
    public function testProceedToAcceptInProgressException($statusId, $statusDescription, $expectedExceptionMessages)
    {
        $stock = $this->createEntityWithStatusIdAndDescription($statusId, $statusDescription);
        $exceptionThrown = false;

        try {
            $stock->proceedToAcceptInProgress(m::mock(RefData::class));
        } catch (ForbiddenException $e) {
            $exceptionThrown = true;
            $this->assertEquals($expectedExceptionMessages, $e->getMessages());
        }

        $this->assertTrue($exceptionThrown);
    }

    public function proceedToAcceptInProgressExceptionProvider()
    {
        return [
            [
                Entity::STATUS_SCORING_PENDING,
                'Scoring pending',
                ['This stock is not in the correct status to proceed to accept in progress (Scoring pending)']
            ],
            [
                Entity::STATUS_SCORING_NEVER_RUN,
                'Scoring never run',
                ['This stock is not in the correct status to proceed to accept in progress (Scoring never run)']
            ],
            [
                Entity::STATUS_SCORING_IN_PROGRESS,
                'Scoring in progress',
                ['This stock is not in the correct status to proceed to accept in progress (Scoring in progress)']
            ],
            [
                Entity::STATUS_SCORING_SUCCESSFUL,
                'Scoring successful',
                ['This stock is not in the correct status to proceed to accept in progress (Scoring successful)']
            ],
            [
                Entity::STATUS_SCORING_PREREQUISITE_FAIL,
                'Scoring prerequisite fail',
                ['This stock is not in the correct status to proceed to accept in progress (Scoring prerequisite fail)']
            ],
            [
                Entity::STATUS_SCORING_UNEXPECTED_FAIL,
                'Scoring unexpected fail',
                ['This stock is not in the correct status to proceed to accept in progress (Scoring unexpected fail)']
            ],
            [
                Entity::STATUS_ACCEPT_IN_PROGRESS,
                'Accept in progress',
                ['This stock is not in the correct status to proceed to accept in progress (Accept in progress)']
            ],
            [
                Entity::STATUS_ACCEPT_SUCCESSFUL,
                'Accept successful',
                ['This stock is not in the correct status to proceed to accept in progress (Accept successful)']
            ],
            [
                Entity::STATUS_ACCEPT_PREREQUISITE_FAIL,
                'Accept prerequisite fail',
                ['This stock is not in the correct status to proceed to accept in progress (Accept prerequisite fail)']
            ],
            [
                Entity::STATUS_ACCEPT_UNEXPECTED_FAIL,
                'Accept unexpected fail',
                ['This stock is not in the correct status to proceed to accept in progress (Accept unexpected fail)']
            ],
        ];
    }

    public function testProceedToAcceptSuccessful()
    {
        $stock = $this->createEntityWithStatusId(Entity::STATUS_ACCEPT_IN_PROGRESS);
        $newStatus = m::mock(RefData::class);

        $stock->proceedToAcceptSuccessful($newStatus);
        $this->assertSame($newStatus, $stock->getStatus());
    }

    /**
     * @dataProvider proceedToAcceptSuccessfulExceptionProvider
     */
    public function testProceedToAcceptSuccessfulException($statusId, $statusDescription, $expectedExceptionMessages)
    {
        $stock = $this->createEntityWithStatusIdAndDescription($statusId, $statusDescription);
        $exceptionThrown = false;

        try {
            $stock->proceedToAcceptSuccessful(m::mock(RefData::class));
        } catch (ForbiddenException $e) {
            $exceptionThrown = true;
            $this->assertEquals($expectedExceptionMessages, $e->getMessages());
        }

        $this->assertTrue($exceptionThrown);
    }

    public function proceedToAcceptSuccessfulExceptionProvider()
    {
        return [
            [
                Entity::STATUS_SCORING_NEVER_RUN,
                'Scoring never run',
                ['This stock is not in the correct status to proceed to accept successful (Scoring never run)']
            ],
            [
                Entity::STATUS_SCORING_PENDING,
                'Scoring pending',
                ['This stock is not in the correct status to proceed to accept successful (Scoring pending)']
            ],
            [
                Entity::STATUS_SCORING_IN_PROGRESS,
                'Scoring in progress',
                ['This stock is not in the correct status to proceed to accept successful (Scoring in progress)']
            ],
            [
                Entity::STATUS_SCORING_SUCCESSFUL,
                'Scoring successful',
                ['This stock is not in the correct status to proceed to accept successful (Scoring successful)']
            ],
            [
                Entity::STATUS_SCORING_PREREQUISITE_FAIL,
                'Scoring prerequisite fail',
                ['This stock is not in the correct status to proceed to accept successful (Scoring prerequisite fail)']
            ],
            [
                Entity::STATUS_SCORING_UNEXPECTED_FAIL,
                'Scoring unexpected fail',
                ['This stock is not in the correct status to proceed to accept successful (Scoring unexpected fail)']
            ],
            [
                Entity::STATUS_ACCEPT_PENDING,
                'Accept pending',
                ['This stock is not in the correct status to proceed to accept successful (Accept pending)']
            ],
            [
                Entity::STATUS_ACCEPT_SUCCESSFUL,
                'Accept successful',
                ['This stock is not in the correct status to proceed to accept successful (Accept successful)']
            ],
            [
                Entity::STATUS_ACCEPT_PREREQUISITE_FAIL,
                'Accept prerequisite fail',
                ['This stock is not in the correct status to proceed to accept successful (Accept prerequisite fail)']
            ],
            [
                Entity::STATUS_ACCEPT_UNEXPECTED_FAIL,
                'Accept unexpected fail',
                ['This stock is not in the correct status to proceed to accept successful (Accept unexpected fail)']
            ],
        ];
    }

    public function testProceedToAcceptUnexpectedFail()
    {
        $stock = $this->createEntityWithStatusId(Entity::STATUS_ACCEPT_IN_PROGRESS);
        $newStatus = m::mock(RefData::class);

        $stock->proceedToAcceptUnexpectedFail($newStatus);
        $this->assertSame($newStatus, $stock->getStatus());
    }

    /**
     * @dataProvider proceedToAcceptUnexpectedFailExceptionProvider
     */
    public function testProceedToAcceptUnexpectedFailException($statusId, $statusDescription, $expectedExceptionMessages)
    {
        $stock = $this->createEntityWithStatusIdAndDescription($statusId, $statusDescription);
        $exceptionThrown = false;

        try {
            $stock->proceedToAcceptUnexpectedFail(m::mock(RefData::class));
        } catch (ForbiddenException $e) {
            $exceptionThrown = true;
            $this->assertEquals($expectedExceptionMessages, $e->getMessages());
        }

        $this->assertTrue($exceptionThrown);
    }

    public function proceedToAcceptUnexpectedFailExceptionProvider()
    {
        return [
            [
                Entity::STATUS_SCORING_NEVER_RUN,
                'Scoring never run',
                ['This stock is not in the correct status to proceed to accept unexpected fail (Scoring never run)']
            ],
            [
                Entity::STATUS_SCORING_PENDING,
                'Scoring pending',
                ['This stock is not in the correct status to proceed to accept unexpected fail (Scoring pending)']
            ],
            [
                Entity::STATUS_SCORING_IN_PROGRESS,
                'Scoring in progress',
                ['This stock is not in the correct status to proceed to accept unexpected fail (Scoring in progress)']
            ],
            [
                Entity::STATUS_SCORING_SUCCESSFUL,
                'Scoring successful',
                ['This stock is not in the correct status to proceed to accept unexpected fail (Scoring successful)']
            ],
            [
                Entity::STATUS_SCORING_PREREQUISITE_FAIL,
                'Scoring prerequisite fail',
                ['This stock is not in the correct status to proceed to accept unexpected fail (Scoring prerequisite fail)']
            ],
            [
                Entity::STATUS_SCORING_UNEXPECTED_FAIL,
                'Scoring unexpected fail',
                ['This stock is not in the correct status to proceed to accept unexpected fail (Scoring unexpected fail)']
            ],
            [
                Entity::STATUS_ACCEPT_PENDING,
                'Accept pending',
                ['This stock is not in the correct status to proceed to accept unexpected fail (Accept pending)']
            ],
            [
                Entity::STATUS_ACCEPT_SUCCESSFUL,
                'Accept successful',
                ['This stock is not in the correct status to proceed to accept unexpected fail (Accept successful)']
            ],
            [
                Entity::STATUS_ACCEPT_PREREQUISITE_FAIL,
                'Accept prerequisite fail',
                ['This stock is not in the correct status to proceed to accept unexpected fail (Accept prerequisite fail)']
            ],
            [
                Entity::STATUS_ACCEPT_UNEXPECTED_FAIL,
                'Accept unexpected fail',
                ['This stock is not in the correct status to proceed to accept unexpected fail (Accept unexpected fail)']
            ],
        ];
    }

    private function createEntityWithStatusIdAndDescription($statusId, $description)
    {
        $status = m::mock(RefData::class);
        $status->shouldReceive('getId')
            ->andReturn($statusId);
        $status->shouldReceive('getDescription')
            ->andReturn($description);

        return $this->createEntityWithStatus($status);
    }

    private function createEntityWithStatusId($statusId)
    {
        $status = m::mock(RefData::class);
        $status->shouldReceive('getId')
            ->andReturn($statusId);

        return $this->createEntityWithStatus($status);
    }

    private function createEntityWithStatus($status)
    {
        $irhpPermitType = m::mock(IrhpPermitType::class)->makePartial();
        $irhpStockEntity = Entity::create(
            $irhpPermitType,
            null,
            1400,
            $status,
            null,
            null,
            null,
            '2019-01-01',
            '2019-02-01'
        );
        $irhpPermitType->shouldReceive('getId')
            ->andReturn(3);
        return($irhpStockEntity);
    }

    public function testGetNonReservedNonReplacementRangesOrderedByFromNo()
    {
        $entity = m::mock(Entity::class)->makePartial();

        $firstExpectedRange = $this->createMockRange(false, false, 300, RefData::EMISSIONS_CATEGORY_EURO5_REF);
        $secondExpectedRange = $this->createMockRange(false, false, 420, RefData::EMISSIONS_CATEGORY_EURO6_REF);
        $thirdExpectedRange = $this->createMockRange(false, false, 500, RefData::EMISSIONS_CATEGORY_EURO5_REF);

        $irhpPermitRanges = new ArrayCollection(
            [
                $secondExpectedRange,
                $this->createMockRange(false, true, 230, RefData::EMISSIONS_CATEGORY_EURO5_REF),
                $this->createMockRange(true, false, 100, RefData::EMISSIONS_CATEGORY_EURO6_REF),
                $this->createMockRange(true, true, 500, RefData::EMISSIONS_CATEGORY_EURO5_REF),
                $firstExpectedRange,
                $thirdExpectedRange
            ]
        );

        $entity->shouldReceive('getIrhpPermitRanges')
            ->andReturn($irhpPermitRanges);

        $result = $entity->getNonReservedNonReplacementRangesOrderedByFromNo();

        $this->assertInstanceOf(ArrayCollection::class, $result);

        $resultAsArray = array_values($result->toArray());
        $this->assertSame($firstExpectedRange, $resultAsArray[0]);
        $this->assertSame($secondExpectedRange, $resultAsArray[1]);
        $this->assertSame($thirdExpectedRange, $resultAsArray[2]);
    }

    public function testGetNonReservedNonReplacementRangesOrderedByFromNoWithEmissionsCategoryId()
    {
        $entity = m::mock(Entity::class)->makePartial();

        $firstExpectedRange = $this->createMockRange(false, false, 300, RefData::EMISSIONS_CATEGORY_EURO5_REF);
        $secondExpectedRange = $this->createMockRange(false, false, 500, RefData::EMISSIONS_CATEGORY_EURO5_REF);

        $irhpPermitRanges = new ArrayCollection(
            [
                $this->createMockRange(false, false, 600, RefData::EMISSIONS_CATEGORY_EURO6_REF),
                $this->createMockRange(false, true, 700, RefData::EMISSIONS_CATEGORY_EURO5_REF),
                $secondExpectedRange,
                $this->createMockRange(true, false, 800, RefData::EMISSIONS_CATEGORY_EURO6_REF),
                $this->createMockRange(true, true, 900, RefData::EMISSIONS_CATEGORY_EURO5_REF),
                $firstExpectedRange,
                $this->createMockRange(false, false, 1000, RefData::EMISSIONS_CATEGORY_EURO6_REF),
                $this->createMockRange(false, true, 110, RefData::EMISSIONS_CATEGORY_EURO5_REF),
                $this->createMockRange(true, false, 1200, RefData::EMISSIONS_CATEGORY_EURO6_REF),
                $this->createMockRange(true, true, 1300, RefData::EMISSIONS_CATEGORY_EURO6_REF)
            ]
        );

        $entity->shouldReceive('getIrhpPermitRanges')
            ->andReturn($irhpPermitRanges);

        $result = $entity->getNonReservedNonReplacementRangesOrderedByFromNo(RefData::EMISSIONS_CATEGORY_EURO5_REF);

        $this->assertInstanceOf(ArrayCollection::class, $result);

        $resultAsArray = array_values($result->toArray());
        $this->assertSame($firstExpectedRange, $resultAsArray[0]);
        $this->assertSame($secondExpectedRange, $resultAsArray[1]);
    }

    private function createMockRange($ssReserve, $lostReplacement, $fromNo, $emissionsCategoryId)
    {
        $irhpPermitRange = m::mock(IrhpPermitRange::class);
        $irhpPermitRange->shouldReceive('getSsReserve')
            ->andReturn($ssReserve);
        $irhpPermitRange->shouldReceive('getLostReplacement')
            ->andReturn($lostReplacement);
        $irhpPermitRange->shouldReceive('getFromNo')
            ->andReturn($fromNo);
        $irhpPermitRange->shouldReceive('getEmissionsCategory->getId')
            ->andReturn($emissionsCategoryId);

        return $irhpPermitRange;
    }

    public function testGetValidityYear()
    {
        $dateTime = new DateTime('2015-12-31');

        $entity = m::mock(Entity::class)->makePartial();
        $entity->shouldReceive('getValidTo')
            ->with(true)
            ->andReturn($dateTime);

        $this->assertEquals(2015, $entity->getValidityYear());
    }

    public function testGetValidityYearNullValidTo()
    {
        $entity = m::mock(Entity::class)->makePartial();
        $entity->shouldReceive('getValidTo')
            ->with(true)
            ->andReturn(null);

        $this->assertNull($entity->getValidityYear());
    }

    /**
     * @dataProvider dpGetAllocationMode
     */
    public function testGetAllocationMode($irhpPermitTypeId, $businessProcessId, $expectedAllocationMode)
    {
        $businessProcess = m::mock(RefData::class);
        $businessProcess->shouldReceive('getId')
            ->andReturn($businessProcessId);

        $entity = m::mock(Entity::class)->makePartial();
        $entity->shouldReceive('getIrhpPermitType->getId')
            ->withNoArgs()
            ->andReturn($irhpPermitTypeId);
        $entity->setBusinessProcess($businessProcess);

        $this->assertEquals(
            $expectedAllocationMode,
            $entity->getAllocationMode()
        );
    }

    public function dpGetAllocationMode()
    {
        return [
            [
                IrhpPermitType::IRHP_PERMIT_TYPE_ID_BILATERAL,
                RefData::BUSINESS_PROCESS_APG,
                Entity::ALLOCATION_MODE_STANDARD,
            ],
            [
                IrhpPermitType::IRHP_PERMIT_TYPE_ID_MULTILATERAL,
                RefData::BUSINESS_PROCESS_APG,
                Entity::ALLOCATION_MODE_STANDARD,
            ],
            [
                IrhpPermitType::IRHP_PERMIT_TYPE_ID_ECMT_SHORT_TERM,
                RefData::BUSINESS_PROCESS_APGG,
                Entity::ALLOCATION_MODE_EMISSIONS_CATEGORIES,
            ],
            [
                IrhpPermitType::IRHP_PERMIT_TYPE_ID_ECMT_SHORT_TERM,
                RefData::BUSINESS_PROCESS_APSG,
                Entity::ALLOCATION_MODE_CANDIDATE_PERMITS,
            ],
            [
                IrhpPermitType::IRHP_PERMIT_TYPE_ID_ECMT_REMOVAL,
                RefData::BUSINESS_PROCESS_APG,
                Entity::ALLOCATION_MODE_STANDARD_WITH_EXPIRY,
            ],
        ];
    }

    public function testGetAllocationModeException()
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage(
            'No allocation mode set for permit type ' . IrhpPermitType::IRHP_PERMIT_TYPE_ID_BILATERAL
        );

        $businessProcess = m::mock(RefData::class);
        $businessProcess->shouldReceive('getId')
            ->andReturn(RefData::BUSINESS_PROCESS_APSG);

        $entity = m::mock(Entity::class)->makePartial();
        $entity->shouldReceive('getIrhpPermitType->getId')
            ->withNoArgs()
            ->andReturn(IrhpPermitType::IRHP_PERMIT_TYPE_ID_BILATERAL);
        $entity->setBusinessProcess($businessProcess);

        $entity->getAllocationMode();
    }
}
