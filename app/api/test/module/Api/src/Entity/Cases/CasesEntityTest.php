<?php

namespace Dvsa\OlcsTest\Api\Entity\Cases;

use Doctrine\Common\Collections\ArrayCollection;
use Dvsa\Olcs\Api\Entity\Si\SeriousInfringement;
use Dvsa\Olcs\Api\Entity\Si\ErruRequest as ErruRequestEntity;
use Dvsa\OlcsTest\Api\Entity\Abstracts\EntityTester;
use Dvsa\Olcs\Api\Entity\Cases\Cases as Entity;
use Dvsa\Olcs\Api\Entity\Application\Application as ApplicationEntity;
use Dvsa\Olcs\Api\Entity\Licence\Licence as LicenceEntity;
use Dvsa\Olcs\Api\Entity\Cases\Complaint as ComplaintEntity;
use Dvsa\Olcs\Api\Entity\System\RefData;
use Dvsa\Olcs\Api\Entity\Cases\Appeal as AppealEntity;
use Dvsa\Olcs\Api\Entity\Cases\Stay as StayEntity;
use Mockery as m;

/**
 * Cases Entity Unit Tests
 *
 * Initially auto-generated but won't be overridden
 */
class CasesEntityTest extends EntityTester
{
    public function setUp()
    {
        /** @var \Dvsa\Olcs\Api\Entity\Cases\Cases entity */
        $this->entity = $this->instantiate($this->entityClass);
    }

    /**
     * Define the entity to test
     *
     * @var string
     */
    protected $entityClass = Entity::class;

    /**
     * Tests update
     */
    public function testCreate()
    {
        $caseType = m::mock(RefData::class);
        $openDate = new \DateTime();
        $categorys = new ArrayCollection();
        $outcomes = new ArrayCollection();
        $ecmsNo = 'abcd123456';
        $description = 'description';
        $correctLicenceId = 7;
        $incorrectLicenceId = 99;

        $postedLicence = m::mock(LicenceEntity::class);
        $postedLicence->shouldReceive('getId')->andReturn($incorrectLicenceId);

        $applicationLicence = m::mock(LicenceEntity::class);
        $applicationLicence->shouldReceive('getId')->andReturn($correctLicenceId);
        $application = m::mock(ApplicationEntity::class);
        $application->shouldReceive('canCreateCase')->andReturn(true);
        $application->shouldReceive('getLicence')->andReturn($applicationLicence);
        $transportManager = null;

        $sut = new Entity(
            $openDate,
            $caseType,
            $categorys,
            $outcomes,
            $application,
            $postedLicence,
            $transportManager,
            $ecmsNo,
            $description
        );

        $this->assertEquals($caseType, $sut->getCaseType());
        $this->assertEquals($categorys, $sut->getCategorys());
        $this->assertEquals($outcomes, $sut->getOutcomes());
        $this->assertEquals($ecmsNo, $sut->getEcmsNo());
        $this->assertEquals($description, $sut->getDescription());
        $this->assertEquals($correctLicenceId, $sut->getLicence()->getId());
        $this->assertEquals($application, $sut->getApplication());
        $this->assertEquals($transportManager, $sut->getTransportManager());
    }

    /**
     * Tests create function throws an exception when application can't create cases
     *
     * @expectedException \Dvsa\Olcs\Api\Domain\Exception\ForbiddenException
     */
    public function testCreateThrowsExceptionWhenApplicationCantCreate()
    {
        $caseType = m::mock(RefData::class);
        $openDate = new \DateTime();
        $categorys = new ArrayCollection();
        $outcomes = new ArrayCollection();
        $ecmsNo = 'abcd123456';
        $description = 'description';
        $postedLicence = m::mock(LicenceEntity::class);
        $application = m::mock(ApplicationEntity::class);
        $application->shouldReceive('canCreateCase')->andReturn(false);
        $transportManager = null;

        $sut = new Entity(
            $openDate,
            $caseType,
            $categorys,
            $outcomes,
            $application,
            $postedLicence,
            $transportManager,
            $ecmsNo,
            $description
        );
    }

    /**
     * Tests update
     */
    public function testUpdate()
    {
        $caseType = m::mock(RefData::class);
        $categorys = new ArrayCollection();
        $outcomes = new ArrayCollection();
        $ecmsNo = 'abcd123456';
        $description = 'description';

        $this->entity->setCaseType('case_t_lic');

        $this->entity->update(
            $caseType,
            $categorys,
            $outcomes,
            $ecmsNo,
            $description
        );

        $this->assertEquals($caseType, $this->entity->getCaseType());
        $this->assertEquals($categorys, $this->entity->getCategorys());
        $this->assertEquals($outcomes, $this->entity->getOutcomes());
        $this->assertEquals($ecmsNo, $this->entity->getEcmsNo());
        $this->assertEquals($description, $this->entity->getDescription());

        return true;
    }

