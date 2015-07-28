<?php

namespace Dvsa\OlcsTest\Api\Service\Publication\Context\Application;

use Dvsa\Olcs\Api\Service\Publication\Context\Application\BusNote;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery as m;
use Dvsa\Olcs\Api\Entity\Publication\PublicationLink;
use Dvsa\Olcs\Api\Entity\Publication\PublicationSection as PublicationSectionEntity;
use Dvsa\Olcs\Api\Entity\Application\Application as ApplicationEntity;
use Dvsa\Olcs\Api\Entity\Licence\Licence as LicenceEntity;
use Dvsa\Olcs\Api\Entity\System\RefData as RefDataEntity;
use Dvsa\Olcs\Api\Domain\QueryHandler\QueryHandlerInterface;

/**
 * Class BusNoteTest
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
class BusNoteTest extends MockeryTestCase
{
    /**
     * @dataProvider provideTestProvider
     *
     * @param $section
     * @param $expectedString
     *
     * @group publicationFilter
     *
     * Test the application bus note filter
     */
    public function testProvide($section, $expectedString)
    {

        $sut = new BusNote(m::mock(QueryHandlerInterface::class));

        $output = [
            'busNote' => sprintf($sut::BUS_STRING, $expectedString)
        ];

        $expectedOutput = new \ArrayObject($output);

        $status = m::mock(RefDataEntity::class);
        $status->shouldReceive('getId')->andReturn(LicenceEntity::LICENCE_CATEGORY_PSV);

        $publicationSection = new PublicationSectionEntity();
        $publicationSection->setId($section);

        $application = m::mock(ApplicationEntity::class);
        $application->shouldReceive('getGoodsOrPsv')->andReturn($status);

        $input = new PublicationLink();
        $input->setApplication($application);
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
        $sut = new BusNote(m::mock(QueryHandlerInterface::class));

        return [
            [PublicationSectionEntity::LIC_SURRENDERED_SECTION, $sut::BUS_SURRENDERED],
            [PublicationSectionEntity::LIC_REVOKED_SECTION, $sut::BUS_REVOKED],
            [PublicationSectionEntity::LIC_CNS_SECTION, $sut::BUS_CNS]
        ];
    }
}
