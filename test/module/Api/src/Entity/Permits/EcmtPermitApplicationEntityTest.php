<?php

namespace Dvsa\OlcsTest\Api\Entity\Permits;

use Dvsa\OlcsTest\Api\Entity\Abstracts\EntityTester;
use Dvsa\Olcs\Api\Entity\Permits\EcmtPermitApplication as Entity;
use Dvsa\Olcs\Api\Entity\ContactDetails\Country;
use Dvsa\Olcs\Api\Entity\System\RefData;
use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Mockery as m;

/**
 * EcmtPermitApplication Entity Unit Tests
 *
 * Initially auto-generated but won't be overridden
 */
class EcmtPermitApplicationEntityTest extends EntityTester
{
    /**
     * Define the entity to test
     *
     * @var string
     */
    protected $entityClass = Entity::class;

    /**
     * @dataProvider trueFalseProvider
     */
    public function testUpdateCabotage($cabotage)
    {
        $entity = $this->createApplicationWithCompletedDeclaration();

        $entity->updateCabotage($cabotage);

        $this->assertEquals($cabotage, $entity->getCabotage());
        $this->assertFalse($entity->getCheckedAnswers());
        $this->assertFalse($entity->getDeclaration());
    }

    /**
     * @dataProvider trueFalseProvider
     */
    public function testUpdateEmissions($emissions)
    {
        $entity = $this->createApplicationWithCompletedDeclaration();

        $entity->updateEmissions($emissions);

        $this->assertFalse($entity->getCheckedAnswers());
        $this->assertFalse($entity->getDeclaration());
    }

    public function testUpdateCountrys()
    {
        $entity = $this->createApplicationWithCompletedDeclaration();

        $countrys = array(
            m::mock(Country::class),
            m::mock(Country::class),
            m::mock(Country::class)
        );

        $entity->updateCountrys($countrys);

        $this->assertSame($countrys, $entity->getCountrys());
        $this->assertFalse($entity->getCheckedAnswers());
        $this->assertFalse($entity->getDeclaration());
    }

    public function testUpdatePermitsRequired()
    {
        $permitsRequired = 7;

        $entity = $this->createApplicationWithCompletedDeclaration();

        $entity->updatePermitsRequired($permitsRequired);

        $this->assertEquals($permitsRequired, $entity->getPermitsRequired());
        $this->assertFalse($entity->getCheckedAnswers());
        $this->assertFalse($entity->getDeclaration());
    }

    public function testUpdateTrips()
    {
        $trips = 5;

        $entity = $this->createApplicationWithCompletedDeclaration();

        $entity->updateTrips($trips);

        $this->assertEquals($trips, $entity->getTrips());
        $this->assertFalse($entity->getCheckedAnswers());
        $this->assertFalse($entity->getDeclaration());
    }

    public function testUpdateInternationalJourneys()
    {
        $internationalJourneys = 2;

        $entity = $this->createApplicationWithCompletedDeclaration();

        $entity->updateInternationalJourneys($internationalJourneys);

        $this->assertEquals($internationalJourneys, $entity->getInternationalJourneys());
        $this->assertFalse($entity->getCheckedAnswers());
        $this->assertFalse($entity->getDeclaration());
    }

    public function testUpdateSectors()
    {
        $sectors = m::mock(RefData::class);

        $entity = $this->createApplicationWithCompletedDeclaration();

        $entity->updateSectors($sectors);

        $this->assertSame($sectors, $entity->getSectors());
        $this->assertFalse($entity->getCheckedAnswers());
        $this->assertFalse($entity->getDeclaration());
    }

    private function createApplicationWithCompletedDeclaration()
    {
        $entity = Entity::createNew(
            m::mock(RefData::class),
            m::mock(RefData::class),
            m::mock(RefData::class),
            m::mock(Licence::class)
        );

        $entity->setCheckedAnswers(true);
        $entity->setDeclaration(true);

        return $entity;
    }

    public function trueFalseProvider()
    {
        return [
            [true],
            [false]
        ];
    }
}
