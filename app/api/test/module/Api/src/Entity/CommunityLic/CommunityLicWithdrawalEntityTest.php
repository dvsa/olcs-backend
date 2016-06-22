<?php

namespace Dvsa\OlcsTest\Api\Entity\CommunityLic;

use Dvsa\OlcsTest\Api\Entity\Abstracts\EntityTester;
use Dvsa\Olcs\Api\Entity\CommunityLic\CommunityLicWithdrawal as Entity;
use Mockery as m;

/**
 * CommunityLicWithdrawal Entity Unit Tests
 *
 * Initially auto-generated but won't be overridden
 */
class CommunityLicWithdrawalEntityTest extends EntityTester
{
    /**
     * Define the entity to test
     *
     * @var string
     */
    protected $entityClass = Entity::class;

    public function testUpdateCommunityLicWithdrawal()
    {
        $communityLic = 1;
        $sut = m::mock(Entity::class)->makePartial();
        $sut->updateCommunityLicWithdrawal($communityLic);
        $this->assertEquals($communityLic, $sut->getCommunityLic());
        $this->assertEquals(new \DateTime(), $sut->getStartDate());
    }
}
