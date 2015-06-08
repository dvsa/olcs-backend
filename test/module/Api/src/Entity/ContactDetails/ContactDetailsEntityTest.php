<?php

namespace Dvsa\OlcsTest\Api\Entity\ContactDetails;

use Mockery as m;
use Dvsa\Olcs\Api\Entity\ContactDetails\ContactDetails;
use Dvsa\Olcs\Api\Entity\System\RefData;
use Dvsa\OlcsTest\Api\Entity\Abstracts\EntityTester;
use Dvsa\Olcs\Api\Entity\ContactDetails\ContactDetails as Entity;

/**
 * ContactDetails Entity Unit Tests
 *
 * Initially auto-generated but won't be overridden
 */
class ContactDetailsEntityTest extends EntityTester
{
    /**
     * Define the entity to test
     *
     * @var string
     */
    protected $entityClass = Entity::class;

    public function testConstruct()
    {
        $contactType = m::mock(RefData::class);

        $entity = new ContactDetails($contactType);

        $this->assertSame($contactType, $entity->getContactType());
    }
}
