<?php

namespace Dvsa\OlcsTest\Api\Service\Publication\Process\Application;

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
        $sut = new \Dvsa\Olcs\Api\Service\Publication\Process\Application\Police();

        $person1 = new \Dvsa\Olcs\Api\Entity\Person\Person();
        $person2 = new \Dvsa\Olcs\Api\Entity\Person\Person();
        $person3 = new \Dvsa\Olcs\Api\Entity\Person\Person();

        $publicationLink = new PublicationLink();
        $publicationLink->addPoliceDatas(
            new \Dvsa\Olcs\Api\Entity\Publication\PublicationPoliceData($publicationLink, $person1)
        );

        $input = [
            'applicationPeople' => [$person2, $person3],
        ];
        $sut->process($publicationLink, new ImmutableArrayObject($input));

        $this->assertSame($publicationLink->getPoliceDatas()->count(), 2);
        $this->assertSame($publicationLink->getPoliceDatas()[0]->getPerson(), $person2);
        $this->assertSame($publicationLink->getPoliceDatas()[1]->getPerson(), $person3);
    }

    public function testProcessTransportManagers()
    {
        $sut = new \Dvsa\Olcs\Api\Service\Publication\Process\Application\Police();

        $person1 = new \Dvsa\Olcs\Api\Entity\Person\Person();

        $tm1 = $this->setupTransportManager();
        $tm2 = $this->setupTransportManager();
        $tm3 = $this->setupTransportManager();

        $publicationLink = new PublicationLink();
        $publicationLink->addPoliceDatas(
            new \Dvsa\Olcs\Api\Entity\Publication\PublicationPoliceData($publicationLink, $person1)
        );

        $input = [
            'applicationTransportManagers' => [$tm1, $tm2, $tm3],
        ];
        $sut->process($publicationLink, new ImmutableArrayObject($input));

        $this->assertSame($publicationLink->getPoliceDatas()->count(), 3);
        $this->assertSame($publicationLink->getPoliceDatas()[0]->getPerson(), $tm1->getHomeCd()->getPerson());
        $this->assertSame($publicationLink->getPoliceDatas()[1]->getPerson(), $tm2->getHomeCd()->getPerson());
        $this->assertSame($publicationLink->getPoliceDatas()[2]->getPerson(), $tm3->getHomeCd()->getPerson());
    }

    /**
     * Setup a stug transport manager
     *
     * @return \Dvsa\Olcs\Api\Entity\Tm\TransportManager
     */
    private function setupTransportManager()
    {
        $person = new \Dvsa\Olcs\Api\Entity\Person\Person();

        $cd = new \Dvsa\Olcs\Api\Entity\ContactDetails\ContactDetails(new \Dvsa\Olcs\Api\Entity\System\RefData());
        $cd->setPerson($person);

        $tm = new \Dvsa\Olcs\Api\Entity\Tm\TransportManager();
        $tm->setHomeCd($cd);

        return $tm;
    }
}