    /**
     * Tests updateAnnualTestHistory
     */
    public function testUpdateAnnualTestHistory()
    {
        $annualTestHistory = 'annual test history';

        $this->entity->updateAnnualTestHistory(
            $annualTestHistory
        );

        $this->assertEquals($annualTestHistory, $this->entity->getAnnualTestHistory());

        return true;
    }

    /**
     * Tests updateConvictionNote
     */
    public function testUpdateConvictionNote()
    {
        $convictionNote = 'conviction note';

        $this->entity->updateConvictionNote(
            $convictionNote
        );

        $this->assertEquals($convictionNote, $this->entity->getConvictionNote());

        return true;
    }

    /**
     * Tests updateProhibitionNote
     */
    public function testUpdateProhibitionNote()
    {
        $prohibitionNote = 'prohibition note';

        $this->entity->updateProhibitionNote(
            $prohibitionNote
        );

        $this->assertEquals($prohibitionNote, $this->entity->getProhibitionNote());

        return true;
    }

    public function testIsOpen()
    {
        $sut = $this->instantiate($this->entityClass);

        $this->assertTrue($sut->isOpen());

        $sut->setClosedDate('2015-06-10');

        $this->assertFalse($sut->isOpen());

        $sut->setClosedDate(null);
        $sut->setDeletedDate('2015-06-10');

        $this->assertFalse($sut->isOpen());
    }

    public function testHasComplaints()
    {
        $sut = $this->instantiate($this->entityClass);

        $this->assertFalse($sut->hasComplaints());

        $complaint = m::mock(ComplaintEntity::class)->makePartial();
        $sut->getComplaints()->add($complaint);

        $this->assertTrue($sut->hasComplaints());
    }

    /**
     * Tests closing a case
     */
    public function testClose()
    {
        $outcome = m::mock(RefData::class);

        $this->entity->setOutcomes(new ArrayCollection([$outcome]));

        $this->entity->close();

        $this->assertInstanceOf('\DateTime', $this->entity->getClosedDate());
    }

    /**
     * @expectedException \Dvsa\Olcs\Api\Domain\Exception\ValidationException
     */
    public function testCloseThrowsException()
    {
        $this->entity->setOutcomes(new ArrayCollection());

        $this->entity->close();
    }

    /**
     * @expectedException \Dvsa\Olcs\Api\Domain\Exception\ValidationException
     */
    public function testCloseWithOutstandingAppealThrowsValidationException()
    {
        $outcome = m::mock(RefData::class);
        $appeal = new AppealEntity('1234');

        $this->entity->setAppeal($appeal);
        $this->entity->setOutcomes(new ArrayCollection([$outcome]));

        $this->entity->close();
    }

    public function testCloseWithWithdrawnAppeal()
    {
        $outcome = m::mock(RefData::class);
        $appeal = new AppealEntity('1234');
        $appeal->setWithdrawnDate('2015-06-10');

        $this->entity->setAppeal($appeal);
        $this->entity->setOutcomes(new ArrayCollection([$outcome]));

        $this->entity->close();
        $this->assertInstanceOf('\DateTime', $this->entity->getClosedDate());
    }

    public function testCloseWithCompleteAppeal()
    {
        $outcome = m::mock(RefData::class);
        $appeal = new AppealEntity('1234');

        $appeal->setOutcome($outcome);
        $appeal->setDecisionDate('2015-06-10');

        $this->entity->setAppeal($appeal);
        $this->entity->setOutcomes(new ArrayCollection([$outcome]));

        $this->entity->close();
        $this->assertInstanceOf('\DateTime', $this->entity->getClosedDate());
    }

    /**
     * @expectedException \Dvsa\Olcs\Api\Domain\Exception\ValidationException
     */
    public function testCloseWithOutstandingStay()
    {
        $outcome = m::mock(RefData::class);
        $stay = new StayEntity($this->entity, $outcome);

        $this->entity->setStays(new ArrayCollection([$stay]));
        $this->entity->setOutcomes(new ArrayCollection([$outcome]));

        $this->entity->close();
    }

    public function testCloseWithWithdrawnStay()
    {
        $outcome = m::mock(RefData::class);
        $stay = new StayEntity($this->entity, $outcome);
        $stay->setWithdrawnDate('2015-06-10');

        $this->entity->setStays(new ArrayCollection([$stay]));
        $this->entity->setOutcomes(new ArrayCollection([$outcome]));

        $this->entity->close();
        $this->assertInstanceOf('\DateTime', $this->entity->getClosedDate());
    }

