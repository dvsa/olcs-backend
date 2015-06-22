<?php

namespace Dvsa\OlcsTest\Api\Entity\Cases;

use Doctrine\Common\Collections\ArrayCollection;
use Dvsa\OlcsTest\Api\Entity\Abstracts\EntityTester;
use Dvsa\Olcs\Api\Entity\Cases\Cases as Entity;

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
        $caseType = '';
        $categorys = new ArrayCollection();
        $outcomes = new ArrayCollection();
        $application = null;
        $licence = null;
        $transportManager = null;
        $ecmsNo = 'abcd123456';
        $description = 'description';

        $this->entity->update(
            $caseType,
            $categorys,
            $outcomes,
            $application,
            $licence,
            $transportManager,
            $ecmsNo,
            $description
        );

        $this->assertEquals($caseType, $this->entity->getCaseType());
        $this->assertEquals($categorys, $this->entity->getCategorys());
        $this->assertEquals($outcomes, $this->entity->getOutcomes());
        $this->assertEquals($application, $this->entity->getApplication());
        $this->assertEquals($licence, $this->entity->getLicence());
        $this->assertEquals($transportManager, $this->entity->getTransportManager());
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
}
