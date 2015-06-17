<?php

namespace Dvsa\OlcsTest\Api\Entity\Fee;

use Dvsa\OlcsTest\Api\Entity\Abstracts\EntityTester;
use Dvsa\Olcs\Api\Entity\Fee\FeePayment as Entity;
use Mockery as m;

/**
 * FeePayment Entity Unit Tests
 *
 * Initially auto-generated but won't be overridden
 */
class FeePaymentEntityTest extends EntityTester
{
    /**
     * Define the entity to test
     *
     * @var string
     */
    protected $entityClass = Entity::class;

    public function testGetCalculatedValues()
    {
        $sut = $sut = m::mock(Entity::class)->makePartial()->shouldAllowMockingProtectedMethods();

        $values = $sut->getCalculatedValues();

        $this->assertInternalType('array', $values);
    }
}
