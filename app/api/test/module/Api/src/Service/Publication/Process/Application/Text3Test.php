<?php

namespace Dvsa\OlcsTest\Api\Service\Publication\Process\Application;

use Dvsa\Olcs\Api\Service\Publication\Process\Application\Text3 as ApplicationText3;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery as m;
use Dvsa\Olcs\Api\Entity\Publication\PublicationLink;
use Dvsa\Olcs\Api\Entity\Publication\PublicationSection;
use Dvsa\Olcs\Api\Entity\Organisation\Organisation;
use Dvsa\Olcs\Api\Entity\System\RefData;
use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Dvsa\Olcs\Api\Service\Publication\ImmutableArrayObject;

/**
 * Class Text3Test
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
class Text3Test extends MockeryTestCase
{
    /**
     * @dataProvider dataProviderTestProcessSection
     */
    public function testProcessSection($expectTextSet, $sectionId)
    {
        $sut = new ApplicationText3();

        $organisation = new Organisation();
        $licence = new Licence($organisation, new RefData());
        $application = new \Dvsa\Olcs\Api\Entity\Application\Application($licence, new RefData(), false);

        $publicationSection = new PublicationSection();
        $publicationSection->setId($sectionId);

        $publicationLink = new PublicationLink();
        $publicationLink->setApplication($application);
        $publicationLink->setPublicationSection($publicationSection);

        $input = [
            'licenceAddress' => 'LICENCE_ADDRESS',
            'conditionUndertaking' => [],
        ];

        $sut->process($publicationLink, new ImmutableArrayObject($input));

        if ($expectTextSet) {
            $this->assertSame('LICENCE_ADDRESS', $publicationLink->getText3());
        } else {
            $this->assertNull($publicationLink->getText3());
        }
    }

    public function dataProviderTestProcessSection()
    {
        return [
            [true, PublicationSection::APP_NEW_SECTION],
            [true, PublicationSection::APP_GRANTED_SECTION],
            [false, PublicationSection::APP_GRANT_NOT_TAKEN_SECTION],
            [false, PublicationSection::APP_REFUSED_SECTION],
            [false, PublicationSection::APP_WITHDRAWN_SECTION],
        ];
    }

    public function testProcessOc()
    {
        $sut = new ApplicationText3();

        $organisation = new Organisation();
        $licence = new Licence($organisation, new RefData());
        $application = new \Dvsa\Olcs\Api\Entity\Application\Application($licence, new RefData(), false);

        $address = new \Dvsa\Olcs\Api\Entity\ContactDetails\Address();
        $address->setAddressLine1('ADDRESS1');
        $oc = new \Dvsa\Olcs\Api\Entity\OperatingCentre\OperatingCentre();
        $oc->setAddress($address);
        $aoc = new \Dvsa\Olcs\Api\Entity\Application\ApplicationOperatingCentre($application, $oc);
        $aoc->setNoOfVehiclesRequired(5);
        $aoc->setNoOfTrailersRequired(7);
        $application->addOperatingCentres($aoc);

        $publicationSection = new PublicationSection();
        $publicationSection->setId(PublicationSection::APP_GRANTED_SECTION);

        $publicationLink = new PublicationLink();
        $publicationLink->setApplication($application);
        $publicationLink->setPublicationSection($publicationSection);

        $input = [
            'licenceAddress' => 'LICENCE_ADDRESS',
            'conditionUndertaking' => [],
        ];

        $sut->process($publicationLink, new ImmutableArrayObject($input));

        $this->assertSame(
            "LICENCE_ADDRESS\nOperating Centre: ADDRESS1\nAuthorisation: 5 vehicle(s), 7 trailer(s)",
            $publicationLink->getText3()
        );
    }

    public function testProcessOcS4Ignored()
    {
        $sut = new ApplicationText3();

        $organisation = new Organisation();
        $licence = new Licence($organisation, new RefData());
        $application = new \Dvsa\Olcs\Api\Entity\Application\Application($licence, new RefData(), false);

        $address = new \Dvsa\Olcs\Api\Entity\ContactDetails\Address();
        $address->setAddressLine1('ADDRESS1');
        $oc = new \Dvsa\Olcs\Api\Entity\OperatingCentre\OperatingCentre();
        $oc->setAddress($address);
        $aoc = new \Dvsa\Olcs\Api\Entity\Application\ApplicationOperatingCentre($application, $oc);
        $aoc->setNoOfVehiclesRequired(5);
        $aoc->setNoOfTrailersRequired(7);
        $aoc->setS4(m::mock(\Dvsa\Olcs\Api\Entity\Application\S4::class));
        $application->addOperatingCentres($aoc);

        $publicationSection = new PublicationSection();
        $publicationSection->setId(PublicationSection::APP_NEW_SECTION);

        $publicationLink = new PublicationLink();
        $publicationLink->setApplication($application);
        $publicationLink->setPublicationSection($publicationSection);

        $input = [
            'licenceAddress' => 'LICENCE_ADDRESS',
            'conditionUndertaking' => [],
        ];

        $sut->process($publicationLink, new ImmutableArrayObject($input));

        $this->assertSame(
            "LICENCE_ADDRESS",
            $publicationLink->getText3()
        );
    }

    public function testProcessTransportManagers()
    {
        $sut = new ApplicationText3();

        $organisation = new Organisation();
        $licence = new Licence($organisation, new RefData());
        $application = new \Dvsa\Olcs\Api\Entity\Application\Application($licence, new RefData(), false);

        $tm1 = new \Dvsa\Olcs\Api\Entity\Tm\TransportManager();
        $tm1->setHomeCd(new \Dvsa\Olcs\Api\Entity\ContactDetails\ContactDetails(new RefData()));
        $tm1->getHomeCd()->setPerson(new \Dvsa\Olcs\Api\Entity\Person\Person());
        $tm1->getHomeCd()->getPerson()->setForename('Mandy')->setFamilyName('Smith');

        $publicationSection = new PublicationSection();
        $publicationSection->setId(PublicationSection::APP_GRANTED_SECTION);

        $publicationLink = new PublicationLink();
        $publicationLink->setApplication($application);
        $publicationLink->setPublicationSection($publicationSection);

        $input = [
            'licenceAddress' => 'LICENCE_ADDRESS',
            'conditionUndertaking' => [],
            'applicationTransportManagers' => [$tm1],
        ];

        $sut->process($publicationLink, new ImmutableArrayObject($input));

        $this->assertSame(
            "LICENCE_ADDRESS\nTransport Manager(s): Mandy Smith",
            $publicationLink->getText3()
        );
    }

    public function testProcessConditionsAndUndertakings()
    {
        $sut = new ApplicationText3();

        $organisation = new Organisation();
        $licence = new Licence($organisation, new RefData());
        $application = new \Dvsa\Olcs\Api\Entity\Application\Application($licence, new RefData(), false);

        $tm1 = new \Dvsa\Olcs\Api\Entity\Tm\TransportManager();
        $tm1->setHomeCd(new \Dvsa\Olcs\Api\Entity\ContactDetails\ContactDetails(new RefData()));
        $tm1->getHomeCd()->setPerson(new \Dvsa\Olcs\Api\Entity\Person\Person());
        $tm1->getHomeCd()->getPerson()->setForename('Mandy')->setFamilyName('Smith');

        $publicationSection = new PublicationSection();
        $publicationSection->setId(PublicationSection::APP_GRANTED_SECTION);

        $publicationLink = new PublicationLink();
        $publicationLink->setApplication($application);
        $publicationLink->setPublicationSection($publicationSection);

        $input = [
            'licenceAddress' => 'LICENCE_ADDRESS',
            'conditionUndertaking' => ['CU_LINE1', 'CU_LINE2'],
        ];

        $sut->process($publicationLink, new ImmutableArrayObject($input));

        $this->assertSame(
            "LICENCE_ADDRESS\nCU_LINE1\nCU_LINE2",
            $publicationLink->getText3()
        );
    }
}
