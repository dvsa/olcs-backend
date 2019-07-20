<?php

namespace Dvsa\OlcsTest\Api\Entity\Permits;

use DateTime;
use Dvsa\Olcs\Api\Domain\Exception\RuntimeException;
use Dvsa\Olcs\Api\Entity\Fee\Fee;
use Dvsa\Olcs\Api\Entity\Fee\FeeType;
use Dvsa\Olcs\Api\Entity\IrhpInterface;
use Dvsa\Olcs\Api\Entity\Permits\EcmtPermitApplication as Entity;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitApplication;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitStock;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitWindow;
use Dvsa\Olcs\Api\Entity\Permits\Sectors;
use Dvsa\Olcs\Api\Entity\ContactDetails\Country;
use Dvsa\Olcs\Api\Entity\System\RefData;
use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Dvsa\Olcs\Api\Entity\WithdrawableInterface;
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
        $this->assertEquals(date('Y-m-d'), $entity->getCancellationDate()->format('Y-m-d'));
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
            [IrhpInterface::STATUS_WITHDRAWN],
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
            [IrhpInterface::STATUS_WITHDRAWN],
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
            [IrhpInterface::STATUS_WITHDRAWN],
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
            [IrhpInterface::STATUS_WITHDRAWN],
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
            [IrhpInterface::STATUS_WITHDRAWN],
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
            [IrhpInterface::STATUS_WITHDRAWN, false],
            [Entity::STATUS_AWAITING_FEE, false],
            [Entity::STATUS_FEE_PAID, true],
            [Entity::STATUS_UNSUCCESSFUL, false],
            [Entity::STATUS_ISSUED, false],
            [Entity::STATUS_ISSUING, false],
            [Entity::STATUS_VALID, false],
            [Entity::STATUS_DECLINED, false],
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
            [IrhpInterface::STATUS_WITHDRAWN],
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
            [IrhpInterface::STATUS_WITHDRAWN],
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

    public function dpGetQuestionAnswerData()
    {
        $country1 = m::mock(Country::class);
        $country1->shouldReceive('getCountryDesc')->withNoArgs()->andReturn('Country 1');
        $country2 = m::mock(Country::class);
        $country2->shouldReceive('getCountryDesc')->withNoArgs()->andReturn('Country 2');

        $restrictedCountries = [$country1, $country2];

        return [
            'euro 6 with restricted countries' => [
                IrhpPermitWindow::EMISSIONS_CATEGORY_EURO6_REF,
                1,
                'permits.form.euro6.label',
                'Yes',
                1,
                'Yes',
                $restrictedCountries,
                'permits.page.restricted-countries.question',
                'Yes',
                ['Country 1, Country 2'],
            ],
            'euro 6 with restricted countries, plus emissions not agreed' => [
                IrhpPermitWindow::EMISSIONS_CATEGORY_EURO6_REF,
                0,
                'permits.form.euro6.label',
                'No',
                1,
                'Yes',
                $restrictedCountries,
                'permits.page.restricted-countries.question',
                'Yes',
                ['Country 1, Country 2'],
            ],
            'euro 6 with restricted countries, plus cabotage not agreed' => [
                IrhpPermitWindow::EMISSIONS_CATEGORY_EURO6_REF,
                1,
                'permits.form.euro6.label',
                'Yes',
                0,
                'No',
                $restrictedCountries,
                'permits.page.restricted-countries.question',
                'Yes',
                ['Country 1, Country 2'],
            ],
            'euro 6 with restricted countries, plus cabotage and emissions not agreed' => [
                IrhpPermitWindow::EMISSIONS_CATEGORY_EURO6_REF,
                0,
                'permits.form.euro6.label',
                'No',
                0,
                'No',
                $restrictedCountries,
                'permits.page.restricted-countries.question',
                'Yes',
                ['Country 1, Country 2'],
            ],
            'euro 6 with no restricted countries' => [
                IrhpPermitWindow::EMISSIONS_CATEGORY_EURO6_REF,
                1,
                'permits.form.euro6.label',
                'Yes',
                0,
                'No',
                [],
                'permits.page.restricted-countries.question',
                'No',
                [],
            ],
            'euro 5' => [
                IrhpPermitWindow::EMISSIONS_CATEGORY_EURO5_REF,
                0,
                'permits.form.euro5.label',
                'No',
                1,
                'Yes',
                [],
                'permits.form.restricted.countries.euro5.label',
                'Yes',
                [],
            ],
        ];
    }

    /**
     * @dataProvider dpGetQuestionAnswerData
     */
    public function testGetQuestionAnswerData(
        $emissionCategory,
        $emissionsValue,
        $emissionsQuestion,
        $emissionsAnswer,
        $cabotageValue,
        $cabotageAnswer,
        $countries,
        $countriesQuestion,
        $countriesAnswer,
        $countriesList
    ) {
        $licNo = 'OB1234567';
        $internationalJourneysDesc = 'international journey desc';
        $sectorName = 'sector name';
        $dateReceived = '2017-12-25';
        $declaration = 1;
        $permitsRequired = 999;
        $trips = 666;

        $irhpPermitApplication = $this->getWindowEmissionCategory($emissionCategory);
        $sectors = m::mock(Sectors::class);
        $sectors->shouldReceive('getName')->once()->withNoArgs()->andReturn($sectorName);
        $sourceRefData = m::mock(RefData::class);
        $statusRefData = m::mock(RefData::class);
        $permitTypeRefData = m::mock(RefData::class);
        $internationalJourneysRefData = m::mock(RefData::class);
        $internationalJourneysRefData->shouldReceive('getDescription')
            ->once()
            ->withNoArgs()
            ->andReturn($internationalJourneysDesc);

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
            $declaration,
            $emissionsValue,
            $permitsRequired,
            $trips,
            $internationalJourneysRefData
        );

        $application->addIrhpPermitApplications($irhpPermitApplication);

        $expectedData = [
            [
                'question' => 'permits.check-answers.page.question.licence',
                'answer' =>  $licNo,
            ],
            [
                'question' => $emissionsQuestion,
                'answer' =>  $emissionsAnswer,
            ],
            [
                'question' => 'permits.form.cabotage.label',
                'answer' =>  $cabotageAnswer,
            ],
            [
                'question' => $countriesQuestion,
                'answer' => $countriesAnswer,
                'answerList' => $countriesList,
            ],
            [
                'question' => 'permits.page.permits.required.question',
                'answer' => $permitsRequired,
            ],
            [
                'question' => 'permits.page.number-of-trips.question',
                'answer' => $trips,
            ],
            [
                'question' => 'permits.page.international.journey.question',
                'answer' => $internationalJourneysDesc,
            ],
            [
                'question' => 'permits.page.sectors.question',
                'answer' => $sectorName,
            ],
        ];

        $this->assertEquals($expectedData, $application->getQuestionAnswerData());
    }

    public function dpEmissionsCategory(): array
    {
        return [
            [IrhpPermitWindow::EMISSIONS_CATEGORY_EURO6_REF, true],
            [IrhpPermitWindow::EMISSIONS_CATEGORY_EURO5_REF, false],
            [IrhpPermitWindow::EMISSIONS_CATEGORY_NA_REF, false],
        ];
    }

    /**
     * @dataProvider dpEmissionsCategory
     */
    public function testIsEuro6EmissionCategory($category, $expectedResponse)
    {
        $entity = $this->createApplication();
        $irhpPermitApplication = $this->getWindowEmissionCategory($category);
        $entity->addIrhpPermitApplications(new ArrayCollection([$irhpPermitApplication]));

        $this->assertEquals($expectedResponse, $entity->isEuro6EmissionCategory());
    }

    public function testGetWindowEmissionsCategory()
    {
        $entity = $this->createApplication();
        $irhpPermitApplication = $this->getWindowEmissionCategory(IrhpPermitWindow::EMISSIONS_CATEGORY_EURO6_REF);
        $entity->addIrhpPermitApplications(new ArrayCollection([$irhpPermitApplication]));

        $this->assertEquals(IrhpPermitWindow::EMISSIONS_CATEGORY_EURO6_REF, $entity->getWindowEmissionsCategory());
    }

    private function getWindowEmissionCategory($emissionCategory)
    {
        $irhpPermitApplication = m::mock(IrhpPermitApplication::class);
        $irhpPermitApplication->shouldReceive('getIrhpPermitWindow->getEmissionsCategory->getId')
            ->andReturn($emissionCategory);

        return $irhpPermitApplication;
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
            [Entity::STATUS_CANCELLED, true, false],
            [Entity::STATUS_NOT_YET_SUBMITTED, true, false],
            [Entity::STATUS_UNDER_CONSIDERATION, true, false],
            [IrhpInterface::STATUS_WITHDRAWN, true, false],
            [Entity::STATUS_AWAITING_FEE, true, false],
            [Entity::STATUS_FEE_PAID, true, false],
            [Entity::STATUS_UNSUCCESSFUL, true, false],
            [Entity::STATUS_ISSUED, true, false],
            [Entity::STATUS_ISSUING, true, false],
            [Entity::STATUS_VALID, true, false],
            [Entity::STATUS_DECLINED, true, false],
            [Entity::STATUS_EXPIRED, true, false],
            [Entity::STATUS_CANCELLED, false, false],
            [Entity::STATUS_NOT_YET_SUBMITTED, false, false],
            [Entity::STATUS_UNDER_CONSIDERATION, false, false],
            [IrhpInterface::STATUS_WITHDRAWN, false, false],
            [Entity::STATUS_AWAITING_FEE, false, false],
            [Entity::STATUS_FEE_PAID, false, false],
            [Entity::STATUS_UNSUCCESSFUL, false, false],
            [Entity::STATUS_ISSUED, false, false],
            [Entity::STATUS_ISSUING, false, false],
            [Entity::STATUS_VALID, false, true],
            [Entity::STATUS_DECLINED, false, false],
            [Entity::STATUS_EXPIRED, false, false]
        ];
    }
}
