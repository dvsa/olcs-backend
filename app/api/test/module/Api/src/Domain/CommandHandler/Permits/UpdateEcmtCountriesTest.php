<?php

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Permits;

use Doctrine\ORM\Query;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Api\Domain\CommandHandler\Permits\UpdateEcmtCountries;
use Dvsa\Olcs\Api\Entity\Permits\EcmtPermitApplication;
use Dvsa\Olcs\Api\Entity\ContactDetails\Country;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Doctrine\Common\Collections\ArrayCollection;

use Mockery as m;

class UpdateEcmtCountriesTest extends CommandHandlerTestCase
{
    /** @var Country */
    private $countryReference4;

    /** @var Country */
    private $countryReference10;

    /** @var Country */
    private $countryReference12;

    public function setUp()
    {
        $this->mockRepo('EcmtPermitApplication', EcmtPermitApplication::class);
        $this->mockRepo('Country', Country::class);
        $this->sut = new UpdateEcmtCountries();

        parent::setUp();
    }

    public function initReferences()
    {
        $this->countryReference4 = m::mock(Country::class);
        $this->countryReference10 = m::mock(Country::class);
        $this->countryReference12 = m::mock(Country::class);

        $this->references = [
            Country::class => [
                4 => $this->countryReference4,
                10 => $this->countryReference10,
                12 => $this->countryReference12
            ]
        ];

        parent::initReferences();
    }

    public function testHandleCommand()
    {
        $ecmtPermitApplicationId = 20;

        $countryIds = [4, 10, 12];

        $command = m::mock(CommandInterface::class);
        $command->shouldReceive('getCountryIds')
            ->andReturn($countryIds);
        $command->shouldReceive('getId')
            ->andReturn($ecmtPermitApplicationId);

        $country = m::mock(Country::class);

        $this->repoMap['Country']->shouldReceive('getReference')
            ->andReturn($country);


        $ecmtPermitApplication = m::mock(EcmtPermitApplication::class);
        $ecmtPermitApplication->shouldReceive('getId')
            ->andReturn($ecmtPermitApplicationId);
        $ecmtPermitApplication->shouldReceive('updateCountrys')
            ->once()
            ->ordered()
            ->globally();

        $this->repoMap['EcmtPermitApplication']->shouldReceive('fetchById')
            ->with($ecmtPermitApplicationId)
            ->andReturn($ecmtPermitApplication);
        $this->repoMap['EcmtPermitApplication']->shouldReceive('save')
            ->with($ecmtPermitApplication)
            ->once()
            ->ordered()
            ->globally();

        $result = $this->sut->handleCommand($command);

        $this->assertEquals(
            [
                'ECMT Permit Application Restricted Countries updated'
            ],
            $result->getMessages()
        );
    }
}
