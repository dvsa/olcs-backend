<?php

namespace Dvsa\OlcsTest\Api\Entity\Bus;

use Dvsa\OlcsTest\Api\Entity\Abstracts\EntityTester;
use Dvsa\Olcs\Api\Entity\Bus\BusNoticePeriod as Entity;

/**
 * BusNoticePeriod Entity Unit Tests
 *
 * Initially auto-generated but won't be overridden
 */
class BusNoticePeriodEntityTest extends EntityTester
{
    /**
     * Define the entity to test
     *
     * @var string
     */
    protected $entityClass = Entity::class;

    public function testIsScottishRules()
    {
        $busNotice = new Entity();
        $busNotice->setId(Entity::NOTICE_PERIOD_SCOTLAND);
        $this->assertTrue($busNotice->isScottishRules());
        $busNotice->setId(Entity::NOTICE_PERIOD_OTHER);
        $this->assertFalse($busNotice->isScottishRules());
    }
}
