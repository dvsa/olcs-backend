<?php

namespace Dvsa\OlcsTest\Api\Service\Publication\Process\Application;

use Dvsa\Olcs\Api\Service\Publication\Process\Application\Text1 as ApplicationText1;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery as m;
use Dvsa\Olcs\Api\Entity\Publication\PublicationLink;
use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Dvsa\Olcs\Api\Service\Publication\ImmutableArrayObject;
use Dvsa\Olcs\Api\Entity\Organisation\Organisation;
use Dvsa\Olcs\Api\Entity\System\RefData;
use Dvsa\Olcs\Api\Entity\Application\Application;

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

        $input = [
            'previousPublication' => 9876
        ];

        $licence = new Licence(new Organisation(), new RefData());
        $licence->setLicNo('OB12345');
        $application = new Application($licence, new RefData(), false);
        $application->setLicenceType(new RefData(Licence::LICENCE_TYPE_STANDARD_NATIONAL));

        $publicationLink = new PublicationLink();
        $publicationLink->setLicence($licence);
        $publicationLink->setApplication($application);

        $output = $sut->process($publicationLink, new ImmutableArrayObject($input));

        $expectedString = "OB12345 SN\n(9876)";
        $this->assertEquals($expectedString, $output->getText1());
    }
}
