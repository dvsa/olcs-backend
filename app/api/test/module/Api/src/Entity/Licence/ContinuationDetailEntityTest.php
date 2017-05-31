<?php

namespace Dvsa\OlcsTest\Api\Entity\Licence;

use Dvsa\OlcsTest\Api\Entity\Abstracts\EntityTester;
use Dvsa\Olcs\Api\Entity\Licence\ContinuationDetail as Entity;
use Mockery as m;

/**
 * ContinuationDetail Entity Unit Tests
 */
class ContinuationDetailEntityTest extends EntityTester
{
    /**
     * Define the entity to test
     *
     * @var string
     */
    protected $entityClass = Entity::class;

    public function testGetRelatedOrganisation()
    {
        /** @var Entity $continuationDetail */
        $continuationDetail = m::mock(Entity::class)->makePartial();
        $continuationDetail->shouldReceive('getLicence->getOrganisation')->andReturn('ORG');

        $this->assertEquals('ORG', $continuationDetail->getRelatedOrganisation());
    }
}
