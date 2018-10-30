<?php

namespace Dvsa\OlcsTest\Api\Entity\Permits;

use Dvsa\OlcsTest\Api\Entity\Abstracts\EntityTester;
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

    public function testCreateNew()
    {
        $issueDate = m::mock(DateTime::class);
        $status = m::mock(RefData::class);
        $permitNumber = 431;
        
        $irhpPermitApplication = m::mock(IrhpPermitApplication::class);
        $irhpPermitRange = m::mock(IrhpPermitRange::class);

        $irhpCandidatePermit = m::mock(IrhpCandidatePermit::class);
        $irhpCandidatePermit->shouldReceive('getIrhpPermitApplication')
            ->andReturn($irhpPermitApplication);
        $irhpCandidatePermit->shouldReceive('getIrhpPermitRange')
            ->andReturn($irhpPermitRange);

        $entity = Entity::createNew($irhpCandidatePermit, $issueDate, $status, $permitNumber);

        $this->assertSame($irhpCandidatePermit, $entity->getIrhpCandidatePermit());
        $this->assertSame($irhpPermitApplication, $entity->getIrhpPermitApplication());
        $this->assertSame($irhpPermitRange, $entity->getIrhpPermitRange());
        $this->assertSame($issueDate, $entity->getIssueDate());
        $this->assertSame($permitNumber, $entity->getPermitNumber());
        $this->assertSame($status, $entity->getStatus());
    }
}
