<?php

namespace Dvsa\OlcsTest\Api\Entity\Queue;

use Dvsa\OlcsTest\Api\Entity\Abstracts\EntityTester;
use Dvsa\Olcs\Api\Entity\Queue\Queue as Entity;
use Dvsa\Olcs\Api\Entity\System\RefData;

/**
 * Queue Entity Unit Tests
 *
 * Initially auto-generated but won't be overridden
 */
class QueueEntityTest extends EntityTester
{
    /**
     * Define the entity to test
     *
     * @var string
     */
    protected $entityClass = Entity::class;

    public function testConstructorWithType()
    {
        $type = new RefData('foo');
        $sut = new $this->entityClass($type);

        $this->assertSame($type, $sut->getType());
    }

    public function testIncrementAttempts()
    {
        $sut = $this->instantiate($this->entityClass);

        $this->assertEquals(0, $sut->getAttempts());
        $sut->incrementAttempts();
        $this->assertEquals(1, $sut->getAttempts());
        $sut->incrementAttempts();
        $this->assertEquals(2, $sut->getAttempts());
    }
}
