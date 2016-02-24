<?php

namespace Dvsa\OlcsTest\Api\Service\Publication\Context\Licence;

use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery as m;
use Dvsa\Olcs\Api\Entity\Publication\PublicationLink;
use Dvsa\Olcs\Api\Entity\System\RefData;
use Dvsa\Olcs\Api\Entity\Person\Person;

/**
 * Class LicenceNoTest
 *
 * @author Shaun Lizzio <shaun@lizzio.co.uk>
 */
class LicenceNoTest extends MockeryTestCase
{
    /**
     * @var \Dvsa\Olcs\Api\Service\Publication\Context\Licence\LicenceNo
     */
    private $sut;

    public function setUp()
    {
        $this->sut = new \Dvsa\Olcs\Api\Service\Publication\Context\Licence\LicenceNo(
            m::mock(\Dvsa\Olcs\Api\Domain\QueryHandler\QueryHandlerInterface::class)
        );

        parent::setUp();
    }

    public function testProvideWithLicence()
    {
        $publicationLink = $this->getPublicationLinkWithLicence();
        $context = new \ArrayObject();

        $this->sut->provide($publicationLink, $context);

        $this->assertSame(
            [
                'licenceNo' => 'UB1234567'
            ],
            $context->getArrayCopy()
        );
    }

    public function testProvideWithApplication()
    {
        $publicationLink = $this->getPublicationLinkWithApplication();
        $context = new \ArrayObject();

        $this->sut->provide($publicationLink, $context);

        $this->assertSame(
            [
                'licenceNo' => 'UB1234588'
            ],
            $context->getArrayCopy()
        );
    }

    /**
     * @return PublicationLink
     */
    private function getPublicationLinkWithLicence()
    {
        $publicationLink = new PublicationLink();
        $organisation = new \Dvsa\Olcs\Api\Entity\Organisation\Organisation();

        $licence = new \Dvsa\Olcs\Api\Entity\Licence\Licence($organisation, new RefData());
        $licence->setLicNo('UB1234567');
        $publicationLink->setLicence($licence);

        return $publicationLink;
    }

    /**
     * @return PublicationLink
     */
    private function getPublicationLinkWithApplication()
    {
        $publicationLink = new PublicationLink();

        $organisation = new \Dvsa\Olcs\Api\Entity\Organisation\Organisation();

        $licence = new \Dvsa\Olcs\Api\Entity\Licence\Licence($organisation, new RefData());
        $licence->setLicNo('UB1234588');
        $application = new \Dvsa\Olcs\Api\Entity\Application\Application($licence, new RefData(), false);

        $publicationLink->setApplication($application);

        return $publicationLink;
    }
}
