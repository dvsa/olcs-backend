<?php

namespace Dvsa\OlcsTest\Api\Entity\DataRetention;

use Dvsa\OlcsTest\Api\Entity\Abstracts\EntityTester;
use Dvsa\Olcs\Api\Entity\DataRetention\DataRetention as Entity;

/**
 * DataRetention Entity Unit Tests
 *
 * Initially auto-generated but won't be overridden
 */
class DataRetentionEntityTest extends EntityTester
{
    /**
     * Define the entity to test
     *
     * @var string
     */
    protected $entityClass = Entity::class;

    public function testMarkForDelete()
    {
        $dataRetentionEntity = new Entity();
        $dataRetentionEntity->setActionConfirmation(false);

        $expectedEntity = new Entity();
        $expectedEntity->setActionConfirmation(true);
        $expectedEntity->setActionedDate(new \DateTime('now'));

        $this->assertEquals(
            $dataRetentionEntity->markForDelete(),
            $expectedEntity
        );
    }

    public function testMarkForReview()
    {
        $dataRetentionEntity = new Entity();
        $dataRetentionEntity->setActionConfirmation(true);
        $dataRetentionEntity->setActionedDate(new \DateTime('now'));

        $expectedEntity = new Entity();
        $expectedEntity->setActionConfirmation(false);
        $expectedEntity->setActionedDate(null);

        $this->assertEquals(
            $dataRetentionEntity->markForReview(),
            $expectedEntity
        );
    }

    public function testMarkForDeleteButHasReviewDate()
    {
        $dataRetentionEntity = new Entity();
        $dataRetentionEntity->setActionConfirmation(false);
        $dataRetentionEntity->setNextReviewDate(new \DateTime('now'));

        $expectedEntity = new Entity();
        $expectedEntity->setActionConfirmation(false);
        $expectedEntity->setActionedDate(null);
        $expectedEntity->setNextReviewDate(new \DateTime('now'));

        $this->assertEquals(
            $dataRetentionEntity->markForDelete(),
            $expectedEntity
        );
    }
}
