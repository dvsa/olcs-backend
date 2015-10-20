<?php

namespace Dvsa\OlcsTest\Api\Service\Publication\Process\Licence;

use Mockery\Adapter\Phpunit\MockeryTestCase;
use Dvsa\Olcs\Api\Entity\Publication\PublicationLink;
use Dvsa\Olcs\Api\Service\Publication\ImmutableArrayObject;

/**
 * Class PoliceTest
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class PoliceTest extends MockeryTestCase
{
    public function testProcessApplicationPeople()
    {
        $sut = new \Dvsa\Olcs\Api\Service\Publication\Process\Licence\Police();

        $person1 = new \Dvsa\Olcs\Api\Entity\Person\Person();
        $person2 = new \Dvsa\Olcs\Api\Entity\Person\Person();
        $person3 = new \Dvsa\Olcs\Api\Entity\Person\Person();

        $publicationLink = new PublicationLink();
        $publicationLink->addPoliceDatas(
            new \Dvsa\Olcs\Api\Entity\Publication\PublicationPoliceData($publicationLink, $person1)
        );

        $input = [
            'licencePeople' => [$person2, $person3],
        ];
        $sut->process($publicationLink, new ImmutableArrayObject($input));

        $this->assertSame($publicationLink->getPoliceDatas()->count(), 2);
        $this->assertSame($publicationLink->getPoliceDatas()[0]->getPerson(), $person2);
        $this->assertSame($publicationLink->getPoliceDatas()[1]->getPerson(), $person3);
    }
}
