<?php

namespace Dvsa\OlcsTest\Api\Entity\Permits;

use Dvsa\OlcsTest\Api\Entity\Abstracts\EntityTester;
use Dvsa\Olcs\Api\Domain\Exception\ForbiddenException;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitStock as Entity;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitType;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitRange;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitWindow;
use Dvsa\Olcs\Api\Entity\System\RefData;
use Mockery as m;
use Dvsa\Olcs\Api\Entity\Traits\ProcessDateTrait;

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

        $entity = Entity::create($irhpPermitType, $validFrom, $validTo, $initialStock, $status);

        $this->assertEquals($irhpPermitType, $entity->getIrhpPermitType());
        $this->assertEquals($expectedFrom, $entity->getValidFrom());
        $this->assertEquals($expectedTo, $entity->getValidTo());
        $this->assertEquals($initialStock, $entity->getInitialStock());
        $this->assertEquals($status, $entity->getStatus());

        $entity->update($irhpPermitType, $updateValidFrom, $updateValidTo, $updateInitialStock);

        $this->assertEquals($updateExpectedFrom, $entity->getValidFrom());
        $this->assertEquals($updateExpectedTo, $entity->getValidTo());
        $this->assertEquals($updateInitialStock, $entity->getInitialStock());
    }

    public function testGetStatusDescription()
    {
        $statusDescription = 'status description';

        $status = m::mock(RefData::class);
        $status->shouldReceive('getDescription')
            ->andReturn($statusDescription);

        $stock = Entity::create(
            m::mock(IrhpPermitType::class),
            '2019-01-01',
            '2019-02-01',
            1400,
            $status
        );

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

        $stock = Entity::create(
            m::mock(IrhpPermitType::class),
            '2019-01-01',
            '2019-02-01',
            1400,
            $stock
        );

        $stock->setIrhpPermitRanges($data['irhpPermitRanges']);
        $stock->setIrhpPermitWindows($data['irhpPermitWindows']);

        $this->assertEquals($expected, $stock->canDelete($data));
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
            [Entity::STATUS_ACCEPT_SUCCESSFUL, false],
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
                Entity::STATUS_ACCEPT_SUCCESSFUL,
                'Accept successful',
                ['This stock is not in the correct status to proceed to scoring pending (Accept successful)']
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
        return Entity::create(
            m::mock(IrhpPermitType::class),
            '2019-01-01',
            '2019-02-01',
            1400,
            $status
        );
    }
}
