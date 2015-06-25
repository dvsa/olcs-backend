<?php

namespace Dvsa\OlcsTest\Api\Entity\CommunityLic;

use Dvsa\OlcsTest\Api\Entity\Abstracts\EntityTester;
use Dvsa\Olcs\Api\Entity\CommunityLic\CommunityLic as Entity;
use Mockery as m;

/**
 * CommunityLic Entity Unit Tests
 *
 * Initially auto-generated but won't be overridden
 */
class CommunityLicEntityTest extends EntityTester
{
    /**
     * Define the entity to test
     *
     * @var string
     */
    protected $entityClass = Entity::class;

    public function testUpdateCommunityLic()
    {
        $data = [
            'status' => 'status',
            'specifiedDate' => '2015-01-01',
            'serialNoPrefix' => 'A',
            'licence' => 1,
            'issueNo' => 0
        ];

        $sut = m::mock(Entity::class)->makePartial();
        $sut->updateCommunityLic($data);

        $this->assertEquals('status', $sut->getStatus());
        $this->assertEquals('2015-01-01', $sut->getSpecifiedDate());
        $this->assertEquals('A', $sut->getSerialNoPrefix());
        $this->assertEquals(1, $sut->getLicence());
        $this->assertEquals(0, $sut->getIssueNo());
    }

    public function testChangeStatusAndExpiryDate()
    {
        $sut = m::mock(Entity::class)->makePartial();
        $sut->changeStatusAndExpiryDate('status', '2015-01-01');
        $this->assertEquals('status', $sut->getStatus());
        $this->assertEquals('2015-01-01', $sut->getExpiredDate());
    }
}
