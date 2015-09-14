<?php

namespace Dvsa\OlcsTest\Api\Service\Publication\Process;

use Doctrine\Common\Collections\ArrayCollection;
use Dvsa\Olcs\Api\Service\Publication\Process\Police;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery as m;
use Dvsa\Olcs\Api\Entity\Publication\PublicationLink;
use Dvsa\Olcs\Api\Entity\Licence\Licence as LicenceEntity;
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

        $personMock = m::mock(PersonEntity::class);
        $personMock->shouldReceive('getBirthDate')->andReturn($birthDate);
        $personMock->shouldReceive('getForename')->andReturn($forename);
        $personMock->shouldReceive('getFamilyName')->andReturn($familyName);

        $organisationPersonMock = m::mock(OrganisationPerson::class);
        $organisationPersonMock->shouldReceive('getPerson')->andReturn($personMock);

        $organisationPersons = new ArrayCollection([$organisationPersonMock]);

        $licenceMock = m::mock(LicenceEntity::class);
        $licenceMock->shouldReceive('getOrganisation->getOrganisationPersons')
            ->once()
            ->andReturn($organisationPersons);

        $publicationLink = m::mock(PublicationLink::class)->makePartial();
        $publicationLink->shouldReceive('getLicence')->andReturn($licenceMock);

        $expectedPoliceData = new PublicationPoliceData($publicationLink, $birthDate, $forename, $familyName);

        $output = $sut->process($publicationLink, new ImmutableArrayObject());

        $this->assertEquals($expectedPoliceData, $output->getPoliceDatas()[0]);
    }
}
