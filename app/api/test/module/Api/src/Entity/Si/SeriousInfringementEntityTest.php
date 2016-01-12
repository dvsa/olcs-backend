<?php

namespace Dvsa\OlcsTest\Api\Entity\Si;

use Dvsa\Olcs\Api\Entity\Si\SeriousInfringement;
use Dvsa\OlcsTest\Api\Entity\Abstracts\EntityTester;
use Dvsa\Olcs\Api\Entity\Si\SeriousInfringement as Entity;
use Dvsa\Olcs\Api\Entity\User\User as UserEntity;
use Mockery as m;

/**
 * SeriousInfringement Entity Unit Tests
 *
 * Initially auto-generated but won't be overridden
 */
class SeriousInfringementEntityTest extends EntityTester
{
    /**
     * Define the entity to test
     *
     * @var string
     */
    protected $entityClass = Entity::class;

    /**
     * tests updateErruResponse
     */
    public function testUpdateErruResponse()
    {
        $user = m::mock(UserEntity::class);
        $date = new \DateTime();

        $entity = new SeriousInfringement();

        $entity->updateErruResponse($user, $date);

        $this->assertEquals($user, $entity->getErruResponseUser());
        $this->assertEquals($date, $entity->getErruResponseTime());
        $this->assertEquals('Y', $entity->getErruResponseSent());
    }
}
