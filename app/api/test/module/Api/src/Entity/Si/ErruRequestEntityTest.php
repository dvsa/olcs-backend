<?php

namespace Dvsa\OlcsTest\Api\Entity\Si;

use Dvsa\Olcs\Api\Entity\Doc\Document;
use Dvsa\OlcsTest\Api\Entity\Abstracts\EntityTester;
use Dvsa\Olcs\Api\Entity\Si\ErruRequest as Entity;
use Dvsa\Olcs\Api\Entity\ContactDetails\Country as CountryEntity;
use Dvsa\Olcs\Api\Entity\Cases\Cases as CaseEntity;
use Dvsa\Olcs\Api\Entity\User\User as UserEntity;
use Dvsa\Olcs\Api\Entity\System\RefData;
use Mockery as m;

/**
 * ErruRequest Entity Unit Tests
 *
 * Initially auto-generated but won't be overridden
 */
class ErruRequestEntityTest extends EntityTester
{
    /**
     * Define the entity to test
     *
     * @var string
     */
    protected $entityClass = Entity::class;

    /**
     * Tests creation of erru requests
     */
    public function testCreate()
    {
        $case = m::mock(CaseEntity::class);
        $msiType = m::mock(RefData::class);
        $memberStateCode = m::mock(CountryEntity::class);
        $originatingAuthority = 'originating authority';
        $transportUndertakingName = 'transport undertaking';
        $vrm = 'vrm';
        $notificationNumber = '0ffefb6b-6344-4a60-9a53-4381c32f98d9';
        $workflowId = '20776dc3-5fe7-42d5-b554-09ad12fa25c4';

        $entity = new Entity(
            $case,
            $msiType,
            $memberStateCode,
            $originatingAuthority,
            $transportUndertakingName,
            $vrm,
            $notificationNumber,
            $workflowId
        );

        $this->assertEquals($case, $entity->getCase());
        $this->assertEquals($msiType, $entity->getMsiType());
        $this->assertEquals($memberStateCode, $entity->getMemberStateCode());
        $this->assertEquals($originatingAuthority, $entity->getOriginatingAuthority());
        $this->assertEquals($transportUndertakingName, $entity->getTransportUndertakingName());
        $this->assertEquals($vrm, $entity->getVrm());
        $this->assertEquals($notificationNumber, $entity->getNotificationNumber());
        $this->assertEquals($workflowId, $entity->getWorkflowId());
    }

    /**
     * tests queueErruResponse
     */
    public function testQueueErruResponse()
    {
        $user = m::mock(UserEntity::class);
        $date = new \DateTime();
        $document = m::mock(Document::class);
        $msiType = m::mock(RefData::class);

        /** @var Entity $entity */
        $entity = m::mock(Entity::class)->makePartial();

        $entity->queueErruResponse($user, $date, $document, $msiType);

        $this->assertEquals($user, $entity->getResponseUser());
        $this->assertEquals($date, $entity->getResponseTime());
        $this->assertEquals($document, $entity->getResponseDocument());
        $this->assertEquals($msiType, $entity->getMsiType());
    }
}
