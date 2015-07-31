<?php

namespace Dvsa\OlcsTest\Api\Service\Publication\Process\Application;

use Dvsa\Olcs\Api\Service\Publication\Process\Application\Text2 as ApplicationText2;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery as m;
use Dvsa\Olcs\Api\Entity\Publication\PublicationLink;
use Dvsa\Olcs\Api\Entity\Licence\Licence as LicenceEntity;
use Dvsa\Olcs\Api\Entity\Publication\PublicationSection as PublicationSectionEntity;
use Dvsa\Olcs\Api\Entity\Organisation\Organisation as OrganisationEntity;
use Dvsa\Olcs\Api\Entity\Organisation\TradingName as TradingNameEntity;
use Dvsa\Olcs\Api\Entity\Organisation\OrganisationPerson as OrganisationPersonEntity;
use Dvsa\Olcs\Api\Entity\Person\Person as PersonEntity;
use Doctrine\Common\Collections\ArrayCollection;
use Dvsa\Olcs\Api\Service\Publication\ImmutableArrayObject;

/**
 * Class Text2Test
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
class Text2Test extends MockeryTestCase
{
    /**
     * @dataProvider processCallsCancelledProvider
     *
     * @param $licType
     * @param $publicationSection
     * @param $calledFunction
     *
     * @group publicationFilter
     *
     * Test the application bus note filter
     */
    public function testProcessCallsCancelled($licType, $publicationSection, $calledFunction)
    {
        $sut = new ApplicationText2();

        $publicationLink = $this->getProcessInput($licType, $publicationSection);
        $context = new ImmutableArrayObject(['licenceCancelled' => 'licence cancelled']);
        $output = $this->getProcessResult($publicationLink, $context);

        $this->assertEquals(
            implode("\n", $sut->$calledFunction($publicationLink, $context, [])),
            $output->getText2()
        );
    }

    /**
     * @dataProvider processCallsGetLicenceInfoProvider
     *
     * @group publicationFilter
     *
     * @param $licType
     * @param $publicationSection
     */
    public function testProcessCallsGetLicenceInfo($licType, $publicationSection)
    {
        $sut = new ApplicationText2();

        $publicationLink = $this->getProcessInput($licType, $publicationSection);
        $context = new ImmutableArrayObject();

        $output = $this->getProcessResult($publicationLink, $context);
        $this->assertEquals($sut->getLicenceInfo($publicationLink->getLicence()), $output->getText2());
    }

    /**
     * @dataProvider processCallsGetAllDataProvider
     *
     * @group publicationFilter
     *
     * @param $licType
     * @param $publicationSection
     */
    public function testProcessCallsGetAllData($licType, $publicationSection)
    {
        $sut = new ApplicationText2();

        $publicationLink = $this->getProcessInput($licType, $publicationSection);
        $context = new ImmutableArrayObject();

        $output = $this->getProcessResult($publicationLink, $context);
        $this->assertEquals(
            implode(
                "\n",
                $sut->getAllData($publicationLink, [])
            ),
            $output->getText2()
        );
    }

    /**
     * Gets the publication object
     *
     * @param $licenceType
     * @param $publicationSection
     * @return PublicationLink
     */
    public function getProcessInput($licenceType, $publicationSection)
    {
        $licNo = 'OB1234567';
        $organisationName = 'Organisation Name';
        $organisationTradingName = 'Organisation Trading Name';
        $organisationTradingName2 = 'Organisation Trading Name 2';
        $organisationType = 'org_t_rc';
        $personForename = 'John';
        $personFamilyName = 'Smith';

        $tradingName = m::mock(TradingNameEntity::class);
        $tradingName->shouldReceive('getName')->andReturn($organisationTradingName);
        $tradingName2 = m::mock(TradingNameEntity::class);
        $tradingName2->shouldReceive('getName')->andReturn($organisationTradingName2);
        $tradingNames = new ArrayCollection([$tradingName, $tradingName2]);

        $personMock = m::mock(PersonEntity::class);
        $personMock->shouldReceive('getForename')->andReturn($personForename);
        $personMock->shouldReceive('getFamilyName')->andReturn($personFamilyName);

        $organisationPerson = m::mock(OrganisationPersonEntity::class);
        $organisationPerson->shouldReceive('getPerson')->andReturn($personMock);
        $organisationPersons = new ArrayCollection([$organisationPerson]);

        $organisationMock = m::mock(OrganisationEntity::class);
        $organisationMock->shouldReceive('getName')->andReturn($organisationName);
        $organisationMock->shouldReceive('getTradingNames')->andReturn($tradingNames);
        $organisationMock->shouldReceive('getOrganisationPersons')->andReturn($organisationPersons);
        $organisationMock->shouldReceive('getType->getId')->andReturn($organisationType);

        $licenceMock = m::mock(LicenceEntity::class);
        $licenceMock->shouldReceive('getOrganisation')->andReturn($organisationMock);
        $licenceMock->shouldReceive('getLicNo')->andReturn($licNo);
        //$licenceMock->shouldReceive('getLicenceType->getOlbsKey')->andReturn($licenceType);

        $publicationLink = m::mock(PublicationLink::class)->makePartial();
        $publicationLink->shouldReceive('getLicence')->andReturn($licenceMock);
        $publicationLink->shouldReceive('getLicence->getOrganisation')->andReturn($organisationMock);
        $publicationLink->shouldReceive('getPublicationSection->getId')->andReturn($publicationSection);
        $publicationLink->shouldReceive('getApplication->getGoodsOrPsv->getId')->andReturn($licenceType);

        return $publicationLink;
    }

    /**
     * Gets the result from a filter
     *
     * @param PublicationLink $publicationLink
     * @param ImmutableArrayObject $context
     * @return PublicationLink
     */
    public function getProcessResult(PublicationLink $publicationLink, ImmutableArrayObject $context)
    {
        $sut = new ApplicationText2();
        return $sut->process($publicationLink, $context);
    }

    /**
     * Filter provider
     *
     * @return array
     */
    public function processCallsCancelledProvider()
    {
        return [
            [
                LicenceEntity::LICENCE_CATEGORY_GOODS_VEHICLE,
                'some_status',
                'getGvCancelled'
            ],
            [
                LicenceEntity::LICENCE_CATEGORY_PSV,
                'some_status',
                'getPsvCancelled'
            ]
        ];
    }

    /**
     * Provider for when filter is expected to call getAllData
     *
     * @return array
     */
    public function processCallsGetAllDataProvider()
    {
        return [
            [LicenceEntity::LICENCE_CATEGORY_GOODS_VEHICLE, PublicationSectionEntity::APP_GRANTED_SECTION],
            [LicenceEntity::LICENCE_CATEGORY_GOODS_VEHICLE, PublicationSectionEntity::APP_REFUSED_SECTION],
            [LicenceEntity::LICENCE_CATEGORY_GOODS_VEHICLE, 'some_status'],
            [LicenceEntity::LICENCE_CATEGORY_PSV, 'some_status']
        ];
    }

    /**
     * Provider for when filter is expected to call getLicenceInfo
     *
     * @return array
     */
    public function processCallsGetLicenceInfoProvider()
    {
        return [
            [LicenceEntity::LICENCE_CATEGORY_GOODS_VEHICLE, PublicationSectionEntity::APP_WITHDRAWN_SECTION]
        ];
    }
}
