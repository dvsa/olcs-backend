<?php

namespace Dvsa\OlcsTest\Api\Entity\Person;

use Dvsa\OlcsTest\Api\Entity\Abstracts\EntityTester;
use Dvsa\Olcs\Api\Entity\Person\Person as Entity;
use Dvsa\Olcs\Api\Entity\System\RefData;
use Mockery as m;

/**
 * Person Entity Unit Tests
 *
 * Initially auto-generated but won't be overridden
 */
class PersonEntityTest extends EntityTester
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
        $title = m::mock(RefData::class);

        $entity->updatePerson('forename', 'familyname', $title, '2015-01-01', 'bplace');

        $this->assertSame($title, $entity->getTitle());
        $this->assertEquals('forename', $entity->getForename());
        $this->assertEquals('familyname', $entity->getFamilyName());
        $this->assertEquals(new \DateTime('2015-01-01'), $entity->getBirthDate());
        $this->assertEquals('bplace', $entity->getBirthPlace());
    }
}
