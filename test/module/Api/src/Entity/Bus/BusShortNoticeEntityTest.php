<?php

namespace Dvsa\OlcsTest\Api\Entity\Bus;

use Dvsa\OlcsTest\Api\Entity\Abstracts\EntityTester;
use Dvsa\Olcs\Api\Entity\Bus\BusShortNotice as Entity;

/**
 * BusShortNotice Entity Unit Tests
 *
 * Initially auto-generated but won't be overridden
 */
class BusShortNoticeEntityTest extends EntityTester
{
    public function setUp()
    {
        $this->entity = $this->instantiate($this->entityClass);
    }

    /**
     * Define the entity to test
     *
     * @var string
     */
    protected $entityClass = Entity::class;

    /**
     * Tests calculated values
     */
    public function testGetCalculatedValues()
    {
        $result = $this->entity->getCalculatedValues();
        $this->assertEquals($result['busReg'], null);
    }
}
