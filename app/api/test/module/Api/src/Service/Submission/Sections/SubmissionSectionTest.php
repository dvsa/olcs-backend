<?php

namespace Dvsa\OlcsTest\Api\Service\Submission\Sections;

use Dvsa\Olcs\Api\Entity\Application\Application;
use Dvsa\Olcs\Api\Entity\Cases\ConditionUndertaking;
use Dvsa\Olcs\Api\Entity\ContactDetails\Address;
use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Dvsa\Olcs\Api\Entity\OperatingCentre\OperatingCentre;
use Dvsa\Olcs\Api\Entity\Organisation\Organisation;
use Dvsa\Olcs\Api\Entity\Organisation\OrganisationPerson;
use Dvsa\Olcs\Api\Entity\Person\Person;
use Dvsa\Olcs\Api\Entity\TrafficArea\TrafficArea;
use Dvsa\Olcs\Api\Entity\Vehicle\Vehicle;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery as m;
use Dvsa\Olcs\Api\Entity\Cases\Cases as CasesEntity;
use Dvsa\Olcs\Api\Entity\System\RefData;
use Dvsa\Olcs\Api\Domain\QueryHandler\QueryHandlerInterface;
use Doctrine\Common\Collections\ArrayCollection;

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

        $organisation = $this->generateOrganisation();
        $licence = $this->generateLicence($organisation, 7);

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

        $organisationLicences = new ArrayCollection();
        $applications = new ArrayCollection();

        for ($i=1; $i < 3; $i++) {
            $licence = $this->generateLicence($organisation, $i);
            $applications->add(
                $this->generateApplication($i, $licence, Application::APPLICATION_STATUS_GRANTED)
            );
            $applications->add(
                $this->generateApplication((100+$i), $licence, Application::APPLICATION_STATUS_UNDER_CONSIDERATION)
            );

            $licence->setApplications($applications);

            $organisationLicences->add($licence);
        }
        $organisation->setLicences($organisationLicences);

        $organisation->setLeadTcArea($this->generateTrafficArea('B'));

        return $organisation;
    }

    protected function generateLicence(Organisation $organisation, $id = null)
    {
        $licence = new Licence(
            $organisation,
            $this->generateRefDataEntity($this->licenceStatus)
        );
        $licence->setId($id);
        $licence->setVersion($id);
        $licence->setLicenceType($this->generateRefDataEntity($this->licenceType));
        $licence->setGoodsOrPsv($this->generateRefDataEntity($this->goodsOrPsv));
        $licence->setLicNo('OB12345');
        $licence->setTotAuthTrailers(5);

        $licence->setLicenceVehicles($this->generateLicenceVehicles($licence));

        $licence->setOperatingCentres($this->generateOperatingCentres($licence));

        $licence->setApplications($this->generateApplications($licence));

        $licence->setConditionUndertakings(
            $this->generateConditionsUndertakings(
                $licence,
                ConditionUndertaking::TYPE_CONDITION,
                58
            )
        );

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

    protected function generateConditionsUndertakings($parentEntity, $conditionType, $id = 1)
    {
        $conditionUndertakings = new ArrayCollection();

        $cu = new ConditionUndertaking(
            $this->generateRefDataEntity($conditionType),
            'Y',
            'N'
        );
        if ($parentEntity instanceof Licence) {
            $cu->setAddedVia($this->generateRefDataEntity(ConditionUndertaking::ADDED_VIA_LICENCE));
            $cu->setAttachedTo($this->generateRefDataEntity(ConditionUndertaking::ATTACHED_TO_LICENCE));
        } elseif ($parentEntity instanceof Application) {
            $cu->setAddedVia($this->generateRefDataEntity(ConditionUndertaking::ADDED_VIA_APPLICATION));
            $cu->setAttachedTo($this->generateRefDataEntity(ConditionUndertaking::ATTACHED_TO_OPERATING_CENTRE));
            $cu->setOperatingCentre($this->generateOperatingCentre());
        }

        $cu->setId($id);
        $cu->setVersion((100+$id));
        $cu->setCreatedOn(new \DateTime('2011-01-23'));
        $cu->setAddedVia($this->generateRefDataEntity(ConditionUndertaking::ADDED_VIA_LICENCE));
        $cu->setAttachedTo($this->generateRefDataEntity(ConditionUndertaking::ATTACHED_TO_LICENCE));

        $conditionUndertakings->add($cu);

        return $conditionUndertakings;
    }

    protected function generateOperatingCentres($licence)
    {
        $operatingCentres = new ArrayCollection();

        for ($i=1; $i < 2; $i++) {
            $operatingCentre = $this->generateOperatingCentre($i);
            $loc = new \Dvsa\Olcs\Api\Entity\Licence\LicenceOperatingCentre($licence, $operatingCentre);
            $loc->setNoOfVehiclesRequired(6);
            $loc->setNoOfTrailersRequired(4);
            $operatingCentres->add($loc);
        }

        return $operatingCentres;
    }

    protected function generateOperatingCentre($i = 1)
    {
        $operatingCentre = new OperatingCentre();
        $operatingCentre->setId($i);
        $operatingCentre->setVersion($i);

        $address = $this->generateAddress($i);
        $operatingCentre->setAddress($address);
        return $operatingCentre;
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

    protected function generateApplications(Licence $licence)
    {
        $applications = new ArrayCollection();
        $grantedApp = $this->generateApplication(63, $licence, Application::APPLICATION_STATUS_GRANTED);

        $grantedApp->setConditionUndertakings(
            $this->generateConditionsUndertakings(
                $grantedApp,
                ConditionUndertaking::TYPE_UNDERTAKING,
                88
            )
        );
        $applications->add($grantedApp);

        $applications->add(
            $this->generateApplication(75, $licence, Application::APPLICATION_STATUS_NOT_SUBMITTED)
        );

        $applications->add(
            $this->generateApplication(75, $licence, Application::APPLICATION_STATUS_REFUSED)
        );

        $applications->add(
            $this->generateApplication(75, $licence, Application::APPLICATION_STATUS_GRANTED, true)
        );

        return $applications;
    }

    protected function generateApplication($id, Licence $licence, $status, $isVariation = false)
    {
        $application = new Application(
            $licence,
            $this->generateRefDataEntity($status),
            $isVariation
        );
        $application->setId($id);
        $application->setVersion(($id*2));
        $application->setReceivedDate(new \DateTime('2014-05-05'));

        $application->setConditionUndertakings(
            $this->generateConditionsUndertakings(
                $application,
                ConditionUndertaking::TYPE_UNDERTAKING,
                34
            )
        );

        return $application;
    }

    protected function generateTrafficArea($id)
    {
        $ta = new TrafficArea();

        $ta->setId($id);
        $ta->setName('FOO');

        return $ta;
    }
}
