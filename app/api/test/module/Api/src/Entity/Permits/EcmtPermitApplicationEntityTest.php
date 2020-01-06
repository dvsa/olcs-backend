<?php

namespace Dvsa\OlcsTest\Api\Entity\Permits;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Dvsa\Olcs\Api\Domain\Command\Email\SendEcmtSuccessful;
use Dvsa\Olcs\Api\Domain\Command\Email\SendEcmtUnsuccessful;
use Dvsa\Olcs\Api\Domain\Command\Email\SendEcmtPartSuccessful;
use Dvsa\Olcs\Api\Domain\Exception\ForbiddenException;
use Dvsa\Olcs\Api\Domain\Exception\RuntimeException;
use Dvsa\Olcs\Api\Entity\ContactDetails\Country;
use Dvsa\Olcs\Api\Entity\Fee\Fee;
use Dvsa\Olcs\Api\Entity\Fee\FeeType;
use Dvsa\Olcs\Api\Entity\Generic\Question;
use Dvsa\Olcs\Api\Entity\IrhpInterface;
use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Dvsa\Olcs\Api\Entity\Permits\EcmtPermitApplication as Entity;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitApplication;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitStock;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitType;
use Dvsa\Olcs\Api\Entity\Permits\Sectors;
use Dvsa\Olcs\Api\Entity\Permits\Traits\ApplicationAcceptConsts;
use Dvsa\Olcs\Api\Entity\System\RefData;
use Dvsa\Olcs\Api\Entity\Task\Task;
use Dvsa\Olcs\Api\Entity\WithdrawableInterface;
use Dvsa\OlcsTest\Api\Entity\Abstracts\EntityTester;
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
        $roadworthiness = 1;
        $declaration = 1;
        $emissions = 1;
        $requiredEuro5 = 199;
        $requiredEuro6 = 800;
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
            $roadworthiness,
            $declaration,
            $emissions,
            $requiredEuro5,
            $requiredEuro6,
            $trips,
            $internationalJourneysRefData
        );

        $this->assertSame($sourceRefData, $application->getSource());
        $this->assertSame($statusRefData, $application->getStatus());
        $this->assertSame($permitTypeRefData, $application->getPermitType());
        $this->assertSame($licence, $application->getLicence());
        $this->assertEquals($dateReceived, $application->getDateReceived()->format('Y-m-d'));
        $this->assertEquals($sectors, $application->getSectors());
        $this->assertEquals($countrys, $application->getCountrys());
        $this->assertEquals($expectedHasRestrictedCountries, $application->getHasRestrictedCountries());
        $this->assertEquals($cabotage, $application->getCabotage());
        $this->assertEquals($roadworthiness, $application->getRoadworthiness());
        $this->assertEquals($declaration, $application->getCheckedAnswers()); //auto updated on internal updates
        $this->assertEquals($declaration, $application->getDeclaration());
        $this->assertEquals($emissions, $application->getEmissions());
        $this->assertEquals($requiredEuro5, $application->getRequiredEuro5());
        $this->assertEquals($requiredEuro6, $application->getRequiredEuro6());
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
        $required_euro5 = 199;
        $required_euro6 = 800;
        $trips = 666;
        $internationalJourneys = RefData::INTER_JOURNEY_60_90;
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
            $required_euro5,
            $required_euro6,
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
        $this->assertEquals($required_euro5, $application->getRequiredEuro5());
        $this->assertEquals($required_euro6, $application->getRequiredEuro6());
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
        $entity->withdraw(new RefData(IrhpInterface::STATUS_WITHDRAWN), new RefData(WithdrawableInterface::WITHDRAWN_REASON_BY_USER));
        $this->assertEquals(IrhpInterface::STATUS_WITHDRAWN, $entity->getStatus()->getId());
        $this->assertEquals(WithdrawableInterface::WITHDRAWN_REASON_BY_USER, $entity->getWithdrawReason()->getId());
        $this->assertEquals(date('Y-m-d'), $entity->getWithdrawnDate()->format('Y-m-d'));
    }

    /**
    * @dataProvider dpWithdrawException
    * @expectedException \Dvsa\Olcs\Api\Domain\Exception\ForbiddenException
    */
    public function testWithdrawException($status)
    {
        $entity = $this->createApplication($status);
        $entity->withdraw(new RefData(IrhpInterface::STATUS_WITHDRAWN), new RefData(WithdrawableInterface::WITHDRAWN_REASON_BY_USER));
    }

    /**
     * Tests declining an application
     */
    public function testDecline()
    {
        $entity = $this->createApplicationAwaitingFee();
        $entity->decline(new RefData(IrhpInterface::STATUS_WITHDRAWN), new RefData(WithdrawableInterface::WITHDRAWN_REASON_DECLINED));
        $this->assertEquals(IrhpInterface::STATUS_WITHDRAWN, $entity->getStatus()->getId());
        $this->assertEquals(WithdrawableInterface::WITHDRAWN_REASON_DECLINED, $entity->getWithdrawReason()->getId());
        $this->assertEquals(date('Y-m-d'), $entity->getWithdrawnDate()->format('Y-m-d'));
    }

    /**
     * @dataProvider dpDeclineAcceptException
     * @expectedException \Dvsa\Olcs\Api\Domain\Exception\ForbiddenException
     */
    public function testDeclineException($status)
    {
        $entity = $this->createApplication($status);
        $entity->decline(new RefData(IrhpInterface::STATUS_WITHDRAWN), new RefData(WithdrawableInterface::WITHDRAWN_REASON_BY_USER));
    }

    /**
     * Tests accepting an application
     */
    public function testAccept()
    {
        $entity = $this->createApplicationAwaitingFee();
        $entity->completeIssueFee(new RefData(IrhpInterface::STATUS_FEE_PAID));
        $entity->accept(new RefData(IrhpInterface::STATUS_VALID));
        $this->assertEquals(IrhpInterface::STATUS_VALID, $entity->getStatus()->getId());
    }

    /**
     * @dataProvider dpDeclineAcceptException
     * @expectedException \Dvsa\Olcs\Api\Domain\Exception\ForbiddenException
     */
    public function testAcceptException($status)
    {
        $entity = $this->createApplication($status);
        $entity->accept(new RefData(IrhpInterface::STATUS_VALID));
    }

    /**
     * Tests cancelling an application
     */
    public function testCancel()
    {
        $entity = $this->createApplication();
        $entity->cancel(new RefData(IrhpInterface::STATUS_CANCELLED));
        $this->assertEquals(IrhpInterface::STATUS_CANCELLED, $entity->getStatus()->getId());
        $this->assertEquals(date('Y-m-d'), $entity->getCancellationDate()->format('Y-m-d'));
    }

    /**
     * @dataProvider dpCancelException
     * @expectedException \Dvsa\Olcs\Api\Domain\Exception\ForbiddenException
     */
    public function testCancelException($status)
    {
        $entity = $this->createApplication($status);
        $entity->cancel(new RefData(IrhpInterface::STATUS_CANCELLED));
    }

    /**
     * Pass array of app statuses to make sure an exception is thrown
     *
     * @return array
     */
    public function dpWithdrawException()
    {
        return [
            [IrhpInterface::STATUS_CANCELLED],
            [IrhpInterface::STATUS_NOT_YET_SUBMITTED],
            [IrhpInterface::STATUS_AWAITING_FEE],
            [IrhpInterface::STATUS_WITHDRAWN],
            [IrhpInterface::STATUS_UNSUCCESSFUL],
            [IrhpInterface::STATUS_ISSUED],
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
            [IrhpInterface::STATUS_CANCELLED],
            [IrhpInterface::STATUS_NOT_YET_SUBMITTED],
            [IrhpInterface::STATUS_UNDER_CONSIDERATION],
            [IrhpInterface::STATUS_WITHDRAWN],
            [IrhpInterface::STATUS_UNSUCCESSFUL],
            [IrhpInterface::STATUS_ISSUED],
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
            [IrhpInterface::STATUS_CANCELLED],
            [IrhpInterface::STATUS_AWAITING_FEE],
            [IrhpInterface::STATUS_WITHDRAWN],
            [IrhpInterface::STATUS_UNSUCCESSFUL],
            [IrhpInterface::STATUS_ISSUED],
            [IrhpInterface::STATUS_UNDER_CONSIDERATION]
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
    public function testUpdateRoadworthiness($roadworthiness)
    {
        $entity = $this->createApplicationWithCompletedDeclaration();

        $entity->updateRoadworthiness($roadworthiness);

        $this->assertEquals($roadworthiness, $entity->getRoadworthiness());
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
        $requiredEuro5 = 10;
        $requiredEuro6 = 20;

        $entity = $this->createApplicationWithCompletedDeclaration();

        $entity->updatePermitsRequired($requiredEuro5, $requiredEuro6);

        $this->assertEquals($requiredEuro5+$requiredEuro6, $entity->calculateTotalPermitsRequired());
        $this->assertEquals($requiredEuro5, $entity->getRequiredEuro5());
        $this->assertEquals($requiredEuro6, $entity->getRequiredEuro6());
        $this->assertFalse($entity->getCheckedAnswers());
        $this->assertFalse($entity->getDeclaration());
    }


    /**
     * @expectedException \Dvsa\Olcs\Api\Domain\Exception\RuntimeException
     */
    public function testUpdatePermitsRequiredNull()
    {
        $requiredEuro5 = null;
        $requiredEuro6 = null;

        $entity = $this->createApplicationWithCompletedDeclaration();

        $entity->updatePermitsRequired($requiredEuro5, $requiredEuro6);

        $entity->calculateTotalPermitsRequired();
        $this->assertEquals($requiredEuro5, $entity->getRequiredEuro5());
        $this->assertEquals($requiredEuro6, $entity->getRequiredEuro6());
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
        $internationalJourneys = new RefData(RefData::INTER_JOURNEY_60_90);

        $entity = $this->createApplicationWithCompletedDeclaration();

        $entity->updateInternationalJourneys($internationalJourneys);

        $this->assertEquals(RefData::INTER_JOURNEY_60_90, $entity->getInternationalJourneys()->getId());
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

    /**
     * @dataProvider dpTestGetPermitIntensityOfUse
     */
    public function testGetPermitIntensityOfUse($emissionsCategoryId, $expectedIntensityOfUse)
    {
        $entity = m::mock(Entity::class)->makePartial();
        $entity->setTrips(35);
        $entity->setRequiredEuro5(2);
        $entity->setRequiredEuro6(5);

        $this->assertEquals(
            $expectedIntensityOfUse,
            $entity->getPermitIntensityOfUse($emissionsCategoryId)
        );
    }

    /**
     * @dataProvider dpTestGetPermitIntensityOfUse
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function testGetPermitIntensityOfUseZeroPermitsRequested($emissionsCategoryId, $expectedIntensityOfUse)
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Permit intensity of use cannot be calculated with zero number of permits');

        $entity = m::mock(Entity::class)->makePartial();
        $entity->setRequiredEuro5(0);
        $entity->setRequiredEuro6(0);

        $entity->getPermitIntensityOfUse($emissionsCategoryId);
    }

    public function testGetPermitIntensityOfUseBadEmissionsCategory()
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Unexpected emissionsCategoryId parameter for getPermitIntensityOfUse: xyz');

        $entity = m::mock(Entity::class)->makePartial();
        $entity->getPermitIntensityOfUse('xyz');
    }

    public function dpTestGetPermitIntensityOfUse()
    {
        return [
            [null, 5],
            [RefData::EMISSIONS_CATEGORY_EURO5_REF, 17.5],
            [RefData::EMISSIONS_CATEGORY_EURO6_REF, 7],
        ];
    }

    /**
     * @dataProvider dpTestGetPermitApplicationScore
     */
    public function testGetPermitApplicationScore(
        $emissionsCategoryId,
        $internationalJourneys,
        $expectedPermitApplicationScore
    ) {
        $intensityOfUse = 5;

        $entity = m::mock(Entity::class)->makePartial();
        $entity->shouldReceive('getPermitIntensityOfUse')
            ->with($emissionsCategoryId)
            ->andReturn($intensityOfUse);

        $internationalJourneys = new RefData($internationalJourneys);
        $entity->updateInternationalJourneys($internationalJourneys);

        $this->assertEquals(
            $expectedPermitApplicationScore,
            $entity->getPermitApplicationScore($emissionsCategoryId)
        );
    }

    public function dpTestGetPermitApplicationScore()
    {
        return [
            [null, RefData::INTER_JOURNEY_LESS_60, 1.5],
            [null, RefData::INTER_JOURNEY_60_90, 3.75],
            [null, RefData::INTER_JOURNEY_MORE_90, 5],
            [RefData::EMISSIONS_CATEGORY_EURO5_REF, RefData::INTER_JOURNEY_LESS_60, 1.5],
            [RefData::EMISSIONS_CATEGORY_EURO5_REF, RefData::INTER_JOURNEY_60_90, 3.75],
            [RefData::EMISSIONS_CATEGORY_EURO5_REF, RefData::INTER_JOURNEY_MORE_90, 5],
            [RefData::EMISSIONS_CATEGORY_EURO6_REF, RefData::INTER_JOURNEY_LESS_60, 1.5],
            [RefData::EMISSIONS_CATEGORY_EURO6_REF, RefData::INTER_JOURNEY_60_90, 3.75],
            [RefData::EMISSIONS_CATEGORY_EURO6_REF, RefData::INTER_JOURNEY_MORE_90, 5],
        ];
    }

    public function testGetAssociatedStock()
    {
        $irhpPermitStock = m::mock(IrhpPermitStock::class);

        $irhpPermitApplication = m::mock(IrhpPermitApplication::class);
        $irhpPermitApplication->shouldReceive('getIrhpPermitWindow->getIrhpPermitStock')
            ->once()
            ->withNoArgs()
            ->andReturn($irhpPermitStock);

        $entity = $this->createApplication();
        $entity->setIrhpPermitApplications(new ArrayCollection([$irhpPermitApplication]));

        $this->assertEquals($irhpPermitStock, $entity->getAssociatedStock());
    }

    private function createApplicationUnderConsideration()
    {
        return $this->createApplication(IrhpInterface::STATUS_UNDER_CONSIDERATION);
    }

    private function createApplicationAwaitingFee()
    {
        return $this->createApplication(IrhpInterface::STATUS_AWAITING_FEE);
    }

    private function createApplication($status = IrhpInterface::STATUS_NOT_YET_SUBMITTED)
    {
        $entity = Entity::createNew(
            m::mock(RefData::class),
            new RefData($status),
            m::mock(RefData::class),
            m::mock(Licence::class)
        );

        return $entity;
    }

    private function createApplicationWithCompletedDeclaration($status = IrhpInterface::STATUS_NOT_YET_SUBMITTED)
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
        return $this->createApplication(IrhpInterface::STATUS_VALID);
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
        $roadworthiness = 1;
        $declaration = 1;
        $checkedAnswers = 1;
        $emissions = 1;
        $requiredEuro5 = 199;
        $requiredEuro6 = 800;
        $trips = 666;
        $internationalJourneys = RefData::INTER_JOURNEY_60_90;
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
            $roadworthiness,
            $declaration,
            $emissions,
            $requiredEuro5,
            $requiredEuro6,
            $trips,
            $internationalJourneyRefData
        );

        $application->setCheckedAnswers($checkedAnswers);

        $newLicence = m::mock(Licence::class);

        $application->updateLicence($newLicence);

        $this->assertSame($application->getLicence(), $newLicence);
        $this->assertEquals($application->getCabotage(), null);
        $this->assertNull($application->getRoadworthiness());
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
        $entity = $this->createApplication(IrhpInterface::STATUS_FEE_PAID);
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
        $entity = $this->createApplication(IrhpInterface::STATUS_ISSUING);
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
        $entity = $this->createApplication(IrhpInterface::STATUS_FEE_PAID);
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
            [IrhpInterface::STATUS_CANCELLED],
            [IrhpInterface::STATUS_NOT_YET_SUBMITTED],
            [IrhpInterface::STATUS_UNDER_CONSIDERATION],
            [IrhpInterface::STATUS_WITHDRAWN],
            [IrhpInterface::STATUS_AWAITING_FEE],
            [IrhpInterface::STATUS_UNSUCCESSFUL],
            [IrhpInterface::STATUS_ISSUED],
            [IrhpInterface::STATUS_ISSUING],
            [IrhpInterface::STATUS_VALID],
        ];
    }

    public function testIsIssueInProgressSuccess()
    {
        $entity = $this->createApplication(IrhpInterface::STATUS_ISSUING);
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
            [IrhpInterface::STATUS_CANCELLED],
            [IrhpInterface::STATUS_NOT_YET_SUBMITTED],
            [IrhpInterface::STATUS_UNDER_CONSIDERATION],
            [IrhpInterface::STATUS_WITHDRAWN],
            [IrhpInterface::STATUS_AWAITING_FEE],
            [IrhpInterface::STATUS_FEE_PAID],
            [IrhpInterface::STATUS_UNSUCCESSFUL],
            [IrhpInterface::STATUS_ISSUED],
            [IrhpInterface::STATUS_VALID],
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
            [IrhpInterface::STATUS_CANCELLED, false],
            [IrhpInterface::STATUS_NOT_YET_SUBMITTED, false],
            [IrhpInterface::STATUS_UNDER_CONSIDERATION, false],
            [IrhpInterface::STATUS_WITHDRAWN, false],
            [IrhpInterface::STATUS_AWAITING_FEE, false],
            [IrhpInterface::STATUS_FEE_PAID, true],
            [IrhpInterface::STATUS_UNSUCCESSFUL, false],
            [IrhpInterface::STATUS_ISSUED, false],
            [IrhpInterface::STATUS_ISSUING, false],
            [IrhpInterface::STATUS_VALID, false],
        ];
    }

    /**
     * Tests logic for finding overdue issue fees, and checks that the 4 fees over 10 days old are returned initially
     *
     * $fee1 isn't overdue, so is ignored
     * $fee2 is overdue, but doesn't need to be checked because $fee5 is more recent and will match
     * $fee3 is overdue, is outstanding, but isn't an issue fee
     * $fee4 would be overdue, but is not outstanding, so the fee type is not checked
     * $fee5 is overdue, outstanding and the correct fee type, causes the method to return true
     */
    public function testIssueFeeOverdue()
    {
        $entity = $this->createApplication();

        $dateTimeMinus9 = (new \DateTime('-9 weekdays'))->format(\DateTime::ISO8601);
        $dateTimeMinus10 = (new \DateTime('-10 weekdays'))->format(\DateTime::ISO8601);
        $dateTimeMinus11 = (new \DateTime('-11 weekdays'))->format(\DateTime::ISO8601);

        $fee1 = m::mock(Fee::class)->makePartial();
        $fee1->shouldReceive('isOutstanding')->never();
        $fee1->shouldReceive('getFeeType->isEcmtIssue')->never();
        $fee1->setInvoicedDate($dateTimeMinus9);

        $fee2 = m::mock(Fee::class)->makePartial();
        $fee2->shouldReceive('isOutstanding')->never();
        $fee2->shouldReceive('getFeeType->isEcmtIssue')->never();
        $fee2->setInvoicedDate($dateTimeMinus11);

        $fee3 = m::mock(Fee::class)->makePartial();
        $fee3->shouldReceive('isOutstanding')->once()->withNoArgs()->andReturn(true);
        $fee3->shouldReceive('getFeeType->isEcmtIssue')->once()->withNoArgs()->andReturn(false);
        $fee3->setInvoicedDate($dateTimeMinus10);

        $fee4 = m::mock(Fee::class)->makePartial();
        $fee4->shouldReceive('isOutstanding')->once()->withNoArgs()->andReturn(false);
        $fee4->shouldReceive('getFeeType->isEcmtIssue')->never();
        $fee4->setInvoicedDate($dateTimeMinus10);

        $fee5 = m::mock(Fee::class)->makePartial();
        $fee5->shouldReceive('isOutstanding')->once()->withNoArgs()->andReturn(true);
        $fee5->shouldReceive('getFeeType->isEcmtIssue')->once()->withNoArgs()->andReturn(true);
        $fee5->setInvoicedDate($dateTimeMinus10);

        $feesCollection = new ArrayCollection([$fee1, $fee2, $fee3, $fee4, $fee5]);

        $entity->setFees($feesCollection);

        $this->assertEquals(4, $entity->getFeesByAge()->count());
        $this->assertTrue($entity->issueFeeOverdue());
    }

    /**
     * @dataProvider dpIssueFeeOverdueProvider
     */
    public function testIssueFeeOverdueBoundary($days, $expected)
    {
        $entity = $this->createApplication();
        $invoiceDate = (new \DateTime('-' . $days . ' weekdays'))->format(\DateTime::ISO8601);

        $fee = m::mock(Fee::class)->makePartial();
        $fee->shouldReceive('isOutstanding')->times($expected)->andReturn(true);
        $fee->shouldReceive('getFeeType->isEcmtIssue')->times($expected)->andReturn(true);
        $fee->setInvoicedDate($invoiceDate);

        $feesCollection = new ArrayCollection([$fee]);

        $entity->setFees($feesCollection);

        $this->assertEquals($expected, $entity->getFeesByAge()->count());
        $this->assertEquals($expected, $entity->issueFeeOverdue());
    }

    public function dpIssueFeeOverdueProvider()
    {
        return [
            [9, 0],
            [10, 1],
            [11, 1],
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
            [IrhpInterface::STATUS_CANCELLED],
            [IrhpInterface::STATUS_UNDER_CONSIDERATION],
            [IrhpInterface::STATUS_WITHDRAWN],
            [IrhpInterface::STATUS_AWAITING_FEE],
            [IrhpInterface::STATUS_FEE_PAID],
            [IrhpInterface::STATUS_UNSUCCESSFUL],
            [IrhpInterface::STATUS_ISSUED],
            [IrhpInterface::STATUS_ISSUING],
            [IrhpInterface::STATUS_VALID],
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
        $statusRefData = new RefData(IrhpInterface::STATUS_NOT_YET_SUBMITTED);
        $permitTypeRefData = m::mock(RefData::class);
        $licence = m::mock(Licence::class);
        $dateReceived = '2017-12-25';
        $sectors = m::mock(Sectors::class);
        $cabotage = 1;
        $roadworthiness = 1;
        $declaration = 1;
        $emissions = 1;
        $requiredEuro5 = 199;
        $requiredEuro6 = 800;
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
            $roadworthiness,
            $declaration,
            $emissions,
            $requiredEuro5,
            $requiredEuro6,
            $trips,
            $internationalJourneysRefData
        );

        $stock = m::mock(IrhpPermitStock::class);

        $irhpPermitApplication = m::mock(IrhpPermitApplication::class);
        $irhpPermitApplication->shouldReceive('getIrhpPermitWindow->getIrhpPermitStock')
            ->once()
            ->withNoArgs()
            ->andReturn($stock);

        $application->setIrhpPermitApplications(new ArrayCollection([$irhpPermitApplication]));

        $licence->shouldReceive('canMakeEcmtApplication')
            ->once()
            ->with($stock, $application)
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

    public function dpGetQuestionAnswerData()
    {
        $country1 = m::mock(Country::class);
        $country1->shouldReceive('getCountryDesc')->withNoArgs()->andReturn('Country 1');
        $country2 = m::mock(Country::class);
        $country2->shouldReceive('getCountryDesc')->withNoArgs()->andReturn('Country 2');

        $restrictedCountries = [$country1, $country2];

        return [
            'euro 5 only with restricted countries' => [
                4,
                null,
                ['4 permits for Euro 5 minimum emission standard'],
                $restrictedCountries,
                ['Yes', 'Country 1, Country 2'],
            ],
            'euro 5 only with no restricted countries' => [
                4,
                null,
                ['4 permits for Euro 5 minimum emission standard'],
                [],
                ['No'],
            ],
            'euro 6 only with restricted countries' => [
                null,
                7,
                ['7 permits for Euro 6 minimum emission standard'],
                $restrictedCountries,
                ['Yes', 'Country 1, Country 2'],
            ],
            'euro 6 only with no restricted countries' => [
                null,
                7,
                ['7 permits for Euro 6 minimum emission standard'],
                [],
                ['No'],
            ],
            'both emission types with restricted countries' => [
                4,
                7,
                [
                    '4 permits for Euro 5 minimum emission standard',
                    '7 permits for Euro 6 minimum emission standard',
                ],
                $restrictedCountries,
                ['Yes', 'Country 1, Country 2'],
            ],
            'both emission types with no restricted countries' => [
                4,
                7,
                [
                    '4 permits for Euro 5 minimum emission standard',
                    '7 permits for Euro 6 minimum emission standard',
                ],
                [],
                ['No'],
            ],
        ];
    }

    /**
     * @dataProvider dpGetQuestionAnswerData
     */
    public function testGetQuestionAnswerData(
        $requiredEuro5,
        $requiredEuro6,
        $expectedPermitsRequired,
        $countries,
        $countriesAnswer
    ) {
        $licNo = 'OB1234567';
        $internationalJourneysRefDataId = 'international journey ref data id';
        $sectorName = 'sector name';
        $dateReceived = '2017-12-25';
        $emissionsValue = 1;
        $roadworthinessValue = 0;
        $cabotageValue = 1;
        $declaration = 1;
        $trips = 666;
        $year = 2019;
        $permitsRequiredAnswer = array_merge(['Permits for '  . $year], $expectedPermitsRequired);

        $sectors = m::mock(Sectors::class);
        $sectors->shouldReceive('getName')->once()->withNoArgs()->andReturn($sectorName);
        $sourceRefData = m::mock(RefData::class);
        $statusRefData = m::mock(RefData::class);
        $permitTypeRefData = m::mock(RefData::class);
        $internationalJourneysRefData = m::mock(RefData::class);
        $internationalJourneysRefData->shouldReceive('getId')
            ->once()
            ->withNoArgs()
            ->andReturn($internationalJourneysRefDataId);

        $licence = m::mock(Licence::class);
        $licence->shouldReceive('getLicNo')->once()->withNoArgs()->andReturn($licNo);

        $application = Entity::createNewInternal(
            $sourceRefData,
            $statusRefData,
            $permitTypeRefData,
            $licence,
            $dateReceived,
            $sectors,
            new ArrayCollection($countries),
            $cabotageValue,
            $roadworthinessValue,
            $declaration,
            $emissionsValue,
            $requiredEuro5,
            $requiredEuro6,
            $trips,
            $internationalJourneysRefData
        );

        $irhpPermitApplication = m::mock(IrhpPermitApplication::class);
        $irhpPermitApplication->shouldReceive('getIrhpPermitWindow->getIrhpPermitStock->getValidityYear')
            ->once()
            ->andReturn($year);
        $application->addIrhpPermitApplications($irhpPermitApplication);

        $expectedData = [
            [
                'question' => 'permits.check-answers.page.question.licence',
                'answer' =>  $licNo,
                'questionType' => Question::QUESTION_TYPE_STRING,
            ],
            [
                'question' => 'permits.form.cabotage.label',
                'answer' =>  $cabotageValue,
                'questionType' => Question::QUESTION_TYPE_BOOLEAN,
            ],
            [
                'question' => 'permits.page.roadworthiness.question',
                'answer' =>  $roadworthinessValue,
                'questionType' => Question::QUESTION_TYPE_BOOLEAN,
            ],
            [
                'question' => 'permits.page.restricted-countries.question',
                'answer' => $countriesAnswer,
                'questionType' => Question::QUESTION_TYPE_STRING,
            ],
            [
                'question' => 'permits.form.euro-emissions.label',
                'answer' =>  $emissionsValue,
                'questionType' => Question::QUESTION_TYPE_BOOLEAN,
            ],
            [
                'question' => 'permits.page.permits.required.question',
                'answer' => $permitsRequiredAnswer,
                'questionType' => Question::QUESTION_TYPE_STRING,
            ],
            [
                'question' => 'permits.page.number-of-trips.question',
                'answer' => $trips,
                'questionType' => Question::QUESTION_TYPE_INTEGER,
            ],
            [
                'question' => 'permits.page.international.journey.question',
                'answer' => $internationalJourneysRefDataId,
                'questionType' => Question::QUESTION_TYPE_STRING,
            ],
            [
                'question' => 'permits.page.sectors.question',
                'answer' => $sectorName,
                'questionType' => Question::QUESTION_TYPE_STRING,
            ],
        ];

        $this->assertEquals($expectedData, $application->getQuestionAnswerData());
    }

    /**
     *
     * @dataProvider productRefMonthProvider
     */
    public function testGetProductReferenceForTier($expected, $validFrom, $validTo, $now)
    {
        $entity = $this->createApplication();

        $irhpPermitApplication = m::mock(IrhpPermitApplication::class);
        $entity->addIrhpPermitApplications(new ArrayCollection([$irhpPermitApplication]));

        $irhpPermitStock = m::mock(IrhpPermitStock::class);
        $irhpPermitApplication->shouldReceive('getIrhpPermitWindow->getIrhpPermitStock')
            ->andReturn($irhpPermitStock);

        $irhpPermitStock->shouldReceive('getValidFrom')->andReturn($validFrom);
        $irhpPermitStock->shouldReceive('getValidTo')->andReturn($validTo);
        $this->assertEquals($expected, $entity->getProductReferenceForTier($now));
    }

    public function productRefMonthProvider()
    {
        $validFrom = new DateTime('first day of January next year');
        $validTo = new DateTime('last day of December next year');

        return [
            [
                FeeType::FEE_TYPE_ECMT_ISSUE_100_PRODUCT_REF,
                $validFrom,
                $validTo,
                new DateTime('first day of January next year')
            ],
            [
                FeeType::FEE_TYPE_ECMT_ISSUE_100_PRODUCT_REF,
                $validFrom,
                $validTo,
                new DateTime('first day of February next year')
            ],
            [
                FeeType::FEE_TYPE_ECMT_ISSUE_100_PRODUCT_REF,
                $validFrom,
                $validTo,
                new DateTime('first day of March next year')
            ],
            [
                FeeType::FEE_TYPE_ECMT_ISSUE_75_PRODUCT_REF,
                $validFrom,
                $validTo,
                new DateTime('first day of April next year')
            ],
            [
                FeeType::FEE_TYPE_ECMT_ISSUE_75_PRODUCT_REF,
                $validFrom,
                $validTo,
                new DateTime('first day of May next year')
            ],
            [
                FeeType::FEE_TYPE_ECMT_ISSUE_75_PRODUCT_REF,
                $validFrom,
                $validTo,
                new DateTime('first day of June next year')
            ],
            [
                FeeType::FEE_TYPE_ECMT_ISSUE_50_PRODUCT_REF,
                $validFrom,
                $validTo,
                new DateTime('first day of July next year')
            ],
            [
                FeeType::FEE_TYPE_ECMT_ISSUE_50_PRODUCT_REF,
                $validFrom,
                $validTo,
                new DateTime('first day of August next year')
            ],
            [
                FeeType::FEE_TYPE_ECMT_ISSUE_50_PRODUCT_REF,
                $validFrom,
                $validTo,
                new DateTime('first day of September next year')
            ],
            [
                FeeType::FEE_TYPE_ECMT_ISSUE_25_PRODUCT_REF,
                $validFrom,
                $validTo,
                new DateTime('first day of October next year')
            ],
            [
                FeeType::FEE_TYPE_ECMT_ISSUE_25_PRODUCT_REF,
                $validFrom,
                $validTo,
                new DateTime('first day of November next year')
            ],
            [
                FeeType::FEE_TYPE_ECMT_ISSUE_25_PRODUCT_REF,
                $validFrom,
                $validTo,
                new DateTime('first day of December next year')
            ],
        ];
    }

    /**
     * @dataProvider dpCanBeExpired
     */
    public function testCanBeExpired($statusId, $validPermits, $expected)
    {
        $application = $this->createApplication($statusId);
        $irhpPermitApplication = m::mock(IrhpPermitApplication::class);
        $irhpPermitApplication->shouldReceive('hasValidPermits')->andReturn($validPermits);
        $application->setIrhpPermitApplications(new ArrayCollection([$irhpPermitApplication]));
        $this->assertEquals($expected, $application->canBeExpired());
    }

    public function dpCanBeExpired()
    {
        return [
            [IrhpInterface::STATUS_CANCELLED, true, false],
            [IrhpInterface::STATUS_NOT_YET_SUBMITTED, true, false],
            [IrhpInterface::STATUS_UNDER_CONSIDERATION, true, false],
            [IrhpInterface::STATUS_WITHDRAWN, true, false],
            [IrhpInterface::STATUS_AWAITING_FEE, true, false],
            [IrhpInterface::STATUS_FEE_PAID, true, false],
            [IrhpInterface::STATUS_UNSUCCESSFUL, true, false],
            [IrhpInterface::STATUS_ISSUED, true, false],
            [IrhpInterface::STATUS_ISSUING, true, false],
            [IrhpInterface::STATUS_VALID, true, false],
            [IrhpInterface::STATUS_EXPIRED, true, false],
            [IrhpInterface::STATUS_CANCELLED, false, false],
            [IrhpInterface::STATUS_NOT_YET_SUBMITTED, false, false],
            [IrhpInterface::STATUS_UNDER_CONSIDERATION, false, false],
            [IrhpInterface::STATUS_WITHDRAWN, false, false],
            [IrhpInterface::STATUS_AWAITING_FEE, false, false],
            [IrhpInterface::STATUS_FEE_PAID, false, false],
            [IrhpInterface::STATUS_UNSUCCESSFUL, false, false],
            [IrhpInterface::STATUS_ISSUED, false, false],
            [IrhpInterface::STATUS_ISSUING, false, false],
            [IrhpInterface::STATUS_VALID, false, true],
            [IrhpInterface::STATUS_EXPIRED, false, false]
        ];
    }

    /**
     * @dataProvider dpTestIsActive
     */
    public function testIsActive($status, $expected)
    {
        $entity = $this->createApplication($status);
        $this->assertSame($expected, $entity->isActive());
    }

    public function dpTestIsActive()
    {
        return [
            [IrhpInterface::STATUS_CANCELLED, false],
            [IrhpInterface::STATUS_NOT_YET_SUBMITTED, true],
            [IrhpInterface::STATUS_UNDER_CONSIDERATION, true],
            [IrhpInterface::STATUS_WITHDRAWN, false],
            [IrhpInterface::STATUS_AWAITING_FEE, true],
            [IrhpInterface::STATUS_FEE_PAID, true],
            [IrhpInterface::STATUS_UNSUCCESSFUL, false],
            [IrhpInterface::STATUS_ISSUED, false],
            [IrhpInterface::STATUS_ISSUING, true],
            [IrhpInterface::STATUS_VALID, false],
            [IrhpInterface::STATUS_EXPIRED, false],
        ];
    }

    public function testCalculateTotalPermitsRequired()
    {
        $entity = $this->createApplicationWithCompletedDeclaration();

        $entity->setRequiredEuro5(2);
        $entity->setRequiredEuro6(2);

        $total = $entity->calculateTotalPermitsRequired();

        $this->assertEquals($total, 4);
    }

    /**
     * @expectedException \Dvsa\Olcs\Api\Domain\Exception\RuntimeException
     */
    public function testCalculateTotalPermitsRequiredWhenNull()
    {
        $entity = $this->createApplicationWithCompletedDeclaration();
        $entity->calculateTotalPermitsRequired();
    }

    public function testExpire()
    {
        $entity = m::mock(Entity::class)->makePartial();
        $entity->shouldReceive('canBeExpired')
            ->andReturn(true);

        $this->assertNull($entity->getExpiryDate());
        $status = m::mock(RefData::class);

        $entity->expire($status);
        $this->assertSame($status, $entity->getStatus());
        $this->assertInstanceOf(DateTime::class, $entity->getExpiryDate());
    }

    /**
     * @dataProvider dpTestHasOutstandingFees
     */
    public function testHasOutstandingFees($feeArray, $expected)
    {
        $mockEntity = m::mock(Entity::class)->makePartial();
        $mockEntity->shouldReceive('getLatestOutstandingEcmtApplicationFee')
            ->andReturn($feeArray);
        $this->assertEquals($expected, $mockEntity->hasOutstandingFees());
    }

    public function dpTestHasOutstandingFees()
    {
        return [
            [null, false],
            [[1], true]
        ];
    }

    /**
     * @dataProvider dpTestGetLatestOutstandingEcmtApplicationFee
     */
    public function testGetLatestOutstandingEcmtApplicationFee($fees, $expectedFee)
    {
        $entity = $this->createApplicationAwaitingFee();
        $entity->setFees($fees);
        $this->assertSame($expectedFee, $entity->getLatestOutstandingEcmtApplicationFee());
    }

    public function dpTestGetLatestOutstandingEcmtApplicationFee()
    {
        $appFeeOutstanding = m::mock(Fee::class);
        $appFeeOutstanding
            ->shouldReceive('isOutstanding')
            ->andReturn(true);
        $appFeeOutstanding
            ->shouldReceive('getFeeType->getFeeType->getId')
            ->andReturn(FeeType::FEE_TYPE_ECMT_APP);
        $appFeeOutstanding->allows('getInvoicedDate')
            ->andReturn('2022-01-02 10:10:11');

        $appFeePaid = m::mock(Fee::class);
        $appFeePaid
            ->shouldReceive('isOutstanding')
            ->andReturn(false);
        $appFeePaid->allows('getInvoicedDate')
            ->andReturn('2022-01-02 10:10:11');

        $issueFeeOutstanding = m::mock(Fee::class);
        $issueFeeOutstanding
            ->shouldReceive('isOutstanding')
            ->andReturn(true);
        $issueFeeOutstanding
            ->shouldReceive('getFeeType->getFeeType->getId')
            ->andReturn(FeeType::FEE_TYPE_ECMT_ISSUE);
        $issueFeeOutstanding->allows('getInvoicedDate')
            ->andReturn('2022-01-05 10:10:11');

        $issueFeePaid = m::mock(Fee::class);
        $issueFeePaid
            ->shouldReceive('isOutstanding')
            ->andReturn(false);
        $issueFeePaid->allows('getInvoicedDate')
            ->andReturn('2022-01-07 12:10:11');

        $singleOutstanding = new ArrayCollection([$appFeeOutstanding]);
        $noOutstanding = new ArrayCollection([$appFeePaid]);
        $issueOutstanding = new ArrayCollection([$appFeePaid, $issueFeeOutstanding]);
        $noFees = new ArrayCollection();
        $allPaid = new ArrayCollection([$appFeePaid, $issueFeePaid]);

        return [
            [$singleOutstanding, $appFeeOutstanding],
            [$noOutstanding, null],
            [$issueOutstanding, $issueFeeOutstanding],
            [$noFees, null],
            [$allPaid, null]
        ];
    }

    public function testGetCamelCaseEntityName()
    {
        $application = $this->createApplication();

        $this->assertEquals(
            'ecmtPermitApplication',
            $application->getCamelCaseEntityName()
        );
    }

    public function testGetEmailCommandLookup()
    {
        $expectedEmailCommandLookup = [
            ApplicationAcceptConsts::SUCCESS_LEVEL_NONE => SendEcmtUnsuccessful::class,
            ApplicationAcceptConsts::SUCCESS_LEVEL_PARTIAL => SendEcmtPartSuccessful::class,
            ApplicationAcceptConsts::SUCCESS_LEVEL_FULL => SendEcmtSuccessful::class
        ];

        $application = $this->createApplication();

        $this->assertEquals(
            $expectedEmailCommandLookup,
            $application->getEmailCommandLookup()
        );
    }

    public function testGetIssueFeeProductReference()
    {
        $productReference = 'PRODUCT_REFERENCE_FOR_TIER';

        $entity = m::mock(Entity::class)->makePartial();
        $entity->shouldReceive('getProductReferenceForTier')
            ->andReturn($productReference);

        $this->assertSame(
            $productReference,
            $entity->getIssueFeeProductReference()
        );
    }

    /**
     * @dataProvider dpUpdateChecked
     */
    public function testUpdateChecked($checked)
    {
        $entity = $this->createApplication();

        $entity->updateChecked($checked);
        $this->assertEquals($checked, $entity->getChecked());
    }

    public function dpUpdateChecked()
    {
        return [
            [true],
            [false],
        ];
    }

    public function testGetSubmissionTaskDescription()
    {
        $entity = $this->createApplication();

        $this->assertEquals(
            Task::TASK_DESCRIPTION_ANNUAL_ECMT_RECEIVED,
            $entity->getSubmissionTaskDescription()
        );
    }

    public function testRequiresPreAllocationCheck()
    {
        $entity = $this->createApplication();

        $this->assertTrue(
            $entity->requiresPreAllocationCheck()
        );
    }

    public function testFetchOpenSubmissionTask()
    {
        $application = m::mock(Entity::class)->makePartial();

        $application->shouldReceive('getSubmissionTaskDescription')
            ->withNoArgs()
            ->andReturn('submission task');

        $task1 = $this->createMockTask('description 1', 'N', Task::CATEGORY_PERMITS, Task::SUBCATEGORY_FEE_DUE);
        $task2 = $this->createMockTask('submission task', 'Y', Task::CATEGORY_PERMITS, Task::SUBCATEGORY_APPLICATION);
        $task3 = $this->createMockTask('submission task', 'N', Task::CATEGORY_BUS, Task::SUBCATEGORY_APPLICATION);
        $task4 = $this->createMockTask('submission task', 'N', Task::CATEGORY_PERMITS, Task::SUBCATEGORY_FEE_DUE);
        $task5 = $this->createMockTask('submission task', 'N', Task::CATEGORY_PERMITS, Task::SUBCATEGORY_APPLICATION);
        $task6 = $this->createMockTask('description 2', 'N', Task::CATEGORY_PERMITS, Task::SUBCATEGORY_APPLICATION);

        $application->setTasks(
            new ArrayCollection([$task1, $task2, $task3, $task4, $task5, $task6])
        );

        $this->assertSame(
            $task5,
            $application->fetchOpenSubmissionTask()
        );
    }

    public function testFetchOpenSubmissionTaskNull()
    {
        $application = m::mock(Entity::class)->makePartial();

        $application->shouldReceive('getSubmissionTaskDescription')
            ->withNoArgs()
            ->andReturn('submission task');

        $task1 = $this->createMockTask('description 1', 'N', Task::CATEGORY_PERMITS, Task::SUBCATEGORY_FEE_DUE);
        $task2 = $this->createMockTask('submission task', 'Y', Task::CATEGORY_PERMITS, Task::SUBCATEGORY_APPLICATION);
        $task3 = $this->createMockTask('submission task', 'N', Task::CATEGORY_BUS, Task::SUBCATEGORY_APPLICATION);
        $task4 = $this->createMockTask('submission task', 'N', Task::CATEGORY_PERMITS, Task::SUBCATEGORY_FEE_DUE);
        $task5 = $this->createMockTask('description 2', 'N', Task::CATEGORY_PERMITS, Task::SUBCATEGORY_APPLICATION);

        $application->setTasks(
            new ArrayCollection([$task1, $task2, $task3, $task4, $task5])
        );

        $this->assertNull(
            $application->fetchOpenSubmissionTask()
        );
    }

    private function createMockTask($description, $isClosed, $categoryId, $subcategoryId)
    {
        $task = m::mock(Task::class);
        $task->shouldReceive('getDescription')
            ->withNoArgs()
            ->andReturn($description);
        $task->shouldReceive('getIsClosed')
            ->withNoArgs()
            ->andReturn($isClosed);
        $task->shouldReceive('getCategory->getId')
            ->withNoArgs()
            ->andReturn($categoryId);
        $task->shouldReceive('getSubcategory->getId')
            ->withNoArgs()
            ->andReturn($subcategoryId);

        return $task;
    }

    /**
     * @dataProvider dpCanBeRevivedFromWithdrawn
     */
    public function testCanBeRevivedFromWithdrawn($withdrawReason, $inScope, $expected)
    {
        $application = m::mock(Entity::class)->makePartial();

        $withdrawReasonRefData = m::mock(RefData::class);
        $withdrawReasonRefData->shouldReceive('getId')
            ->withNoArgs()
            ->andReturn($withdrawReason);

        $application->setWithdrawReason($withdrawReasonRefData);

        $application->shouldReceive('isWithdrawn')
            ->withNoArgs()
            ->andReturn(true);

        $application->shouldReceive('getInScope')
            ->withNoArgs()
            ->andReturn($inScope);

        $this->assertEquals(
            $expected,
            $application->canBeRevivedFromWithdrawn()
        );
    }

    public function dpCanBeRevivedFromWithdrawn()
    {
        return [
            [WithdrawableInterface::WITHDRAWN_REASON_UNPAID, true, true],
            [WithdrawableInterface::WITHDRAWN_REASON_DECLINED, true, true],
            [WithdrawableInterface::WITHDRAWN_REASON_NOTSUCCESS, true, false],
            [WithdrawableInterface::WITHDRAWN_REASON_DECLINED, false, false],
        ];
    }

    /**
     * @dataProvider dpCanBeRevivedFromWithdrawnNotWithdrawn
     */
    public function testCanBeRevivedFromWithdrawnNotWithdrawn($inScope, $expected)
    {
        $application = m::mock(Entity::class)->makePartial();

        $application->setWithdrawReason(null);

        $application->shouldReceive('isWithdrawn')
            ->withNoArgs()
            ->andReturn(false);

        $application->shouldReceive('getInScope')
            ->withNoArgs()
            ->andReturn($inScope);

        $this->assertEquals(
            $expected,
            $application->canBeRevivedFromWithdrawn()
        );
    }

    public function dpCanBeRevivedFromWithdrawnNotWithdrawn()
    {
        return [
            [true, false],
            [false, false],
        ];
    }

    public function testReviveFromWithdrawn()
    {
        $application = m::mock(Entity::class)->makePartial();

        $withdrawnStatus = m::mock(RefData::class);
        $withdrawnDate = m::mock(DateTime::class);

        $underConsiderationStatus = m::mock(RefData::class);

        $application->setStatus($withdrawnStatus);
        $application->setWithdrawReason(WithdrawableInterface::WITHDRAWN_REASON_DECLINED);
        $application->setWithdrawnDate = $withdrawnDate;

        $application->shouldReceive('canBeRevivedFromWithdrawn')
            ->withNoArgs()
            ->andReturn(true);

        $application->reviveFromWithdrawn($underConsiderationStatus);

        $this->assertSame($underConsiderationStatus, $application->getStatus());
        $this->assertNull($application->getWithdrawReason());
        $this->assertNull($application->getWithdrawnDate());
    }

    public function testReviveFromWithdrawnException()
    {
        $application = m::mock(Entity::class)->makePartial();

        $this->expectException(ForbiddenException::class);
        $this->expectExceptionMessage('Unable to revive this application from a withdrawn state');

        $application->shouldReceive('canBeRevivedFromWithdrawn')
            ->withNoArgs()
            ->andReturn(false);

        $application->reviveFromWithdrawn(
            m::mock(RefData::class)
        );
    }

    /**
     * @dataProvider dpTestCanBeRevivedFromUnsuccessful
     */
    public function testCanBeRevivedFromUnsuccessful($status, $expected)
    {
        $entity = $this->createApplication($status);
        $this->assertSame($expected, $entity->canBeRevivedFromUnsuccessful());
    }

    public function dpTestCanBeRevivedFromUnsuccessful()
    {
        return [
            [IrhpInterface::STATUS_CANCELLED, false],
            [IrhpInterface::STATUS_NOT_YET_SUBMITTED, false],
            [IrhpInterface::STATUS_UNDER_CONSIDERATION, false],
            [IrhpInterface::STATUS_WITHDRAWN, false],
            [IrhpInterface::STATUS_AWAITING_FEE, false],
            [IrhpInterface::STATUS_FEE_PAID, false],
            [IrhpInterface::STATUS_UNSUCCESSFUL, true],
            [IrhpInterface::STATUS_ISSUED, false],
            [IrhpInterface::STATUS_ISSUING, false],
            [IrhpInterface::STATUS_VALID, false],
            [IrhpInterface::STATUS_EXPIRED, false],
        ];
    }

    public function testReviveFromUnsuccessful()
    {
        $underConsiderationStatus = m::mock(RefData::class);

        $application = m::mock(Entity::class)->makePartial();

        $application->setStatus(m::mock(RefData::class));
        $application->shouldReceive('canBeRevivedFromUnsuccessful')
            ->withNoArgs()
            ->andReturnTrue();

        $application->reviveFromUnsuccessful($underConsiderationStatus);

        $this->assertSame(
            $underConsiderationStatus,
            $application->getStatus()
        );
    }

    public function testReviveFromUnsuccessfulException()
    {
        $this->expectException(ForbiddenException::class);
        $this->expectExceptionMessage('Unable to revive this application from an unsuccessful state');

        $application = m::mock(Entity::class)->makePartial();

        $application->shouldReceive('canBeRevivedFromUnsuccessful')
            ->withNoArgs()
            ->andReturnFalse();

        $application->reviveFromUnsuccessful(
            m::mock(RefData::class)
        );
    }
}
