<?php

namespace Dvsa\OlcsTest\Api\Entity\CommunityLic;

use Dvsa\OlcsTest\Api\Entity\Abstracts\EntityTester;
use Dvsa\Olcs\Api\Entity\CommunityLic\CommunityLicWithdrawalReason as Entity;
use Mockery as m;

/**
 * CommunityLicWithdrawalReason Entity Unit Tests
 *
 * Initially auto-generated but won't be overridden
 */
class CommunityLicWithdrawalReasonEntityTest extends EntityTester
{
    /**
     * Define the entity to test
     *
     * @var string
     */
    protected $entityClass = Entity::class;

    public function testUpdateReason()
    {
        $withdrawalId = 1;
        $type = 'type';
        $sut = m::mock(Entity::class)->makePartial();
        $sut->updateReason($withdrawalId, $type);
        $this->assertEquals($withdrawalId, $sut->getCommunityLicWithdrawal());
        $this->assertEquals($type, $sut->getType());
    }
}
