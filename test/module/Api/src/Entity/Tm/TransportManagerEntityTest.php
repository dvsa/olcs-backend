<?php

namespace Dvsa\OlcsTest\Api\Entity\Tm;

use Dvsa\OlcsTest\Api\Entity\Abstracts\EntityTester;
use Dvsa\Olcs\Api\Entity\Tm\TransportManager as Entity;

/**
 * TransportManager Entity Unit Tests
 *
 * Initially auto-generated but won't be overridden
 */
class TransportManagerEntityTest extends EntityTester
{
    /**
     * Define the entity to test
     *
     * @var string
     */
    protected $entityClass = Entity::class;

    public function testUpdatePerson()
    {
        $entity = new Entity();

        $entity->updateTransportManager('tmtype', 'tmstatus', 1, 2, 3);

        $this->assertEquals('tmtype', $entity->getTmType());
        $this->assertEquals('tmstatus', $entity->getTmStatus());
        $this->assertEquals(1, $entity->getWorkCd());
        $this->assertEquals(2, $entity->getHomeCd());
        $this->assertEquals(3, $entity->getCreatedBy());
    }
}
