<?php

namespace Dvsa\OlcsTest\Api\Service\Submission\Sections;

use Dvsa\Olcs\Api\Entity\ContactDetails\Address;
use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Dvsa\Olcs\Api\Entity\OperatingCentre\OperatingCentre;
use Dvsa\Olcs\Api\Entity\Organisation\Organisation;
use Dvsa\Olcs\Api\Entity\Organisation\OrganisationPerson;
use Dvsa\Olcs\Api\Entity\Person\Person;
use Dvsa\Olcs\Api\Entity\Vehicle\Vehicle;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery as m;
use Dvsa\Olcs\Api\Entity\Cases\Cases as CasesEntity;
use Dvsa\Olcs\Api\Entity\System\RefData;
use Dvsa\Olcs\Api\Domain\QueryHandler\QueryHandlerInterface;
use Doctrine\Common\Collections\ArrayCollection;
use OlcsEntities\Entity\LicenceVehicle;

/**
 * Class SubmissionSectionTest
 * @author Shaun Lizzio <shaun@valtech.co.uk>
 */
class SubmissionSectionTest extends MockeryTestCase
{
    protected $submissionSection = '';
    protected $licenceStatus = 'lic_status';
    protected $organisationType = 'org_type';
    protected $licenceType = 'lic_type';
    protected $goodsOrPsv = 'goods';
    protected $natureOfBusiness = 'nob1';

    /**
     * @dataProvider sectionTestProvider
     *
     * @param $section
     * @param $expectedString
     */
    public function testGenerateSection($input = null, $expectedResult = null)
    {
        if (!empty($input)) {
            $sut = new $this->submissionSection(m::mock(QueryHandlerInterface::class));

            $this->assertEquals($expectedResult, $sut->generateSection($input));
        } else {
            $this->markTestSkipped();
        }
    }

    /**
     * Filter provider
     *
     * @return array
     */
    public function sectionTestProvider()
    {
        return [];
    }

    protected function getCase()
    {
        $openDate = new \DateTime('2012-01-01 15:00:00');
        $caseType = new RefData('case_type');
        $caseType->setDescription('case type 1');

        $categorys = new ArrayCollection(['cat1', 'cat2']);
        $outcomes = new ArrayCollection(['out1']);

        $licence = $this->generateLicence();

        $application = null;

        $ecmsNo = 'ecms1234';
        $description = 'case description';
        $transportManager = null;

        $case = new CasesEntity(
            $openDate, $caseType, $categorys, $outcomes, $application, $licence, $transportManager, $ecmsNo,
            $description
        );

        $case->setId(99);

        return $case;
    }

    protected function generateRefDataEntity($id, $description = 'desc')
    {
        $refData = new RefData($id);
        $refData->setDescription($id . '-' . $description);

        return $refData;
    }

    protected function generatePerson($id)
    {
        $person = new Person($id);
        $person->setId($id);
        $person->setTitle($this->generateRefDataEntity('title'));
        $person->setForename('fn' . $id);
        $person->setFamilyName('sn' . $id);
        $person->setBirthDate(new \DateTime('1977-01-' . $id));

        return $person;
    }

    protected function generateOrganisation()
    {
        $organisation = new Organisation();
        $organisationType = $this->generateRefDataEntity($this->organisationType);
        $organisation->setType($organisationType);
        $organisation->setName('Org name');
        $organisation->setNatureOfBusiness($this->natureOfBusiness);

        $organisationPersons = new ArrayCollection();

        $organisationPerson = new OrganisationPerson();

        $organisationPerson->setPerson($this->generatePerson(1));
        $organisationPersons->add($organisationPerson);

        $organisation->setOrganisationPersons($organisationPersons);

        return $organisation;
    }

    protected function generateLicence()
    {
        $licence = new Licence(
            $this->generateOrganisation(),
            $this->generateRefDataEntity($this->licenceStatus)
        );
        $licence->setLicenceType($this->generateRefDataEntity($this->licenceType));
        $licence->setGoodsOrPsv($this->generateRefDataEntity($this->goodsOrPsv));
        $licence->setLicNo('OB12345');
        $licence->setTotAuthTrailers(5);

        $licence->setLicenceVehicles($this->generateLicenceVehicles($licence));

        $licence->setOperatingCentres($this->generateOperatingCentres($licence));

        return $licence;
    }

    protected function generateLicenceVehicles($licence)
    {
        $licenceVehicles = new ArrayCollection();
        $vehicle = new Vehicle();

        $lv = new \Dvsa\Olcs\Api\Entity\Licence\LicenceVehicle($licence, $vehicle);
        $lv->setSpecifiedDate(new \DateTime('2000-01-01'));

        $licenceVehicles->add($lv);
        $licenceVehicles->add($lv);
        $licenceVehicles->add($lv);

        return $licenceVehicles;
    }

    protected function generateOperatingCentres($licence)
    {
        $operatingCentres = new ArrayCollection();

        for ($i=1; $i < 2; $i++) {
            $operatingCentre = new OperatingCentre();
            $operatingCentre->setId($i);
            $operatingCentre->setVersion($i);
            $loc = new \Dvsa\Olcs\Api\Entity\Licence\LicenceOperatingCentre($licence, $operatingCentre);
            $loc->setNoOfVehiclesRequired(6);
            $loc->setNoOfTrailersRequired(4);

            $address = $this->generateAddress($i);
            $operatingCentre->setAddress($address);
            $operatingCentres->add($loc);
        }

        return $operatingCentres;
    }

    protected function generateAddress($id)
    {
        $address = new Address($id);
        $address->setId($id);
        $address->setAddressLine1($id . '_a1');
        $address->setAddressLine2($id . '_a2');
        $address->setAddressLine3($id . '_a3');
        $address->setTown($id . 't');
        $address->setPostcode('pc' . $id . '1PC');

        return $address;
    }

}
