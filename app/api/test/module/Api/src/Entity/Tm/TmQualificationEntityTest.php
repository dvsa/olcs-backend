<?php

namespace Dvsa\OlcsTest\Api\Entity\Tm;

use Dvsa\OlcsTest\Api\Entity\Abstracts\EntityTester;
use Dvsa\Olcs\Api\Entity\Tm\TmQualification as Entity;

/**
 * TmQualification Entity Unit Tests
 *
 * Initially auto-generated but won't be overridden
 */
class TmQualificationEntityTest extends EntityTester
{
    /**
     * Define the entity to test
     *
     * @var string
     */
    protected $entityClass = Entity::class;

    public function testUpdateTmQualification()
    {
        $entity = new Entity();

        $entity->updateTmQualification(
            'qtype',
            '123',
            '2015-01-01',
            'GB',
            1
        );

        $this->assertEquals('qtype', $entity->getQualificationType());
        $this->assertEquals('123', $entity->getSerialNo());
        $this->assertEquals(new \DateTime('2015-01-01'), $entity->getIssuedDate());
        $this->assertEquals('GB', $entity->getCountryCode());
        $this->assertEquals(1, $entity->getTransportManager());
    }

    public function testUpdateTmQualificationWithException()
    {
        $this->expectException(\Dvsa\Olcs\Api\Domain\Exception\ValidationException::class);

        $entity = new Entity();

        $entity->updateTmQualification(
            'qtype',
            '123',
            '2222-01-01',
            'GB'
        );
    }
}
