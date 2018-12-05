<?php

namespace Dvsa\OlcsTest\Api\Entity\Publication;

use Dvsa\OlcsTest\Api\Entity\Abstracts\EntityTester;
use Dvsa\Olcs\Api\Entity\Publication\Recipient as Entity;
use Dvsa\Olcs\Api\Domain\Exception;

/**
 * Recipient Entity Unit Tests
 *
 * Initially auto-generated but won't be overridden
 */
class RecipientEntityTest extends EntityTester
{
    /**
     * Define the entity to test
     *
     * @var string
     */
    protected $entityClass = Entity::class;

    public function setUp()
    {
        $this->entity = $this->instantiate($this->entityClass);
    }

    public function testUpdate()
    {
        $isObjector = 'Y';
        $contactName = 'test me';
        $emailAddress = 'test@test.me';
        $sendAppDecision = 'Y';
        $sendNoticesProcs = 'N';

        $this->entity->update($isObjector, $contactName, $emailAddress, $sendAppDecision, $sendNoticesProcs);

        $this->assertEquals($isObjector, $this->entity->getIsObjector());
        $this->assertEquals($contactName, $this->entity->getContactName());
        $this->assertEquals($emailAddress, $this->entity->getEmailAddress());
        $this->assertEquals($sendAppDecision, $this->entity->getSendAppDecision());
        $this->assertEquals($sendNoticesProcs, $this->entity->getSendNoticesProcs());
    }

    public function testUpdateWithInvalidSubscriptionDetails()
    {
        $this->expectException(Exception\ValidationException::class);

        $isObjector = 'Y';
        $contactName = 'test me';
        $emailAddress = 'test@test.me';
        $sendAppDecision = 'N';
        $sendNoticesProcs = 'N';

        $this->entity->update($isObjector, $contactName, $emailAddress, $sendAppDecision, $sendNoticesProcs);
    }
}
