<?php

namespace Dvsa\OlcsTest\Api\Entity\Permits;

use Dvsa\OlcsTest\Api\Entity\Abstracts\EntityTester;
use Dvsa\Olcs\Api\Entity\Permits\IrhpApplication as Entity;
use Dvsa\Olcs\Api\Enntity\Licence\Licence;
use Dvsa\Olcs\Api\Entity\Organisation\Organisation;
use Mockery as m;

/**
 * IrhpApplication Entity Unit Tests
 *
 * Initially auto-generated but won't be overridden
 */
class IrhpApplicationEntityTest extends EntityTester
{
    /**
     * Define the entity to test
     *
     * @var string
     */
    protected $entityClass = Entity::class;

    public function testGetRelatedOrganisation()
    {
        $organisation = m::mock(Organisation::class);

        $licence = m::mock(Licence::class);
        $licence->shouldReceive('getOrganisation')
            ->andReturn($organisation);

        $irhpApplication = new Entity();
        $irhpApplication->setLicence($licence);

        $this->assertSame(
            $organisation,
            $irhpApplication->getRelatedOrganisation()
        );
    }
}
