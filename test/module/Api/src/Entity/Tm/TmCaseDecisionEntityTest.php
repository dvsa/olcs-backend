<?php

namespace Dvsa\OlcsTest\Api\Entity\Tm;

use Dvsa\Olcs\Api\Entity\System\RefData;
use Dvsa\Olcs\Api\Entity\Cases\Cases as CasesEntity;
use Dvsa\Olcs\Api\Entity\Tm\TmCaseDecision as Entity;
use Dvsa\OlcsTest\Api\Entity\Abstracts\EntityTester;
use Mockery as m;

/**
 * TmCaseDecision Entity Unit Tests
 *
 * Initially auto-generated but won't be overridden
 */
class TmCaseDecisionEntityTest extends EntityTester
{
    /**
     * Define the entity to test
     *
     * @var string
     */
    protected $entityClass = Entity::class;

    public function testConstruct()
    {
        $case = m::mock(CasesEntity::class);
        $decision = m::mock(RefData::class);

        $entity = new Entity($case, $decision);

        $this->assertSame($case, $entity->getCase());
        $this->assertSame($decision, $entity->getDecision());
    }

    public function testCreateForReputeNotLost()
    {
        $data = [
            'case' => 11,
            'isMsi' => 'Y',
            'decisionDate' => '2016-01-01',
            'notifiedDate' => '2016-01-01',
            'reputeNotLostReason' => 'testing',
        ];

        $case = m::mock(CasesEntity::class);

        $decision = m::mock(RefData::class)->makePartial();
        $decision->setId(Entity::DECISION_REPUTE_NOT_LOST);

        $entity = Entity::create($case, $decision, $data);

        $this->assertSame($case, $entity->getCase());
        $this->assertSame($decision, $entity->getDecision());
        $this->assertEquals($data['isMsi'], $entity->getIsMsi());
        $this->assertInstanceOf(\DateTime::class, $entity->getDecisionDate());
        $this->assertEquals($data['decisionDate'], $entity->getDecisionDate()->format('Y-m-d'));
        $this->assertInstanceOf(\DateTime::class, $entity->getNotifiedDate());
        $this->assertEquals($data['notifiedDate'], $entity->getNotifiedDate()->format('Y-m-d'));
        $this->assertEquals($data['reputeNotLostReason'], $entity->getReputeNotLostReason());
    }

    public function testUpdateForReputeNotLost()
    {
        $data = [
            'isMsi' => 'Y',
            'decisionDate' => '2016-01-01',
            'notifiedDate' => '2016-01-01',
            'reputeNotLostReason' => 'testing',
        ];

        $case = m::mock(CasesEntity::class);

        $decision = m::mock(RefData::class)->makePartial();
        $decision->setId(Entity::DECISION_REPUTE_NOT_LOST);

        $entity = new Entity($case, $decision);

        // set existing data on the entity before update
        $entity->setIsMsi('N');
        $entity->setDecisionDate(new \DateTime('2015-10-12'));

        $entity->update($data);

        $this->assertSame($case, $entity->getCase());
        $this->assertSame($decision, $entity->getDecision());
        $this->assertEquals($data['isMsi'], $entity->getIsMsi());
        $this->assertInstanceOf(\DateTime::class, $entity->getDecisionDate());
        $this->assertEquals($data['decisionDate'], $entity->getDecisionDate()->format('Y-m-d'));
        $this->assertInstanceOf(\DateTime::class, $entity->getNotifiedDate());
        $this->assertEquals($data['notifiedDate'], $entity->getNotifiedDate()->format('Y-m-d'));
        $this->assertEquals($data['reputeNotLostReason'], $entity->getReputeNotLostReason());
    }
}
