<?php

namespace Dvsa\OlcsTest\Api\Entity\Cases;

use Doctrine\Common\Collections\ArrayCollection;
use Dvsa\OlcsTest\Api\Entity\Abstracts\EntityTester;
use Dvsa\Olcs\Api\Entity\Cases\Cases as Entity;
use Dvsa\Olcs\Api\Entity\Cases\Complaint as ComplaintEntity;
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
    public function testUpdate()
    {
        $caseType = 'case_t_lic';
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
}
