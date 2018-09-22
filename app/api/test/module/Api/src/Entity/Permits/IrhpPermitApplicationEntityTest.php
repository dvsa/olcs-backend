<?php

namespace Dvsa\OlcsTest\Api\Entity\Permits;

use Dvsa\OlcsTest\Api\Entity\Abstracts\EntityTester;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitApplication as Entity;
use Mockery as m;

/**
 * IrhpPermitApplication Entity Unit Tests
 *
 * Initially auto-generated but won't be overridden
 */
class IrhpPermitApplicationEntityTest extends EntityTester
{
    /**
     * Define the entity to test
     *
     * @var string
     */
    protected $entityClass = Entity::class;

    /**
     * @var Entity
     */
    protected $sut;

    public function setUp()
    {
        $this->sut = new Entity(m::mock(\Dvsa\Olcs\Api\Entity\Permits\IrhpPermitApplication::class));

        parent::setUp();
    }

    public function testGetCalculatedBundleValues()
    {
        $this->assertSame(
            [
                'permitsAwarded' => 0
            ],
            $this->sut->getCalculatedBundleValues()
        );
    }
}
