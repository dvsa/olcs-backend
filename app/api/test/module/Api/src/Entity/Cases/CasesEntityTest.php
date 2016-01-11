<?php

namespace Dvsa\OlcsTest\Api\Entity\Cases;

use Doctrine\Common\Collections\ArrayCollection;
use Dvsa\Olcs\Api\Entity\Si\SeriousInfringement;
use Dvsa\OlcsTest\Api\Entity\Abstracts\EntityTester;
use Dvsa\Olcs\Api\Entity\Cases\Cases as Entity;
use Dvsa\Olcs\Api\Entity\Application\Application as ApplicationEntity;
use Dvsa\Olcs\Api\Entity\Licence\Licence as LicenceEntity;
use Dvsa\Olcs\Api\Entity\Cases\Complaint as ComplaintEntity;
use Dvsa\Olcs\Api\Entity\System\RefData;
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
     * @expectedException \Dvsa\Olcs\Api\Domain\Exception\ForbiddenException
     */
    public function testCloseThrowsException()
    {
        $this->entity->setOutcomes(new ArrayCollection());

        $this->entity->close();
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
     * @dataProvider canSendMsiResponseProvider
     *
     * @param RefData|null $erruCaseType
     * @param ArrayCollection $si
     * @param bool $expectedResult
     */
    public function testCanSendMsiResponse($erruCaseType, $si, $expectedResult)
    {
        $sut = $this->instantiate($this->entityClass);
        $sut->setSeriousInfringements($si);
        $sut->setErruCaseType($erruCaseType);

        $this->assertEquals($expectedResult, $sut->canSendMsiResponse());
    }

    /**
     * data provider for testCanSendMsiResponse
     *
     * @return array
     */
    public function canSendMsiResponseProvider()
    {
        $siNotValid1 = m::mock(SeriousInfringement::class);
        $siNotValid1->shouldReceive('getErruResponseSent')->andReturn('Y');
        $siNotValid1->shouldReceive('getAppliedPenalties->isEmpty')->andReturn(true);

        $siNotValid2 = m::mock(SeriousInfringement::class);
        $siNotValid2->shouldReceive('getErruResponseSent')->andReturn('N');
        $siNotValid2->shouldReceive('getAppliedPenalties->isEmpty')->andReturn(true);

        $siValid = m::mock(SeriousInfringement::class);
        $siValid->shouldReceive('getErruResponseSent')->andReturn('Y');
        $siValid->shouldReceive('getAppliedPenalties->isEmpty')->andReturn(false);

        $erruCaseType = new RefData('erru_case_t_msi');

        return [
            [null, new ArrayCollection([$siValid]), false],
            [$erruCaseType, new ArrayCollection(), false],
            [$erruCaseType, new ArrayCollection([$siNotValid1]), false],
            [$erruCaseType, new ArrayCollection([$siNotValid2]), false],
            [$erruCaseType, new ArrayCollection([$siValid]), true]
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
        ];

        $this->assertEquals($expected, $this->entity->getCalculatedBundleValues());
    }

    public function testGetContextValue()
    {
        $this->entity->setId(111);

        $this->assertEquals(111, $this->entity->getContextValue());
    }
}