    public function testCloseWithCompleteStay()
    {
        $outcome = m::mock(RefData::class);
        $stay = new StayEntity($this->entity, $outcome);
        $stay->setOutcome($outcome);
        $stay->setDecisionDate('2015-06-10');

        $this->entity->setStays(new ArrayCollection([$stay]));
        $this->entity->setOutcomes(new ArrayCollection([$outcome]));

        $this->entity->close();
        $this->assertInstanceOf('\DateTime', $this->entity->getClosedDate());
    }

    public function testReopen()
    {
        $this->entity->setClosedDate(new \DateTime());

        $this->entity->reopen();

        $this->assertEquals(null, $this->entity->getClosedDate());
    }

    /**
     * @expectedException \Dvsa\Olcs\Api\Domain\Exception\ForbiddenException
     */
    public function testReopenThrowsException()
    {
        $this->entity->setClosedDate(null);
        $this->entity->reopen();
    }

    /**
     * @dataProvider canAddSiProvider
     *
     * @param ErruRequestEntity|null $erruRequest
     * @param \DateTime|null $closedDate
     * @param bool $expectedResult
     */
    public function testCanAddSi($erruRequest, $closedDate, $expectedResult)
    {
        $sut = $this->instantiate($this->entityClass);
        $sut->setErruRequest($erruRequest);
        $sut->setClosedDate($closedDate);

        $this->assertEquals($expectedResult, $sut->canAddSi());
    }

    /**
     * data provider for testCanSendMsiResponse
     *
     * @return array
     */
    public function canAddSiProvider()
    {
        $erruRequestNoModify = m::mock(ErruRequestEntity::class);
        $erruRequestNoModify->shouldReceive('canModify')->andReturn(false);

        $erruRequestCanModify = m::mock(ErruRequestEntity::class);
        $erruRequestCanModify->shouldReceive('canModify')->andReturn(true);

        $closedDate = new \DateTime('2016-12-25');

        return [
            [null, $closedDate, false],
            [$erruRequestNoModify, $closedDate, false],
            [$erruRequestCanModify, $closedDate, false],
            [null, null, false],
            [$erruRequestNoModify, null, false],
            [$erruRequestCanModify, null, true]
        ];
    }

    /**
     * @dataProvider canSendMsiResponseProvider
     *
     * @param ErruRequestEntity|null $erruRequest
     * @param ArrayCollection $si
     * @param \DateTime|null $closedDate
     * @param bool $expectedResult
     */
    public function testCanSendMsiResponse($erruRequest, $si, $closedDate, $expectedResult)
    {
        $sut = $this->instantiate($this->entityClass);
        $sut->setSeriousInfringements($si);
        $sut->setErruRequest($erruRequest);
        $sut->setClosedDate($closedDate);

        $this->assertEquals($expectedResult, $sut->canSendMsiResponse());
    }

    /**
     * data provider for testCanSendMsiResponse
     *
     * @return array
     */
    public function canSendMsiResponseProvider()
    {
        $siResponseSet = m::mock(SeriousInfringement::class);
        $siResponseSet->shouldReceive('responseSet')->andReturn(true);

        $siNoResponseSet = m::mock(SeriousInfringement::class);
        $siNoResponseSet->shouldReceive('responseSet')->andReturn(false);

        $erruRequestNoModify = m::mock(ErruRequestEntity::class);
        $erruRequestNoModify->shouldReceive('canModify')->andReturn(false);

        $erruRequestCanModify = m::mock(ErruRequestEntity::class);
        $erruRequestCanModify->shouldReceive('canModify')->andReturn(true);

        $closedDate = new \DateTime('2016-12-25');

        return [
            [null, new ArrayCollection([$siResponseSet]), $closedDate, false],
            [$erruRequestNoModify, new ArrayCollection([$siResponseSet]), $closedDate, false],
            [$erruRequestCanModify, new ArrayCollection([$siNoResponseSet, $siResponseSet]), $closedDate, false],
            [$erruRequestCanModify, new ArrayCollection([$siResponseSet]), $closedDate, false],
            [null, new ArrayCollection([$siResponseSet]), null, false],
            [$erruRequestNoModify, new ArrayCollection([$siResponseSet]), null, false],
            [$erruRequestCanModify, new ArrayCollection([$siNoResponseSet, $siResponseSet]), null, false],
            [$erruRequestCanModify, new ArrayCollection([$siResponseSet]), null, true]
        ];
    }

    /**
     * Tests getCalculatedBundleValues
     */
    public function testGetCalculatedBundleValues()
    {
        $expected = [
            'isClosed' => false,
            'canReopen' => false,
            'canClose' => false,
            'canSendMsiResponse' => false,
            'canAddSi' => false,
            'isErru' => false,
        ];

        $this->assertEquals($expected, $this->entity->getCalculatedBundleValues());
    }

    public function testGetContextValue()
    {
        $this->entity->setId(111);

        $this->assertEquals(111, $this->entity->getContextValue());
    }
}
