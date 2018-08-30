<?php

namespace Dvsa\OlcsTest\Api\Entity\Permits;

use Dvsa\Olcs\Api\Entity\Permits\Sectors;
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

    public function testUpdate()
    {
        $application = $this->createApplication();
        $permitType = Entity::PERMIT_TYPE;
        $permitTypeRefData = new RefData($permitType);
        $licence = m::mock(Licence::class)->makePartial(); //make partial allows to differ from what's there already
        $sectors = m::mock(Sectors::class);
        $countries = ['countries'];
        $cabotage = 1;
        $declaration = 0;
        $emissions = 1;
        $permitsRequired = 999;
        $trips = 666;
        $internationalJourneys = Entity::INTER_JOURNEY_60_90;
        $internationalJourneyRefData = new RefData($internationalJourneys);
        $dateReceived = '2017-12-25';

        $application->update(
            $permitTypeRefData,
            $licence,
            $sectors,
            $countries,
            $cabotage,
            $declaration,
            $emissions,
            $permitsRequired,
            $trips,
            $internationalJourneyRefData,
            $dateReceived
        );

        $this->assertEquals($permitType, $application->getPermitType()->getId());
        $this->assertEquals($licence, $application->getLicence());
        $this->assertEquals($sectors, $application->getSectors());
        $this->assertEquals($countries, $application->getCountrys());
        $this->assertEquals($cabotage, $application->getCabotage());
        $this->assertEquals($declaration, $application->getCheckedAnswers()); //auto updated on internal updates
        $this->assertEquals($declaration, $application->getDeclaration());
        $this->assertEquals($emissions, $application->getEmissions());
        $this->assertEquals($permitsRequired, $application->getPermitsRequired());
        $this->assertEquals($trips, $application->getTrips());
        $this->assertEquals($internationalJourneys, $application->getInternationalJourneys()->getId());
        $this->assertEquals($dateReceived, $application->getDateReceived()->format('Y-m-d'));
    }

    /**
     * Tests withdrawing an application
     */
    public function testWithdraw()
    {
        $entity = $this->createApplicationUnderConsideration();
        $entity->withdraw(new RefData(Entity::STATUS_WITHDRAWN));
        $this->assertEquals(Entity::STATUS_WITHDRAWN, $entity->getStatus()->getId());
    }

    /**
     * @dataProvider dpWithdrawException
     * @expectedException \Dvsa\Olcs\Api\Domain\Exception\ForbiddenException
     */
    public function testWithdrawException($status)
    {
        $entity = $this->createApplication($status);
        $entity->withdraw(new RefData(Entity::STATUS_WITHDRAWN));
    }

    /**
     * Tests cancelling an application
     */
    public function testCancel()
    {
        $entity = $this->createApplication();
        $entity->cancel(new RefData(Entity::STATUS_CANCELLED));
        $this->assertEquals(Entity::STATUS_CANCELLED, $entity->getStatus()->getId());
    }

    /**
     * @dataProvider dpCancelException
     * @expectedException \Dvsa\Olcs\Api\Domain\Exception\ForbiddenException
     */
    public function testCancelException($status)
    {
        $entity = $this->createApplication($status);
        $entity->cancel(new RefData(Entity::STATUS_CANCELLED));
    }

    /**
     * Pass array of app statuses to make sure an exception is thrown
     *
     * @return array
     */
    public function dpWithdrawException()
    {
        return [
            [Entity::STATUS_CANCELLED],
            [Entity::STATUS_NOT_YET_SUBMITTED],
            [Entity::STATUS_AWAITING_FEE],
            [Entity::STATUS_WITHDRAWN],
            [Entity::STATUS_UNSUCCESSFUL],
            [Entity::STATUS_ISSUED],
        ];
    }

    /**
     * Pass array of app status to make sure an exception is thrown
     *
     * @return array
     */
    public function dpCancelException()
    {
        return [
            [Entity::STATUS_CANCELLED],
            [Entity::STATUS_AWAITING_FEE],
            [Entity::STATUS_WITHDRAWN],
            [Entity::STATUS_UNSUCCESSFUL],
            [Entity::STATUS_ISSUED],
            [Entity::STATUS_UNDER_CONSIDERATION]
        ];
    }

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
        $internationalJourneys = new RefData(Entity::INTER_JOURNEY_60_90);

        $entity = $this->createApplicationWithCompletedDeclaration();

        $entity->updateInternationalJourneys($internationalJourneys);

        $this->assertEquals(Entity::INTER_JOURNEY_60_90, $entity->getInternationalJourneys()->getId());
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

    private function createApplicationUnderConsideration()
    {
        return $this->createApplication(Entity::STATUS_UNDER_CONSIDERATION);
    }

    private function createApplication($status = Entity::STATUS_NOT_YET_SUBMITTED)
    {
        $entity = Entity::createNew(
            new RefData($status),
            m::mock(RefData::class),
            m::mock(RefData::class),
            m::mock(Licence::class)
        );

        return $entity;
    }

    private function createApplicationWithCompletedDeclaration()
    {
        $entity = $this->createApplication();

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
