<?php

namespace Dvsa\OlcsTest\Api\Service\Publication\Process\Application;

use Dvsa\Olcs\Api\Service\Publication\Process\Application\Text3 as ApplicationText3;
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
 * Class Text3Test
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
class Text3Test extends MockeryTestCase
{
    /**
     * @dataProvider processTestProvider
     *
     * @param string $licenceType
     * @param string $publicationSection
     * @param array $licenceCancelled
     * @param string $calledFunction
     *
     * @group publicationFilter
     *
     * Test the application bus note filter
     */
    public function testProcess($licenceType, $publicationSection, $licenceCancelled, $calledFunction)
    {
        $sut = new ApplicationText3();

        $busNote = 'bus note';
        $licenceAddress = 'Licence address';
        $conditionUndertaking1 = 'condition undertaking 1';
        $conditionUndertaking2 = 'condition undertaking 2';
        $conditionUndertaking = [
            0 => $conditionUndertaking1,
            1 => $conditionUndertaking2,
        ];
        $transportManagers = 'transport managers';
        $operatingCentre1 = 'operating centre 1';
        $operatingCentre2 = 'operating centre 2';
        $operatingCentres = [
            0 => $operatingCentre1,
            1 => $operatingCentre2,
        ];

        $initialData = [
            'licenceAddress' => $licenceAddress,
            'publicationSection' => $publicationSection,
            'busNote' => $busNote,
            'operatingCentres' => $operatingCentres,
            'transportManagers' => $transportManagers,
            'conditionUndertaking' => $conditionUndertaking
        ];

        $inputData = array_merge($initialData, $licenceCancelled);

        $publicationLink = m::mock(PublicationLink::class)->makePartial();
        $publicationLink->shouldReceive('getLicence->getLicenceType')->andReturn($licenceType);
        $publicationLink->shouldReceive('getPublicationSection->getId')->andReturn($publicationSection);

        $context = new ImmutableArrayObject($inputData);
        $output = $sut->process($publicationLink, $context);

        $this->assertEquals(
            implode("\n", $sut->$calledFunction($context, [])),
            $output->getText3()
        );
    }

    /**
     * @return array
     */
    public function processTestProvider()
    {
        return [
            [
                LicenceEntity::LICENCE_CATEGORY_GOODS_VEHICLE,
                PublicationSectionEntity::APP_GRANTED_SECTION,
                [],
                'getPartialData'
            ],
            [
                LicenceEntity::LICENCE_CATEGORY_GOODS_VEHICLE,
                PublicationSectionEntity::APP_WITHDRAWN_SECTION,
                [],
                'getPartialData'
            ],
            [
                LicenceEntity::LICENCE_CATEGORY_GOODS_VEHICLE,
                PublicationSectionEntity::APP_REFUSED_SECTION,
                [],
                'getPartialData'
            ],
            [
                LicenceEntity::LICENCE_CATEGORY_GOODS_VEHICLE,
                'some_section',
                [],
                'getAllData'
            ],
            [
                LicenceEntity::LICENCE_CATEGORY_GOODS_VEHICLE,
                'some_section',
                ['licenceCancelled' => ''],
                'getPartialData'
            ],
            [
                LicenceEntity::LICENCE_CATEGORY_PSV,
                'some_section',
                [],
                'getAllData'
            ]
        ];
    }
}
