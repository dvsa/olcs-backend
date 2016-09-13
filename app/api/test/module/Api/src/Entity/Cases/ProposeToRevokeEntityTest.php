<?php

namespace Dvsa\OlcsTest\Api\Entity\Cases;

use Dvsa\OlcsTest\Api\Entity\Abstracts\EntityTester;
use Dvsa\Olcs\Api\Entity\Cases\ProposeToRevoke as Entity;
use Dvsa\Olcs\Api\Entity\Pi\PresidingTc as PresidingTcEntity;
use Dvsa\Olcs\Api\Entity\Cases\Cases as CasesEntity;
use Mockery as m;

/**
 * ProposeToRevoke Entity Unit Tests
 *
 * Initially auto-generated but won't be overridden
 */
class ProposeToRevokeEntityTest extends EntityTester
{
    /**
     * Define the entity to test
     *
     * @var string
     */
    protected $entityClass = Entity::class;

    /**
     * Test constructor
     */
    public function testConstructor()
    {
        $mockCase = m::mock(CasesEntity::class);
        $reasons = ['foo'];
        $mockPresidingTc = m::mock(PresidingTcEntity::class);
        $ptrAgreedDate = new \DateTime();

        $entity = new Entity($mockCase, $reasons, $mockPresidingTc, $ptrAgreedDate);

        $this->assertEquals($mockCase, $entity->getCase());
        $this->assertEquals($reasons, $entity->getReasons());
        $this->assertInstanceOf(\DateTime::class, $entity->getPtrAgreedDate());
    }

    /**
     * Test update method
     */
    public function testUpdate()
    {
        $mockCase = m::mock(CasesEntity::class);
        $reasons = ['foo'];
        $mockPresidingTc = m::mock(PresidingTcEntity::class);
        $ptrAgreedDate = new \DateTime();

        $entity = new Entity($mockCase, $reasons, $mockPresidingTc, $ptrAgreedDate);

        $newReasons = ['bar'];
        $newMockPresidingTc = m::mock(PresidingTcEntity::class);
        $newPtrAgreedDate = new \DateTime();

        $entity->update($newReasons, $newMockPresidingTc, $newPtrAgreedDate);

        $this->assertEquals($mockCase, $entity->getCase());
        $this->assertEquals($newReasons, $entity->getReasons());
        $this->assertEquals($newMockPresidingTc, $entity->getPresidingTc());
        $this->assertEquals($newPtrAgreedDate, $entity->getPtrAgreedDate());
    }
}
