<?php

namespace Dvsa\OlcsTest\Api\Service\Publication\Context\Licence;

use Dvsa\Olcs\Api\Service\Publication\Context\Licence\LicenceCancelled;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery as m;
use Dvsa\Olcs\Api\Entity\Publication\PublicationLink;
use Dvsa\Olcs\Api\Entity\Publication\PublicationSection as PublicationSectionEntity;

/**
 * Class LicenceCancelledTest
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
class LicenceCancelledTest extends MockeryTestCase
{
    /**
     * @dataProvider provideTestProvider
     *
     * @param $section
     * @param $expectedString
     *
     * @group publicationFilter
     *
     * Test the application licence cancelled filter
     */
    public function testProvide($section, $expectedString)
    {

        $sut = new LicenceCancelled(m::mock(\Dvsa\Olcs\Api\Domain\QueryHandlerManager::class));

        $output = [
            'licenceCancelled' => $expectedString . $sut->createDate()
        ];

        $expectedOutput = new \ArrayObject($output);

        $publicationSection = new PublicationSectionEntity();
        $publicationSection->setId($section);

        $input = new PublicationLink();
        $input->setPublicationSection($publicationSection);

        $this->assertEquals($expectedOutput, $sut->provide($input, new \ArrayObject()));
    }

    /**
     * Filter provider
     *
     * @return array
     */
    public function provideTestProvider()
    {
        $sut = new LicenceCancelled(m::mock(\Dvsa\Olcs\Api\Domain\QueryHandlerManager::class));

        return [
            [PublicationSectionEntity::LIC_SURRENDERED_SECTION, $sut::LIC_SURRENDERED],
            [PublicationSectionEntity::LIC_TERMINATED_SECTION, $sut::LIC_TERMINATED],
            [PublicationSectionEntity::LIC_CNS_SECTION, $sut::LIC_CNS]
        ];
    }
}
