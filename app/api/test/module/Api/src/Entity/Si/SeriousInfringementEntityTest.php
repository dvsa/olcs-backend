<?php

namespace Dvsa\OlcsTest\Api\Entity\Si;

use Doctrine\Common\Collections\ArrayCollection;
use Dvsa\Olcs\Api\Entity\Cases\Cases as CaseEntity;
use Dvsa\Olcs\Api\Entity\ContactDetails\Country as CountryEntity;
use Dvsa\Olcs\Api\Entity\Si\SiCategory as SiCategoryEntity;
use Dvsa\Olcs\Api\Entity\Si\SiCategoryType as SiCategoryTypeEntity;
use Dvsa\Olcs\Api\Entity\Si\SeriousInfringement;
use Dvsa\OlcsTest\Api\Entity\Abstracts\EntityTester;
use Dvsa\Olcs\Api\Entity\Si\SeriousInfringement as Entity;
use Dvsa\Olcs\Api\Entity\User\User as UserEntity;
use Dvsa\Olcs\Api\Entity\ContactDetails\Country;
use Mockery as m;

/**
 * SeriousInfringement Entity Unit Tests
 *
 * Initially auto-generated but won't be overridden
 */
class SeriousInfringementEntityTest extends EntityTester
{
    /**
     * Define the entity to test
     *
     * @var string
     */
    protected $entityClass = Entity::class;

    /**
     * Tests creation of a serious infringement
     */
    public function testCreate()
    {
        $case = m::mock(CaseEntity::class);
        $checkDate = new \DateTime('2015-12-25');
        $infringementDate = new \DateTime('2015-12-26');
        $memberStateCode = m::mock(CountryEntity::class);
        $siCategory = m::mock(SiCategoryEntity::class);
        $siCategoryType = m::mock(SiCategoryTypeEntity::class);
        $imposedErrus = new ArrayCollection();
        $requestedErrus = new ArrayCollection();
        $notificationNumber = '0ffefb6b-6344-4a60-9a53-4381c32f98d9';
        $workflowId = '20776dc3-5fe7-42d5-b554-09ad12fa25c4';

        $entity = new SeriousInfringement(
            $case,
            $checkDate,
            $infringementDate,
            $memberStateCode,
            $siCategory,
            $siCategoryType,
            $imposedErrus,
            $requestedErrus,
            $notificationNumber,
            $workflowId
        );

        $this->assertEquals($case, $entity->getCase());
        $this->assertEquals($checkDate, $entity->getCheckDate());
        $this->assertEquals($infringementDate, $entity->getInfringementDate());
        $this->assertEquals($memberStateCode, $entity->getMemberStateCode());
        $this->assertEquals($siCategory, $entity->getSiCategory());
        $this->assertEquals($siCategoryType, $entity->getSiCategoryType());
        $this->assertEquals($imposedErrus, $entity->getImposedErrus());
        $this->assertEquals($requestedErrus, $entity->getRequestedErrus());
        $this->assertEquals($notificationNumber, $entity->getNotificationNumber());
        $this->assertEquals($workflowId, $entity->getWorkflowId());
    }

    /**
     * tests updateErruResponse
     */
    public function testUpdateErruResponse()
    {
        $user = m::mock(UserEntity::class);
        $date = new \DateTime();

        $entity = m::mock(SeriousInfringement::class)->makePartial();

        $entity->updateErruResponse($user, $date);

        $this->assertEquals($user, $entity->getErruResponseUser());
        $this->assertEquals($date, $entity->getErruResponseTime());
        $this->assertEquals('Y', $entity->getErruResponseSent());
    }
}
