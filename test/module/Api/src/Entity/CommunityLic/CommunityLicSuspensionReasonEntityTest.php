<?php

namespace Dvsa\OlcsTest\Api\Entity\CommunityLic;

use Dvsa\OlcsTest\Api\Entity\Abstracts\EntityTester;
use Dvsa\Olcs\Api\Entity\CommunityLic\CommunityLicSuspensionReason as Entity;
use Mockery as m;

/**
 * CommunityLicSuspensionReason Entity Unit Tests
 *
 * Initially auto-generated but won't be overridden
 */
class CommunityLicSuspensionReasonEntityTest extends EntityTester
{
    /**
     * Define the entity to test
     *
     * @var string
     */
    protected $entityClass = Entity::class;

    public function testUpdateReason()
    {
        $suspensionId = 1;
        $type = 'type';
        $sut = m::mock(Entity::class)->makePartial();
        $sut->updateReason($suspensionId, $type);
        $this->assertEquals($suspensionId, $sut->getCommunityLicSuspension());
        $this->assertEquals($type, $sut->getType());
    }
}
