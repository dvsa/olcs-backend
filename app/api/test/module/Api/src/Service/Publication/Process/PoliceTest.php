<?php

namespace Dvsa\OlcsTest\Api\Service\Publication\Process;

use Doctrine\Common\Collections\ArrayCollection;
use Dvsa\Olcs\Api\Entity\System\RefData;
use Dvsa\Olcs\Api\Service\Publication\Process\Police;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery as m;
use Dvsa\Olcs\Api\Entity\Publication\PublicationLink;
use Dvsa\Olcs\Api\Entity\Licence\Licence as LicenceEntity;
use Dvsa\Olcs\Api\Entity\Organisation\Organisation;
use Dvsa\Olcs\Api\Entity\Organisation\OrganisationPerson;
use Dvsa\Olcs\Api\Entity\Person\Person as PersonEntity;
use Dvsa\Olcs\Api\Service\Publication\ImmutableArrayObject;
use Dvsa\Olcs\Api\Entity\Publication\PublicationPoliceData;

/**
 * Class PoliceTest
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
class PoliceTest extends MockeryTestCase
{
    /**
     * @group publicationFilter
     *
     * Test the Police filter
     */
    public function testProcess()
    {
        $sut = new Police();

        $birthDate = new \DateTime('2015-12-25 00:00:00');
        $forename = 'forename';
        $familyName = 'family name';

        $initialPoliceData = new PublicationPoliceData(new PublicationLink, new PersonEntity());
        $inputPoliceData = new ArrayCollection([$initialPoliceData]);

        $person = new PersonEntity();
        $person->setBirthDate($birthDate);
        $person->setForename($forename);
        $person->setFamilyName($familyName);

        $organisationPerson = new OrganisationPerson();
        $organisationPerson->setPerson($person);

        $organisationPersons = new ArrayCollection([$organisationPerson]);

        $organisation = new Organisation();
        $organisation->setOrganisationPersons($organisationPersons);

        $licence = new LicenceEntity($organisation, new RefData());

        $publicationLink = new PublicationLink();
        $publicationLink->setLicence($licence);
        $publicationLink->setPoliceDatas($inputPoliceData);
        $expectedPoliceData = new PublicationPoliceData($publicationLink, $person);

        $output = $sut->process($publicationLink, new ImmutableArrayObject());

        //check the first element was removed, and that the second one replaced it
        $this->assertEquals(1, $output->getPoliceDatas()->count());
        $this->assertEquals($expectedPoliceData, $output->getPoliceDatas()->first());
    }
}
