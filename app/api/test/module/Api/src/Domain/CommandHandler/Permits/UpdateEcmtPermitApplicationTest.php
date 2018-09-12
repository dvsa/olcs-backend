<?php
/**
 * Created by IntelliJ IDEA.
 * Date: 26/07/2018
 * Time: 12:02
 */

/**
 * Update ECMT
 *
 * @author Andy Newton <andy@vitri.ltd>
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Permits;

use DoctrineORMModuleTest\Assets\Entity\Country;
use Dvsa\Olcs\Api\Domain\CommandHandler\Permits\UpdateEcmtEmissions;
use Dvsa\Olcs\Api\Domain\CommandHandler\Permits\UpdateEcmtPermitApplication;
use Dvsa\Olcs\Api\Domain\Repository;
use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Dvsa\Olcs\Api\Entity\Permits\EcmtPermitApplication;
use Dvsa\Olcs\Api\Entity\Permits\Sectors;
use Dvsa\Olcs\Transfer\Command\Permits\UpdateEcmtPermitApplication as Cmd;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Doctrine\ORM\Query;
use Mockery as m;

class UpdateEcmtPermitApplicationTest extends CommandHandlerTestCase
{

    /** @var Licence */
    private $licenceReference7;


    public function setUp()
    {
        $this->sut = new UpdateEcmtPermitApplication();
        $this->mockRepo('EcmtPermitApplication', Repository\EcmtPermitApplication::class);
        $this->mockRepo('Sectors', Repository\Licence::class);
        $this->mockRepo('Country', Repository\Country::class);
        $this->mockRepo('Licence', Repository\Country::class);
        parent::setUp();
    }

    public function initReferences()
    {
        $this->licenceReference7 = m::mock(Licence::class);

        $this->references = [
            Licence::class => [
                7 => $this->licenceReference7
            ]
        ];

        parent::initReferences();
    }

    public function testHandleCommand()
    {
        $data = [
            'licence' => 7,
            'id' => 4,
            'emissions' => 1,
            'permitsRequired' => 5,
            'cabotage' => 1,
            'sectors' => 7,
            'countryIds' => ['AT', 'GR']
        ];

        $command = Cmd::create($data);
        $sectors = m::mock(Sectors::class);
        $application = m::mock(EcmtPermitApplication::class);
        $country = m::mock(Country::class);

        $this->repoMap['Country']->shouldReceive('getReference')
            ->andReturn($country);

        $application->shouldReceive('getLicence')->with();
        $application->shouldReceive('getDateReceived')->with();
        $application->shouldReceive('getLicence->getId')->with();

        $application->shouldReceive('getPermitsRequired')->withNoArgs()->once()->andReturn(5);

        $application->shouldReceive('update')
            ->andReturn($application);

        $this->repoMap['EcmtPermitApplication']->shouldReceive('fetchUsingId')
            ->with($command, Query::HYDRATE_OBJECT)
            ->andReturn($application)
            ->shouldReceive('save')
            ->once()
            ->with($application);

        $this->repoMap['Sectors']->shouldReceive('fetchById')
            ->with(7)
            ->andReturn($sectors);


        $result = $this->sut->handleCommand($command);


        $arrayResult = $result->toArray();

        $this->assertArrayHasKey('id', $arrayResult);
        $this->assertArrayHasKey('ecmtPermitApplication', $arrayResult['id']);
    }
}
