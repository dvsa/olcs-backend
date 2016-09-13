<?php

namespace Dvsa\OlcsTest\Api\Entity\Cases;

use Dvsa\OlcsTest\Api\Entity\Abstracts\EntityTester;
use Dvsa\Olcs\Api\Entity\Cases\Impounding as Entity;
use Dvsa\Olcs\Api\Entity\Cases\Cases as CasesEntity;
use Dvsa\Olcs\Api\Entity\System\RefData as RefDataEntity;
use Mockery as m;

/**
 * Impounding Entity Unit Tests
 *
 * Initially auto-generated but won't be overridden
 */
class ImpoundingEntityTest extends EntityTester
{
    /**
     * Define the entity to test
     *
     * @var string
     */
    protected $entityClass = Entity::class;

    public function testSetOtherVenueProperties()
    {
        $mockCase = m::mock(CasesEntity::class);
        $mockImpoundingType = m::mock(RefDataEntity::class);

        $sut = new Entity($mockCase, $mockImpoundingType);

        $sut->setVenueProperties(Entity::VENUE_OTHER, 'foo');

        $this->assertNull($sut->getVenue());
        $this->assertEquals('foo', $sut->getVenueOther());
    }
}
