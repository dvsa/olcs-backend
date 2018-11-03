<?php

namespace Dvsa\OlcsTest\Api\Entity\Permits;

use Dvsa\OlcsTest\Api\Entity\Abstracts\EntityTester;
use Dvsa\Olcs\Api\Entity\Permits\IrhpCandidatePermit as Entity;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitApplication as IrhpPermitApplicationEntity;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitRange as IrhpPermitRangeEntity;
use Mockery as m;

/**
 * IrhpCandidatePermit Entity Unit Tests
 *
 * Initially auto-generated but won't be overridden
 */
class IrhpCandidatePermitEntityTest extends EntityTester
{
    /**
     * Define the entity to test
     *
     * @var string
     */
    protected $entityClass = Entity::class;

    public function testPrepareForScoring()
    {
        $candidatePermit = Entity::createNew(m::mock(IrhpPermitApplicationEntity::class));
        $candidatePermit->setSuccessful(1);
        $candidatePermit->setIrhpPermitRange(m::mock(IrhpPermitRangeEntity::class));

        $candidatePermit->prepareForScoring();
        $this->assertEquals(0, $candidatePermit->getSuccessful());
        $this->assertNull($candidatePermit->getIrhpPermitRange());
    }

    public function testHasRandomizedScore()
    {
        $candidatePermit = Entity::createNew(m::mock(IrhpPermitApplicationEntity::class));

        $this->assertFalse($candidatePermit->hasRandomizedScore());
        $candidatePermit->setRandomizedScore(0.123);
        $this->assertTrue($candidatePermit->hasRandomizedScore());
    }

    public function testApplyRandomizedScore()
    {
        $deviationData = [
            'licenceData' => [
                'PD2737280' => [12, 15, 7]
            ],
            'meanDeviation' => 1.5
        ];

        $candidatePermit = Entity::createNew(
            m::mock(IrhpPermitApplicationEntity::class)
        );

        $candidatePermit->applyRandomizedScore($deviationData, 'PD2737280');
        $this->assertNotNull($candidatePermit->getRandomFactor());
        $this->assertNotNull($candidatePermit->getRandomizedScore());
    }

    public function testApplyRange()
    {
        $candidatePermit = Entity::createNew(
            m::mock(IrhpPermitApplicationEntity::class)
        );

        $range = m::mock(IrhpPermitRangeEntity::class);
        $candidatePermit->applyRange($range);
        $this->assertSame($range, $candidatePermit->getIrhpPermitRange());
    }
}
