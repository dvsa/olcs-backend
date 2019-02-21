<?php

namespace Dvsa\OlcsTest\Api\Entity\Permits;

use Dvsa\Olcs\Api\Domain\Exception\RuntimeException;
use Dvsa\Olcs\Api\Entity\Permits\EcmtPermitApplication as Entity;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitApplication;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitWindow;
use Dvsa\Olcs\Api\Entity\Permits\Sectors;
use Dvsa\Olcs\Api\Entity\ContactDetails\Country;
use Dvsa\Olcs\Api\Entity\System\RefData;
use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Dvsa\OlcsTest\Api\Entity\Abstracts\EntityTester;
use Mockery as m;
use Doctrine\Common\Collections\ArrayCollection;

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

    public function testCreateNew()
    {
        $sourceRefData = m::mock(RefData::class);
        $statusRefData = m::mock(RefData::class);
        $permitTypeRefData = m::mock(RefData::class);
        $licence = m::mock(Licence::class);
        $dateReceived = '2017-12-25';

        $application = Entity::createNew(
            $sourceRefData,
            $statusRefData,
            $permitTypeRefData,
            $licence,
            $dateReceived
        );

        $this->assertSame($sourceRefData, $application->getSource());
        $this->assertSame($statusRefData, $application->getStatus());
        $this->assertSame($permitTypeRefData, $application->getPermitType());
        $this->assertSame($licence, $application->getLicence());

        $actualDate = $application->getDateReceived()->format('Y-m-d');
        $this->assertEquals($dateReceived, $actualDate);
    }

    /**
     * @dataProvider dpProvideUpdateCountrys
     */
    public function testCreateNewInternal($countrys, $expectedHasRestrictedCountries)
    {
        $sourceRefData = m::mock(RefData::class);
        $statusRefData = m::mock(RefData::class);
        $permitTypeRefData = m::mock(RefData::class);
        $licence = m::mock(Licence::class);
        $dateReceived = '2017-12-25';
        $sectors = m::mock(Sectors::class);
        $cabotage = 1;
        $declaration = 1;
        $emissions = 1;
        $permitsRequired = 999;
        $trips = 666;
        $internationalJourneysRefData = m::mock(RefData::class);

        $application = Entity::createNewInternal(
            $sourceRefData,
            $statusRefData,
            $permitTypeRefData,
            $licence,
            $dateReceived,
            $sectors,
            $countrys,
            $cabotage,
            $declaration,
            $emissions,
            $permitsRequired,
            $trips,
            $internationalJourneysRefData
        );

        $ecmtPermitAppEntity = m::mock(Entity::class)->shouldAllowMockingProtectedMethods();
        $ecmtPermitAppEntity->shouldReceive('getSectionCompletion')
            ->with(Entity::SECTIONS)
            ->andReturn(['allCompleted' => true]);

        $this->assertSame($sourceRefData, $application->getSource());
        $this->assertSame($statusRefData, $application->getStatus());
        $this->assertSame($permitTypeRefData, $application->getPermitType());
        $this->assertSame($licence, $application->getLicence());
        $this->assertEquals($dateReceived, $application->getDateReceived()->format('Y-m-d'));
        $this->assertEquals($sectors, $application->getSectors());
        $this->assertEquals($countrys, $application->getCountrys());
        $this->assertEquals($expectedHasRestrictedCountries, $application->getHasRestrictedCountries());
        $this->assertEquals($cabotage, $application->getCabotage());
        $this->assertEquals($declaration, $application->getCheckedAnswers()); //auto updated on internal updates
        $this->assertEquals($declaration, $application->getDeclaration());
        $this->assertEquals($emissions, $application->getEmissions());
        $this->assertEquals($permitsRequired, $application->getPermitsRequired());
        $this->assertEquals($trips, $application->getTrips());
        $this->assertSame($internationalJourneysRefData, $application->getInternationalJourneys());
    }

    /**
    * @dataProvider dpProvideUpdateCountrys
    */
    public function testUpdate($countrys, $expectedHasRestrictedCountries)
    {
        $application = $this->createApplication();
        $permitType = Entity::PERMIT_TYPE;
        $permitTypeRefData = new RefData($permitType);
        $licence = m::mock(Licence::class)->makePartial(); //make partial allows to differ from what's there already
        $sectors = m::mock(Sectors::class);
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
            $countrys,
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
        $this->assertEquals($countrys, $application->getCountrys());
        $this->assertEquals($expectedHasRestrictedCountries, $application->getHasRestrictedCountries());
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
        $entity->withdraw(new RefData(Entity::STATUS_WITHDRAWN), new RefData(Entity::WITHDRAWN_REASON_BY_USER));
        $this->assertEquals(Entity::STATUS_WITHDRAWN, $entity->getStatus()->getId());
        $this->assertEquals(Entity::WITHDRAWN_REASON_BY_USER, $entity->getWithdrawReason()->getId());
    }

    /**
    * @dataProvider dpWithdrawException
    * @expectedException \Dvsa\Olcs\Api\Domain\Exception\ForbiddenException
    */
    public function testWithdrawException($status)
    {
        $entity = $this->createApplication($status);
        $entity->withdraw(new RefData(Entity::STATUS_WITHDRAWN), new RefData(Entity::WITHDRAWN_REASON_BY_USER));
    }

    /**
     * Tests declining an application
     */
    public function testDecline()
    {
        $entity = $this->createApplicationAwaitingFee();
        $entity->decline(new RefData(Entity::STATUS_WITHDRAWN), new RefData(Entity::WITHDRAWN_REASON_DECLINED));
        $this->assertEquals(Entity::STATUS_WITHDRAWN, $entity->getStatus()->getId());
        $this->assertEquals(Entity::WITHDRAWN_REASON_DECLINED, $entity->getWithdrawReason()->getId());
    }

    /**
     * @dataProvider dpDeclineAcceptException
     * @expectedException \Dvsa\Olcs\Api\Domain\Exception\ForbiddenException
     */
    public function testDeclineException($status)
    {
        $entity = $this->createApplication($status);
        $entity->decline(new RefData(Entity::STATUS_WITHDRAWN), new RefData(Entity::WITHDRAWN_REASON_BY_USER));
    }

    /**
     * Tests accepting an application
     */
    public function testAccept()
    {
        $entity = $this->createApplicationAwaitingFee();
        $entity->completeIssueFee(new RefData(Entity::STATUS_FEE_PAID));
        $entity->accept(new RefData(Entity::STATUS_VALID));
        $this->assertEquals(Entity::STATUS_VALID, $entity->getStatus()->getId());
    }

    /**
     * @dataProvider dpDeclineAcceptException
     * @expectedException \Dvsa\Olcs\Api\Domain\Exception\ForbiddenException
     */
    public function testAcceptException($status)
    {
        $entity = $this->createApplication($status);
        $entity->accept(new RefData(Entity::STATUS_VALID));
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
     * Pass array of app statuses to make sure an exception is thrown
     *
     * @return array
     */
    public function dpDeclineAcceptException()
    {
        return [
            [Entity::STATUS_CANCELLED],
            [Entity::STATUS_NOT_YET_SUBMITTED],
            [Entity::STATUS_UNDER_CONSIDERATION],
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

    /**
    * @dataProvider dpProvideUpdateCountrys
    */
    public function testUpdateCountrys($countrys, $expectedHasRestrictedCountries)
    {
        $entity = $this->createApplicationWithCompletedDeclaration();

        $entity->updateCountrys($countrys);

        $this->assertSame($countrys, $entity->getCountrys());
        $this->assertEquals($expectedHasRestrictedCountries, $entity->getHasRestrictedCountries());
        $this->assertFalse($entity->getCheckedAnswers());
        $this->assertFalse($entity->getDeclaration());
    }

    public function dpProvideUpdateCountrys()
    {
        $countrys = new ArrayCollection([
            m::mock(Country::class),
            m::mock(Country::class),
            m::mock(Country::class)
        ]);

        return [
            [$countrys, true],
            [new ArrayCollection([]), false]
        ];
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

    public function testCalculatePermitIntensityOfUse()
    {
        $entity = $this->createApplicationWithCompletedDeclaration();

        $entity->setTrips(10);
        $entity->setPermitsRequired(4);

        $intensity = $entity->getPermitIntensityOfUse();

        $this->assertEquals($intensity, 2.5);
    }

    public function testCalculatePermitApplicationScore()
    {
        $entity = $this->createApplicationWithCompletedDeclaration();
        $internationalJourneys = new RefData(Entity::INTER_JOURNEY_60_90);

        $entity->setTrips(10);
        $entity->setPermitsRequired(4);

        $entity->updateInternationalJourneys($internationalJourneys);

        $this->assertEquals($entity->getPermitApplicationScore(), 1.875);
    }

    private function createApplicationUnderConsideration()
    {
        return $this->createApplication(Entity::STATUS_UNDER_CONSIDERATION);
    }

    private function createApplicationAwaitingFee()
    {
        return $this->createApplication(Entity::STATUS_AWAITING_FEE);
    }

    private function createApplication($status = Entity::STATUS_NOT_YET_SUBMITTED)
    {
        $entity = Entity::createNew(
            m::mock(RefData::class),
            new RefData($status),
            m::mock(RefData::class),
            m::mock(Licence::class)
        );

        return $entity;
    }

    private function createApplicationWithCompletedDeclaration($status = Entity::STATUS_NOT_YET_SUBMITTED)
    {
        $entity = $this->createApplication($status);

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

    private function createValidApplication()
    {
        return $this->createApplication(Entity::STATUS_VALID);
    }

    public function testIsValid()
    {
        $entity = $this->createValidApplication();
        $this->assertTrue($entity->isValid());
    }

    /**
     * Test to ensure the permit is reset when the licence is changed.
     * @dataProvider dpProvideUpdateCountrys
     */
    public function testUpdateLicence($countrys)
    {
        $sourceRefData = m::mock(RefData::class);
        $statusRefData = m::mock(RefData::class);
        $permitTypeRefData = m::mock(RefData::class);
        $licence = m::mock(Licence::class);
        $sectors = m::mock(Sectors::class);
        $cabotage = 1;
        $declaration = 1;
        $checkedAnswers = 1;
        $emissions = 1;
        $permitsRequired = 999;
        $trips = 666;
        $internationalJourneys = Entity::INTER_JOURNEY_60_90;
        $internationalJourneyRefData = new RefData($internationalJourneys);
        $dateReceived = '2017-12-25';

        $application = Entity::createNewInternal(
            $sourceRefData,
            $statusRefData,
            $permitTypeRefData,
            $licence,
            $dateReceived,
            $sectors,
            $countrys,
            $cabotage,
            $declaration,
            $emissions,
            $permitsRequired,
            $trips,
            $internationalJourneyRefData
        );

        $application->setCheckedAnswers($checkedAnswers);

        $newLicence = m::mock(Licence::class);

        $application->updateLicence($newLicence);

        $this->assertSame($application->getLicence(), $newLicence);
        $this->assertEquals($application->getCabotage(), null);
        $this->assertEquals($application->getEmissions(), null);
        $this->assertEquals($application->getTrips(), null);
        $this->assertEquals($application->getInternationalJourneys(), null);
        $this->assertEquals($application->getSectors(), null);
        $this->assertEquals($application->getCountrys(), new ArrayCollection());
        $this->assertEquals($application->getHasRestrictedCountries(), null);
        $this->assertEquals($application->getCheckedAnswers(), null);
        $this->assertEquals($application->getDeclaration(), null);
    }

    public function testProceedToIssuing()
    {
        $refData = m::mock(RefData::class);
        $entity = $this->createApplication(Entity::STATUS_FEE_PAID);
        $entity->proceedToIssuing($refData);
        $this->assertSame($refData, $entity->getStatus());
    }

    /**
     * @dataProvider dpIsReadyForIssuingFail
     * @expectedException \Dvsa\Olcs\Api\Domain\Exception\ForbiddenException
     */
    public function testProceedToIssuingException($status)
    {
        $entity = $this->createApplication($status);
        $entity->proceedToIssuing(m::mock(RefData::class));
    }

    public function testProceedToValid()
    {
        $refData = m::mock(RefData::class);
        $entity = $this->createApplication(Entity::STATUS_ISSUING);
        $entity->proceedToValid($refData);
        $this->assertSame($refData, $entity->getStatus());
    }

    /**
     * @dataProvider dpIsIssueInProgressFail
     * @expectedException \Dvsa\Olcs\Api\Domain\Exception\ForbiddenException
     */
    public function testProceedToValidException($status)
    {
        $entity = $this->createApplication($status);
        $entity->proceedToValid(m::mock(RefData::class));
    }

    public function testIsReadyForIssuingSuccess()
    {
        $entity = $this->createApplication(Entity::STATUS_FEE_PAID);
        $this->assertTrue($entity->isReadyForIssuing());
    }

    /**
     * @dataProvider dpIsReadyForIssuingFail
     */
    public function testIsReadyForIssuingFail($status)
    {
        $entity = $this->createApplication($status);
        $this->assertFalse($entity->isReadyForIssuing());
    }

    /**
     * Array of app statuses that don't match ready for issuing
     *
     * @return array
     */
    public function dpIsReadyForIssuingFail()
    {
        return [
            [Entity::STATUS_CANCELLED],
            [Entity::STATUS_NOT_YET_SUBMITTED],
            [Entity::STATUS_UNDER_CONSIDERATION],
            [Entity::STATUS_WITHDRAWN],
            [Entity::STATUS_AWAITING_FEE],
            [Entity::STATUS_UNSUCCESSFUL],
            [Entity::STATUS_ISSUED],
            [Entity::STATUS_ISSUING],
            [Entity::STATUS_VALID],
            [Entity::STATUS_DECLINED],
        ];
    }

    public function testIsIssueInProgressSuccess()
    {
        $entity = $this->createApplication(Entity::STATUS_ISSUING);
        $this->assertTrue($entity->isIssueInProgress());
    }

    /**
     * @dataProvider dpIsIssueInProgressFail
     */
    public function testIsIssueInProgressFail($status)
    {
        $entity = $this->createApplication($status);
        $this->assertFalse($entity->isIssueInProgress());
    }

    /**
     * Array of app statuses that don't match an issue in progress
     *
     * @return array
     */
    public function dpIsIssueInProgressFail()
    {
        return [
            [Entity::STATUS_CANCELLED],
            [Entity::STATUS_NOT_YET_SUBMITTED],
            [Entity::STATUS_UNDER_CONSIDERATION],
            [Entity::STATUS_WITHDRAWN],
            [Entity::STATUS_AWAITING_FEE],
            [Entity::STATUS_FEE_PAID],
            [Entity::STATUS_UNSUCCESSFUL],
            [Entity::STATUS_ISSUED],
            [Entity::STATUS_VALID],
            [Entity::STATUS_DECLINED],
        ];
    }

    /**
     * @dataProvider dpIsFeePaid
     */
    public function testIsFeePaid($status, $expected)
    {
        $entity = $this->createApplication($status);
        $this->assertSame($expected, $entity->isFeePaid());
    }

    /**
     * @return array
     */
    public function dpIsFeePaid()
    {
        return [
            [Entity::STATUS_CANCELLED, false],
            [Entity::STATUS_NOT_YET_SUBMITTED, false],
            [Entity::STATUS_UNDER_CONSIDERATION, false],
            [Entity::STATUS_WITHDRAWN, false],
            [Entity::STATUS_AWAITING_FEE, false],
            [Entity::STATUS_FEE_PAID, true],
            [Entity::STATUS_UNSUCCESSFUL, false],
            [Entity::STATUS_ISSUED, false],
            [Entity::STATUS_ISSUING, false],
            [Entity::STATUS_VALID, false],
            [Entity::STATUS_DECLINED, false],
        ];
    }

    public function testProceedToAwaitingFee()
    {
        $refData = m::mock(RefData::class);
        $entity = $this->createApplication(Entity::STATUS_UNDER_CONSIDERATION);
        $entity->proceedToAwaitingFee($refData);
        $this->assertSame($refData, $entity->getStatus());
    }

    /**
     * @dataProvider dpIsApplicationUnderConsiderationFail
     * @expectedException \Dvsa\Olcs\Api\Domain\Exception\ForbiddenException
     */
    public function testProceedToAwaitingFeeException($status)
    {
        $entity = $this->createApplication($status);
        $entity->proceedToAwaitingFee(m::mock(RefData::class));
    }

    public function testProceedToUnsuccessful()
    {
        $refData = m::mock(RefData::class);
        $entity = $this->createApplication(Entity::STATUS_UNDER_CONSIDERATION);
        $entity->proceedToUnsuccessful($refData);
        $this->assertSame($refData, $entity->getStatus());
    }

    /**
     * @dataProvider dpIsApplicationUnderConsiderationFail
     * @expectedException \Dvsa\Olcs\Api\Domain\Exception\ForbiddenException
     */
    public function testProceedToUnsuccessfulException($status)
    {
        $entity = $this->createApplication($status);
        $entity->proceedToUnsuccessful(m::mock(RefData::class));
    }

    /**
     * Array of app statuses that don't match an application under consideration
     *
     * @return array
     */
    public function dpIsApplicationUnderConsiderationFail()
    {
        return [
            [Entity::STATUS_CANCELLED],
            [Entity::STATUS_NOT_YET_SUBMITTED],
            [Entity::STATUS_WITHDRAWN],
            [Entity::STATUS_AWAITING_FEE],
            [Entity::STATUS_FEE_PAID],
            [Entity::STATUS_UNSUCCESSFUL],
            [Entity::STATUS_ISSUED],
            [Entity::STATUS_ISSUING],
            [Entity::STATUS_VALID],
            [Entity::STATUS_DECLINED],
        ];
    }

    public function testGetFirstIrhpPermitApplicationWithoutItem()
    {
        $appId = 100;

        $entity = $this->createApplication();
        $entity->setId($appId);

        // exception expected
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage(
            sprintf('This ECMT Application has none or more than one IRHP Permit Application (id: %d)', $appId)
        );
        $entity->getFirstIrhpPermitApplication();
    }

    public function testGetFirstIrhpPermitApplicationWithOneItem()
    {
        $entity = $this->createApplication();

        // add an IRHP application
        $irhpPermitApplication = m::mock(IrhpPermitApplication::class);
        $entity->addIrhpPermitApplications($irhpPermitApplication);

        // check the method returns valid result
        $this->assertSame($irhpPermitApplication, $entity->getFirstIrhpPermitApplication());
    }

    public function testGetFirstIrhpPermitApplicationWithMoreThanOneItem()
    {
        $appId = 100;

        $entity = $this->createApplication();
        $entity->setId($appId);

        // add an IRHP application
        $entity->addIrhpPermitApplications(m::mock(IrhpPermitApplication::class));

        // add another IRHP application
        $entity->addIrhpPermitApplications(m::mock(IrhpPermitApplication::class));

        // exception expected
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage(
            sprintf('This ECMT Application has none or more than one IRHP Permit Application (id: %d)', $appId)
        );
        $entity->getFirstIrhpPermitApplication();
    }

    public function testGetPermitsAwarded()
    {
        $irhpPermitApplication = m::mock(IrhpPermitApplication::class);
        $irhpPermitApplication->shouldReceive('countPermitsAwarded')
            ->andReturn(5);

        $entity = $this->createApplicationUnderConsideration();
        $entity->addIrhpPermitApplications($irhpPermitApplication);

        $this->assertEquals(5, $entity->getPermitsAwarded());
    }

    /**
    * @expectedException \Dvsa\Olcs\Api\Domain\Exception\ForbiddenException
    */
    public function testGetPermitsAwardedException()
    {
        $entity = $this->createApplicationAwaitingFee();

        $entity->getPermitsAwarded();
    }

    /**
     * @dataProvider dpProvideSuccessLevel
     */
    public function testGetSuccessLevel($permitsRequired, $permitsAwarded, $expectedSuccessLevel)
    {
        $irhpPermitApplication = m::mock(IrhpPermitApplication::class);
        $irhpPermitApplication->shouldReceive('countPermitsAwarded')
            ->andReturn($permitsAwarded);

        $entity = $this->createApplicationUnderConsideration();
        $entity->setPermitsRequired($permitsRequired);
        $entity->addIrhpPermitApplications($irhpPermitApplication);

        $this->assertEquals(
            $expectedSuccessLevel,
            $entity->getSuccessLevel()
        );
    }

    /**
     * Pass array of app statuses to make sure an exception is thrown
     *
     * @return array
     */
    public function dpProvideSuccessLevel()
    {
        return [
            [10, 1, Entity::SUCCESS_LEVEL_PARTIAL],
            [10, 9, Entity::SUCCESS_LEVEL_PARTIAL],
            [10, 0, Entity::SUCCESS_LEVEL_NONE],
            [1, 0, Entity::SUCCESS_LEVEL_NONE],
            [1, 1, Entity::SUCCESS_LEVEL_FULL],
            [10, 10, Entity::SUCCESS_LEVEL_FULL]
        ];
    }

    /**
    * @expectedException \Dvsa\Olcs\Api\Domain\Exception\ForbiddenException
    */
    public function testGetSuccessLevelException()
    {
        $entity = $this->createApplicationAwaitingFee();

        $entity->getSuccessLevel();
    }

    /**
     * @dataProvider dpProvideOutcomeNotificationType
     */
    public function testGetOutcomeNotificationType($source, $expectedNotificationType)
    {
        $sourceRefData = m::mock(RefData::class);
        $sourceRefData->shouldReceive('getId')
            ->andReturn($source);

        $entity = Entity::createNew(
            $sourceRefData,
            m::mock(RefData::class),
            m::mock(RefData::class),
            m::mock(Licence::class)
        );

        $this->assertEquals(
            $expectedNotificationType,
            $entity->getOutcomeNotificationType()
        );
    }

    /**
     * Pass array of app statuses to make sure an exception is thrown
     *
     * @return array
     */
    public function dpProvideOutcomeNotificationType()
    {
        return [
            [Entity::SOURCE_SELFSERVE, Entity::NOTIFICATION_TYPE_EMAIL],
            [Entity::SOURCE_INTERNAL, Entity::NOTIFICATION_TYPE_MANUAL]
        ];
    }

    /**
     * @dataProvider dpCantBeSubmittedByStatus
     *
     * check false is immediately returned for all statuses (the successful tests use a status of not yet submitted)
     */
    public function testCantBeSubmittedByStatus($status)
    {
        $application = $this->createApplication($status);
        self::assertEquals(false, $application->canBeSubmitted());
    }

    /**
     * App statuses where submission is not allowed
     *
     * @return array
     */
    public function dpCantBeSubmittedByStatus()
    {
        return [
            [Entity::STATUS_CANCELLED],
            [Entity::STATUS_UNDER_CONSIDERATION],
            [Entity::STATUS_WITHDRAWN],
            [Entity::STATUS_AWAITING_FEE ],
            [Entity::STATUS_FEE_PAID],
            [Entity::STATUS_UNSUCCESSFUL],
            [Entity::STATUS_ISSUED],
            [Entity::STATUS_ISSUING],
            [Entity::STATUS_VALID],
            [Entity::STATUS_DECLINED],
        ];
    }

    /**
     * @dataProvider dpLicenceCanMakeApplicationProvider
     *
     * This test puts the application into a state it can be submitted, depending on the state of the licence
     * If the licence is in the correct state then true will be returned
     */
    public function testCanBeSubmittedTrue($licenceCanMakeApplication)
    {
        $sourceRefData = m::mock(RefData::class);
        $statusRefData = new RefData(Entity::STATUS_NOT_YET_SUBMITTED);
        $permitTypeRefData = m::mock(RefData::class);
        $licence = m::mock(Licence::class);
        $dateReceived = '2017-12-25';
        $sectors = m::mock(Sectors::class);
        $cabotage = 1;
        $declaration = 1;
        $emissions = 1;
        $permitsRequired = 999;
        $trips = 666;
        $internationalJourneysRefData = m::mock(RefData::class);
        $countrys = new ArrayCollection([m::mock(Country::class)]);

        $application = Entity::createNewInternal(
            $sourceRefData,
            $statusRefData,
            $permitTypeRefData,
            $licence,
            $dateReceived,
            $sectors,
            $countrys,
            $cabotage,
            $declaration,
            $emissions,
            $permitsRequired,
            $trips,
            $internationalJourneysRefData
        );

        $licence->shouldReceive('canMakeEcmtApplication')
            ->once()
            ->with($application)
            ->andReturn($licenceCanMakeApplication);

        self::assertEquals($licenceCanMakeApplication, $application->canBeSubmitted());
    }

    public function dpLicenceCanMakeApplicationProvider()
    {
        return [
            [true],
            [false]
        ];
    }

    /**
     * @dataProvider dpReturnSnapshotData
     */
    public function testReturnSnapshotData(
        $cabotage,
        $cabotageResult,
        $emissions,
        $emissionsResult,
        $countries,
        $countriesResult,
        $countriesListResult,
        $emissionsQuestion,
        $emissionsCategory,
        $emissionsDeclaration
    ) {
        $licNo = 'OB1234567';
        $id = 1111;
        $applicationRef = $licNo . ' / ' . $id;
        $orgName = 'org name';
        $permitTypeDesc = 'permit type desc';
        $internationalJourneysDesc = 'international journey desc';
        $sectorName = 'sector name';

        $sectors = m::mock(Sectors::class);
        $sectors->shouldReceive('getName')->once()->withNoArgs()->andReturn($sectorName);
        $sourceRefData = m::mock(RefData::class);
        $statusRefData = m::mock(RefData::class);
        $permitTypeRefData = m::mock(RefData::class);
        $permitTypeRefData->shouldReceive('getDescription')
            ->once()
            ->withNoArgs()
            ->andReturn($permitTypeDesc);
        $internationalJourneysRefData = m::mock(RefData::class);
        $internationalJourneysRefData->shouldReceive('getDescription')
            ->once()
            ->withNoArgs()
            ->andReturn($internationalJourneysDesc);

        $licence = m::mock(Licence::class);
        $licence->shouldReceive('getLicNo')->twice()->withNoArgs()->andReturn($licNo);
        $licence->shouldReceive('getOrganisation->getName')->once()->withNoArgs()->andReturn($orgName);
        $dateReceived = '2017-12-25';
        $declaration = 1;
        $permitsRequired = 999;
        $trips = 666;

        $expectedData = [
            'permitType' => $permitTypeDesc,
            'operator' => $orgName,
            'ref' => $applicationRef,
            'licence' => $licNo,
            'emissions' => $emissionsResult,
            'cabotage' => $cabotageResult,
            'limitedCountries' => $countriesResult,
            'limitedCountriesList' => $countriesListResult,
            'permitsRequired' => $permitsRequired,
            'trips' => $trips,
            'internationalJourneys' => $internationalJourneysDesc,
            'goods' => $sectorName,
            'emissionsQuestion' => $emissionsQuestion,
            'emissionsDeclaration' => $emissionsDeclaration
        ];

        $application = Entity::createNewInternal(
            $sourceRefData,
            $statusRefData,
            $permitTypeRefData,
            $licence,
            $dateReceived,
            $sectors,
            new ArrayCollection($countries),
            $cabotage,
            $declaration,
            $emissions,
            $permitsRequired,
            $trips,
            $internationalJourneysRefData
        );

        $application->setId($id);
        $application->setHasRestrictedCountries(!empty($countries));

        $irhpPermitApplication = m::mock(IrhpPermitApplication::class);
        $irhpPermitApplication->shouldReceive('getIrhpPermitWindow->getEmissionsCategory->getId')
            ->andReturn($emissionsCategory);
        $application->setIrhpPermitApplications(new ArrayCollection([$irhpPermitApplication]));

        $this->assertSame($expectedData, $application->returnSnapshotData());
    }

    public function dpReturnSnapshotData()
    {
        $country1 = m::mock(Country::class);
        $country1->shouldReceive('getCountryDesc')
            ->andReturn('country 1');

        $country2 = m::mock(Country::class);
        $country2->shouldReceive('getCountryDesc')
            ->andReturn('country 2');

        return [
            'Euro 5' => [
                'cabotage' => 1,
                'cabotageResult' => 'Yes',
                'emissions' => 0,
                'emissionsResult' => 'No',
                'countries' => [$country1, $country2],
                'countriesResult' => 'Yes',
                'countriesListResult' => null,
                'emissionsQuestion'
                    => 'I confirm that my ECMT permits will only be used by vehicles that are environmentally '
                        . 'compliant with Euro 5 emissions standards as a minimum.',
                'emissionsCategory' => IrhpPermitWindow::EMISSIONS_CATEGORY_EURO5_REF,
                'emissionsDeclaration'
                    => 'I confirm that I will not transport goods to, through and from Austria, Greece, Hungary, '
                        . 'Italy or Russia using this ECMT permit.'
            ],
            'Euro 6' => [
                'cabotage' => 0,
                'cabotageResult' => 'No',
                'emissions' => 1,
                'emissionsResult' => 'Yes',
                'countries' => [$country1, $country2],
                'countriesResult' => 'Yes',
                'countriesListResult' => 'country 1, country 2',
                'emissionsQuestion'
                    => 'I confirm that my ECMT permits will only be used by vehicles that are environmentally '
                        . 'compliant with Euro 6 emissions standards.',
                'emissionsCategory' => IrhpPermitWindow::EMISSIONS_CATEGORY_EURO6_REF,
                'emissionsDeclaration'
                    => 'In the next 12 months are you transporting goods to Austria, Greece, Hungary, Italy or Russia?'
            ],
            'Euro 6 without countries' => [
                'cabotage' => 0,
                'cabotageResult' => 'No',
                'emissions' => 1,
                'emissionsResult' => 'Yes',
                'countries' => [],
                'countriesResult' => 'No',
                'countriesListResult' => null,
                'emissionsQuestion'
                    => 'I confirm that my ECMT permits will only be used by vehicles that are environmentally '
                        . 'compliant with Euro 6 emissions standards.',
                'emissionsCategory' => IrhpPermitWindow::EMISSIONS_CATEGORY_EURO6_REF,
                'emissionsDeclaration'
                    => 'In the next 12 months are you transporting goods to Austria, Greece, Hungary, Italy or Russia?'
            ],
        ];
    }

    public function testGetWindowEmissionsCategory()
    {
        $entity = $this->createApplication();

        $irhpPermitApplication = m::mock(IrhpPermitApplication::class);
        $irhpPermitApplication->shouldReceive('getIrhpPermitWindow->getEmissionsCategory->getId')
            ->andReturn(IrhpPermitWindow::EMISSIONS_CATEGORY_EURO6_REF);

        $entity->addIrhpPermitApplications($irhpPermitApplication);

        $this->assertEquals(IrhpPermitWindow::EMISSIONS_CATEGORY_EURO6_REF, $entity->getWindowEmissionsCategory());
    }
}
