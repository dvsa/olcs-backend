<?php

namespace Dvsa\OlcsTest\Api\Entity\Queue;

use Dvsa\OlcsTest\Api\Entity\Abstracts\EntityTester;
use Dvsa\Olcs\Api\Entity\Queue\Queue as Entity;
use Dvsa\Olcs\Api\Entity\System\RefData;
use Dvsa\Olcs\Api\Domain\Exception\ValidationException;

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

    public function testValidateQueue()
    {
        $sut = new $this->entityClass();
        $this->assertNull(
            $sut->validateQueue(
                Entity::TYPE_CPID_EXPORT_CSV,
                Entity::STATUS_QUEUED
            )
        );
    }

    /**
     * @dataProvider queueDataProvider
     */
    public function testValidateQueueWithException($type, $status)
    {
        $this->setExpectedException(ValidationException::class);
        $sut = new $this->entityClass();
        $sut->validateQueue($type, $status);
    }

    public function queueDataProvider()
    {
        return [
            [Entity::TYPE_COMPANIES_HOUSE_INITIAL, 'foo'],
            ['bar', Entity::STATUS_QUEUED]
        ];
    }
}
