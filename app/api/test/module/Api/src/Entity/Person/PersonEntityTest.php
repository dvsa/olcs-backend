<?php

namespace Dvsa\OlcsTest\Api\Entity\Person;

use Dvsa\OlcsTest\Api\Entity\Abstracts\EntityTester;
use Dvsa\Olcs\Api\Entity\Person\Person as Entity;
use Dvsa\Olcs\Api\Entity\System\RefData;
use Dvsa\Olcs\Api\Entity\Organisation\Disqualification;
use Dvsa\Olcs\Api\Entity\Organisation\Organisation;
use Mockery as m;

/**
 * Person Entity Unit Tests
 *
 * Initially auto-generated but won't be overridden
 */
class PersonEntityTest extends EntityTester
{
    /**
     * Define the entity to test
     *
     * @var string
     */
    protected $entityClass = Entity::class;

    public function testUpdatePerson()
    {
        $entity = new Entity();
        $title = m::mock(RefData::class);

        $entity->updatePerson('forename', 'familyname', $title, '2015-01-01', 'bplace');

        $this->assertSame($title, $entity->getTitle());
        $this->assertEquals('forename', $entity->getForename());
        $this->assertEquals('familyname', $entity->getFamilyName());
        $this->assertEquals(new \DateTime('2015-01-01'), $entity->getBirthDate());
        $this->assertEquals('bplace', $entity->getBirthPlace());
    }

    public function testGetContactDetail()
    {
        $contactDetails = new \Dvsa\Olcs\Api\Entity\ContactDetails\ContactDetails(new RefData());

        $person = new Entity();
        $person->addContactDetails($contactDetails);

        $this->assertSame($contactDetails, $person->getContactDetail());
    }

    public function testGetCalculatedValues()
    {
        $person = m::mock(Entity::class)->makePartial()->shouldAllowMockingProtectedMethods();
        $person->shouldReceive('getDisqualificationStatus')->with()->once()->andReturn('STATUS');

        $this->assertSame(['disqualificationStatus' => 'STATUS'], $person->getCalculatedBundleValues());
    }

    public function testGetFullName()
    {
        $person = new Entity();
        $person->setForename('Foo');
        $person->setFamilyName('Bar');

        $this->assertSame('Foo Bar', $person->getFullName());

    }

    public function testGetDisqualificationNull()
    {
        /* @var $organisation Entity */
        $person = $this->instantiate($this->entityClass);
        $person->setDisqualifications(new \Doctrine\Common\Collections\ArrayCollection());

        $this->assertSame(null, $person->getDisqualification());
    }

    public function testGetDisqualification()
    {
        $disqualification = new Disqualification(m::mock(Organisation::class));

        /* @var $person Entity */
        $person = $this->instantiate($this->entityClass);
        $person->setDisqualifications(new \Doctrine\Common\Collections\ArrayCollection([$disqualification]));

        $this->assertSame($disqualification, $person->getDisqualification());
    }

    public function testGetDisqualificationStatusNone()
    {
        /* @var $person Entity */
        $person = $this->instantiate($this->entityClass);
        $person->setDisqualifications(new \Doctrine\Common\Collections\ArrayCollection());

        $this->assertSame(Disqualification::STATUS_NONE, $person->getDisqualificationStatus());
    }

    public function testGetDisqualificationStatusActive()
    {
        $disqualification = new Disqualification(m::mock(Organisation::class));
        $disqualification->setIsDisqualified('Y');
        $disqualification->setStartDate('2015-01-01');

        /* @var $person Entity */
        $person = $this->instantiate($this->entityClass);
        $person->setDisqualifications(new \Doctrine\Common\Collections\ArrayCollection([$disqualification]));

        $this->assertSame(Disqualification::STATUS_ACTIVE, $person->getDisqualificationStatus());
    }
}
