<?php

namespace Dvsa\OlcsTest\Api\Entity\Licence;

use Mockery as m;
use Dvsa\OlcsTest\Api\Entity\Abstracts\EntityTester;
use Dvsa\Olcs\Api\Entity\Licence\GracePeriod as Entity;

/**
 * GracePeriod Entity Unit Tests
 *
 * Initially auto-generated but won't be overridden
 */
class GracePeriodEntityTest extends EntityTester
{
    /**
     * Define the entity to test
     *
     * @var string
     */
    protected $entityClass = Entity::class;

    public function testIsActiveWhenActive()
    {
        $gracePeriod = m::mock(Entity::class)->makePartial();
        $gracePeriod->shouldReceive('getStartDate')
            ->andReturn('2015-01-01')
            ->twice()
            ->shouldReceive('getEndDate')
            ->andReturn('2015-01-03')
            ->twice();

        $this->assertTrue($gracePeriod->isActive('2015-01-02'));
    }
}
