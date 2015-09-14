<?php

namespace Dvsa\OlcsTest\Api\Entity\Publication;

use Dvsa\Olcs\Api\Entity\Publication\PublicationLink;
use Dvsa\OlcsTest\Api\Entity\Abstracts\EntityTester;
use Dvsa\Olcs\Api\Entity\Publication\PublicationPoliceData as Entity;
use Mockery as m;

/**
 * PublicationPoliceData Entity Unit Tests
 *
 * Initially auto-generated but won't be overridden
 */
class PublicationPoliceDataEntityTest extends EntityTester
{
    public function setUp()
    {
        /** @var \Dvsa\Olcs\Api\Entity\Publication\PublicationPoliceData entity */
        $this->entity = $this->instantiate($this->entityClass);
    }

    /**
     * Define the entity to test
     *
     * @var string
     */
    protected $entityClass = Entity::class;

    /**
     * Tests create
     *
     * @dataProvider birthDateProvider
     */
    public function testCreate($birthDate)
    {
        $publicationLink = m::mock(PublicationLink::class);
        $forename = 'forename';
        $familyName = 'family name';

        $sut = new Entity(
            $publicationLink,
            $birthDate,
            $forename,
            $familyName
        );

        $this->assertEquals($publicationLink, $sut->getPublicationLink());
        $this->assertEquals($birthDate, $sut->getBirthDate());
        $this->assertEquals($forename, $sut->getForename());
        $this->assertEquals($familyName, $sut->getFamilyName());
    }

    public function birthDateProvider()
    {
        return [
            [null],
            [new \DateTime()]
        ];
    }
}
