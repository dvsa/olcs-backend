<?php

namespace Dvsa\OlcsTest\Api\Entity\Doc;

use Dvsa\Olcs\Api\Entity\Application\Application;
use Dvsa\Olcs\Api\Entity\Bus\BusReg;
use Dvsa\Olcs\Api\Entity\Cases\Cases;
use Dvsa\Olcs\Api\Entity\Cases\Statement;
use Dvsa\Olcs\Api\Entity\Ebsr\EbsrSubmission;
use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Dvsa\Olcs\Api\Entity\OperatingCentre\OperatingCentre;
use Dvsa\Olcs\Api\Entity\Organisation\Organisation;
use Dvsa\Olcs\Api\Entity\Submission\Submission;
use Dvsa\Olcs\Api\Entity\Tm\TransportManager;
use Dvsa\OlcsTest\Api\Entity\Abstracts\EntityTester;
use Dvsa\Olcs\Api\Entity\Doc\Document as Entity;
use Mockery as m;

/**
 * Document Entity Unit Tests
 *
 * Initially auto-generated but won't be overridden
 */
class DocumentEntityTest extends EntityTester
{
    /**
     * Define the entity to test
     *
     * @var string
     */
    protected $entityClass = Entity::class;

    /**
     * tests the related organisation returns null when nothing is found
     */
    public function testGetRelatedOrganisationNotFound()
    {
        $entity = m::mock(Entity::class)->makePartial();
        $this->assertNull($entity->getRelatedOrganisation());
    }

    /**
     * tests the related organisation is retrieved properly
     *
     * @dataProvider relatedOrganisationProvider
     *
     * @param $setterMethod
     * @param $relationClass
     */
    public function testGetRelatedOrganisation($setterMethod, $relationClass)
    {
        $organisation = m::mock(Organisation::class);
        $relation = m::mock($relationClass);
        $relation->shouldReceive('getRelatedOrganisation')->once()->andReturn($organisation);

        $entity = m::mock(Entity::class)->makePartial();
        $entity->$setterMethod($relation);

        $this->assertEquals($organisation, $entity->getRelatedOrganisation());
    }

    /**
     * Provider for testGetRelatedOrganisation
     *
     * @return array
     */
    public function relatedOrganisationProvider()
    {
        return [
            ['setLicence', Licence::class],
            ['setApplication', Application::class],
            ['setTransportManager', TransportManager::class],
            ['setCase', Cases::class],
            ['setOperatingCentre', OperatingCentre::class],
            ['setBusReg', BusReg::class],
            ['setIrfoOrganisation', Organisation::class],
            ['setSubmission', Submission::class],
            ['setStatement', Statement::class],
            ['setEbsrSubmission', EbsrSubmission::class]
        ];
    }
}
