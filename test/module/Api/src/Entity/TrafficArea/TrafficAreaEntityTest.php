<?php

namespace Dvsa\OlcsTest\Api\Entity\TrafficArea;

use Doctrine\Common\Collections\ArrayCollection;
use Dvsa\OlcsTest\Api\Entity\Abstracts\EntityTester;
use Dvsa\Olcs\Api\Entity\TrafficArea\TrafficArea as Entity;
use Dvsa\Olcs\Api\Entity\Publication\Recipient as RecipientEntity;
use Mockery as m;

/**
 * TrafficArea Entity Unit Tests
 *
 * Initially auto-generated but won't be overridden
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
class TrafficAreaEntityTest extends EntityTester
{
    /**
     * Define the entity to test
     *
     * @var string
     */
    protected $entityClass = Entity::class;

    const PUB_RECIPIENT_NAME1 = 'name 1';
    const PUB_RECIPIENT_NAME2 = 'name 2';
    const PUB_RECIPIENT_NAME3 = 'name 3';
    const PUB_RECIPIENT_NAME4 = 'name 4';
    const PUB_RECIPIENT_EMAIL1 = 'email1@foo.bar';
    const PUB_RECIPIENT_EMAIL2 = 'email2@foo.bar';
    const PUB_RECIPIENT_EMAIL3 = 'email3@foo.bar';
    const PUB_RECIPIENT_EMAIL4 = 'email4@foo.bar';

    /**
     * Test getPublicationRecipients
     *
     * @dataProvider publicationRecipientsProvider
     *
     * @param $isPolice
     * @param $policeTimes
     * @param $nonPoliceTimes
     * @param $expectedRecipients
     */
    public function testGetPublicationRecipients($isPolice, $policeTimes, $nonPoliceTimes, $expectedRecipients)
    {
        $entity = new Entity();

        $recipient1 = m::mock(RecipientEntity::class);
        $recipient1->shouldReceive('getEmailAddress')->times($nonPoliceTimes)->andReturn(self::PUB_RECIPIENT_EMAIL1);
        $recipient1->shouldReceive('getContactName')->times($nonPoliceTimes)->andReturn(self::PUB_RECIPIENT_NAME1);
        $recipient1->shouldReceive('getIsPolice')->once()->andReturn('N');

        $recipient2 = m::mock(RecipientEntity::class);
        $recipient2->shouldReceive('getEmailAddress')->times($policeTimes)->andReturn(self::PUB_RECIPIENT_EMAIL2);
        $recipient2->shouldReceive('getContactName')->times($policeTimes)->andReturn(self::PUB_RECIPIENT_NAME2);
        $recipient2->shouldReceive('getIsPolice')->once()->andReturn('Y');

        $recipient3 = m::mock(RecipientEntity::class);
        $recipient3->shouldReceive('getEmailAddress')->times($nonPoliceTimes)->andReturn(self::PUB_RECIPIENT_EMAIL3);
        $recipient3->shouldReceive('getContactName')->times($nonPoliceTimes)->andReturn(self::PUB_RECIPIENT_NAME3);
        $recipient3->shouldReceive('getIsPolice')->once()->andReturn('N');

        $recipient4 = m::mock(RecipientEntity::class);
        $recipient4->shouldReceive('getEmailAddress')->times($policeTimes)->andReturn(self::PUB_RECIPIENT_EMAIL4);
        $recipient4->shouldReceive('getContactName')->times($policeTimes)->andReturn(self::PUB_RECIPIENT_NAME4);
        $recipient4->shouldReceive('getIsPolice')->once()->andReturn('Y');

        $recipientCollection = new ArrayCollection([$recipient1, $recipient2, $recipient3, $recipient4]);
        $entity->setRecipients($recipientCollection);

        $this->assertEquals($expectedRecipients, $entity->getPublicationRecipients($isPolice));
    }

    /**
     * Data provider for testGetPublicationRecipients
     *
     * @return array
     */
    public function publicationRecipientsProvider()
    {
        $policeRecipients = [
            self::PUB_RECIPIENT_EMAIL2 => self::PUB_RECIPIENT_NAME2,
            self::PUB_RECIPIENT_EMAIL4 => self::PUB_RECIPIENT_NAME4
        ];

        $nonPoliceRecipients = [
            self::PUB_RECIPIENT_EMAIL1 => self::PUB_RECIPIENT_NAME1,
            self::PUB_RECIPIENT_EMAIL3 => self::PUB_RECIPIENT_NAME3
        ];

        return [
            ['Y', 1, 0, $policeRecipients],
            ['N', 0, 1, $nonPoliceRecipients]
        ];
    }
}
