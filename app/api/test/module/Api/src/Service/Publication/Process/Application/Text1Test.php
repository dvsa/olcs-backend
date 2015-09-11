<?php

namespace Dvsa\OlcsTest\Api\Service\Publication\Process\Application;

use Dvsa\Olcs\Api\Service\Publication\Process\Application\Text1 as ApplicationText1;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery as m;
use Dvsa\Olcs\Api\Entity\Publication\PublicationLink;
use Dvsa\Olcs\Api\Entity\Licence\Licence as LicenceEntity;
use Dvsa\Olcs\Api\Service\Publication\ImmutableArrayObject;

/**
 * Class Text1Test
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
class Text1Test extends MockeryTestCase
{
    /**
     * @group publicationFilter
     *
     * Test the TmHearingText1 filter
     */
    public function testProcess()
    {
        $sut = new ApplicationText1();

        $previousPublication = 'previous publication';
        $licNo = 12345;
        $licenceType = 'SN';

        $input = [
            'previousPublication' => $previousPublication
        ];

        $expectedString = $licNo . $licenceType . ' (Previous Publication:(' . $previousPublication . '))';

        $licenceMock = m::mock(LicenceEntity::class);
        $licenceMock->shouldReceive('getLicNo')->andReturn($licNo);
        $licenceMock->shouldReceive('getLicenceTypeShortCode')->andReturn($licenceType);

        $publicationLink = m::mock(PublicationLink::class)->makePartial();
        $publicationLink->shouldReceive('getLicence')->andReturn($licenceMock);

        $output = $sut->process($publicationLink, new ImmutableArrayObject($input));
        $this->assertEquals($expectedString, $output->getText1());
    }
}
