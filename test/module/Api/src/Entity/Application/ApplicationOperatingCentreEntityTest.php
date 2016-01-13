<?php

namespace Dvsa\OlcsTest\Api\Entity\Application;

use Mockery as m;
use Dvsa\Olcs\Api\Entity\Application\Application;
use Dvsa\OlcsTest\Api\Entity\Abstracts\EntityTester;
use Dvsa\Olcs\Api\Entity\Application\ApplicationOperatingCentre as Entity;
use Dvsa\Olcs\Api\Entity\Application\S4;

/**
 * ApplicationOperatingCentre Entity Unit Tests
 *
 * Initially auto-generated but won't be overridden
 */
class ApplicationOperatingCentreEntityTest extends EntityTester
{
    /**
     * Define the entity to test
     *
     * @var string
     */
    protected $entityClass = Entity::class;

    /**
     * Get/setup and S4
     *
     * @param string $outcomeId refdata id
     *
     * @return S4
     */
    protected function setupS4($outcomeId = null)
    {
        $s4 = $this->instantiate(S4::class);
        if ($outcomeId !== null) {
            $outcome = new \Dvsa\Olcs\Api\Entity\System\RefData();
            $outcome->setId($outcomeId);
            $s4->setOutcome($outcome);
        }

        return $s4;
    }

    public function testCanDeleteNoS4()
    {
        $entity = $this->instantiate(Entity::class);

        $checkCanDelete = $entity->checkCanDelete();
        $this->assertSame([], $checkCanDelete);
    }

    public function testCanDeleteS4WithoutOutcome()
    {
        $entity = $this->instantiate(Entity::class);
        $entity->setS4($this->setupS4());
        $this->assertArrayHasKey('OC_CANNOT_DELETE_HAS_S4', $entity->checkCanDelete());
    }

    public function testCanDeleteS4OutcomeApproved()
    {
        $entity = $this->instantiate(Entity::class);
        $entity->setS4($this->setupS4(S4::STATUS_APPROVED));
        $this->assertArrayHasKey('OC_CANNOT_DELETE_HAS_S4', $entity->checkCanDelete());
    }

    public function testCanDeleteS4OutcomeRejected()
    {
        $entity = $this->instantiate(Entity::class);
        $entity->setS4($this->setupS4(S4::STATUS_REFUSED));

        $checkCanDelete = $entity->checkCanDelete();
        $this->assertSame([], $checkCanDelete);
    }

    public function testGetRelatedOrganisation()
    {
        $org = m::mock();

        $application = m::mock(Application::class);
        $application->shouldReceive('getRelatedOrganisation')->andReturn($org);

        $entity = $this->instantiate(Entity::class);
        $entity->setApplication($application);

        $this->assertSame($org, $entity->getRelatedOrganisation());
    }
}
