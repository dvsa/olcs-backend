<?php

namespace Dvsa\OlcsTest\Api\Service\Publication\Context\Licence;

use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery as m;
use Dvsa\Olcs\Api\Entity\Publication\PublicationLink;
use Dvsa\Olcs\Api\Entity\System\RefData;
use Dvsa\Olcs\Api\Entity\Person\Person;

/**
 * Class PeopleTest
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class PeopleTest extends MockeryTestCase
{
    /**
     * @var \Dvsa\Olcs\Api\Service\Publication\Context\Application\People
     */
    private $sut;

    public function setUp(): void
    {
        $this->sut = new \Dvsa\Olcs\Api\Service\Publication\Context\Licence\People(
            m::mock(\Dvsa\Olcs\Api\Domain\QueryHandlerManager::class)
        );

        parent::setUp();
    }

    public function testProvide()
    {
        $publicationLink = $this->getPublicationLink();
        $context = new \ArrayObject();

        $person432 = $this->getPerson(432, 'Tom', 'Jones');
        $person12 = $this->getPerson(12, 'Maggy', 'Smith');
        $person64 = $this->getPerson(64, 'John', 'Sheriden');

        $this->addOrganisationPerson($publicationLink, $person432);
        $this->addOrganisationPerson($publicationLink, $person12);
        $this->addOrganisationPerson($publicationLink, $person64);

        $this->sut->provide($publicationLink, $context);

        $this->assertSame(
            [
                'licencePeople' => [
                    432 => $person432,
                    12 => $person12,
                    64 => $person64,
                ]
            ],
            $context->getArrayCopy()
        );
    }

    /**
     * @return PublicationLink
     */
    private function getPublicationLink()
    {
        $publicationLink = new PublicationLink();

        $organisation = new \Dvsa\Olcs\Api\Entity\Organisation\Organisation();

        $licence = new \Dvsa\Olcs\Api\Entity\Licence\Licence($organisation, new RefData());
        $publicationLink->setLicence($licence);

        return $publicationLink;
    }

    /**
     * @param PublicationLink $publicationLink
     * @param Person $person
     */
    private function addOrganisationPerson(PublicationLink $publicationLink, Person $person)
    {
        $organisationPerson = new \Dvsa\Olcs\Api\Entity\Organisation\OrganisationPerson();
        $organisationPerson->setPerson($person);
        $publicationLink->getLicence()->getOrganisation()->addOrganisationPersons($organisationPerson);
    }

    /**
     * @param string $forename
     * @param string $familyName
     *
     * @return \Dvsa\Olcs\Api\Entity\Person\Person
     */
    private function getPerson($id, $forename, $familyName)
    {
        $person = new Person();
        $person->setId($id);
        $person->setForename($forename);
        $person->setFamilyName($familyName);

        return $person;
    }
}
