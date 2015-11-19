<?php

namespace Dvsa\OlcsTest\Api\Entity\Application;

use Dvsa\OlcsTest\Api\Entity\Abstracts\EntityTester;
use Dvsa\Olcs\Api\Entity\Application\PreviousConviction as Entity;
use Mockery as m;

/**
 * PreviousConviction Entity Unit Tests
 *
 * Initially auto-generated but won't be overridden
 */
class PreviousConvictionEntityTest extends EntityTester
{
    /**
     * Define the entity to test
     *
     * @var string
     */
    protected $entityClass = Entity::class;

    public function testGetRelatedOrganisationWithApplication()
    {
        $sut = new Entity();

        $this->assertSame(null, $sut->getRelatedOrganisation());
    }

    public function testGetRelatedOrganisation()
    {
        $sut = new Entity();

        $mockApplication = m::mock();
        $mockApplication->shouldReceive('getLicence->getOrganisation')->with()->once()->andReturn('ORG1');
        $sut->setApplication($mockApplication);

        $this->assertSame('ORG1', $sut->getRelatedOrganisation());
    }
}
