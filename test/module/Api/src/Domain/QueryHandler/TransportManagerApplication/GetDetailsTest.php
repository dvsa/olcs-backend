<?php

/**
 * GetDetailsTest
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\TransportManagerApplication;

use Dvsa\Olcs\Api\Domain\QueryHandler\TransportManagerApplication\GetDetails as QueryHandler;
use Dvsa\Olcs\Api\Domain\Repository\TransportManagerApplication as Repo;
use Dvsa\Olcs\Transfer\Query\TransportManagerApplication\GetDetails as Query;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use ZfcRbac\Service\AuthorizationService;
use Mockery as m;

/**
 * GetDetailsTest
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class GetDetailsTest extends QueryHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new QueryHandler();
        $this->mockRepo('TransportManagerApplication', Repo::class);
        $this->mockRepo(
            'ApplicationOperatingCentre',
            \Dvsa\Olcs\Api\Domain\Repository\ApplicationOperatingCentre::class
        );
        $this->mockRepo('LicenceOperatingCentre', \Dvsa\Olcs\Api\Domain\Repository\LicenceOperatingCentre::class);
        $this->mockRepo('PreviousConviction', \Dvsa\Olcs\Api\Domain\Repository\PreviousConviction::class);
        $this->mockRepo('OtherLicence', \Dvsa\Olcs\Api\Domain\Repository\OtherLicence::class);
        $this->mockRepo('TmEmployment', \Dvsa\Olcs\Api\Domain\Repository\TmEmployment::class);

        $this->mockedSmServices = [AuthorizationService::class => m::mock('ZfcRbac\Service\AuthorizationService')];

        parent::setUp();
    }

    public function testHandleQuery()
    {
        $query = Query::create(['id' => 32]);

        $licence = new \Dvsa\Olcs\Api\Entity\Licence\Licence(
            m::mock(\Dvsa\Olcs\Api\Entity\Organisation\Organisation::class),
            m::mock(\Dvsa\Olcs\Api\Entity\System\RefData::class)
        );
        $licence->setId(653);
        $application = new \Dvsa\Olcs\Api\Entity\Application\Application(
            $licence,
            m::mock(\Dvsa\Olcs\Api\Entity\System\RefData::class),
            false
        );
        $tm = new \Dvsa\Olcs\Api\Entity\Tm\TransportManager();
        $tm->setId(213);

        $tmaOl = new \Dvsa\Olcs\Api\Entity\OtherLicence\OtherLicence();

        $tma = new \Dvsa\Olcs\Api\Entity\Tm\TransportManagerApplication();
        $application->setId(53);
        $tma->setApplication($application);
        $tma->setTransportManager($tm);
        $tma->setOtherLicences([$tmaOl]);

        $mockUser = m::mock()->shouldReceive('getTransportManager')->andReturn($tm)->getMock();

        $this->repoMap['TransportManagerApplication']->shouldReceive('fetchDetails')->with(32)->once()->andReturn($tma);
        $this->repoMap['TransportManagerApplication']->shouldReceive('fetchWithOperatingCentres')->with(32)->once();

        // loadApplicationOperatingCentres
        $this->repoMap['ApplicationOperatingCentre']->shouldReceive('fetchByApplication')->with(53)->once();

        // loadLicenceOperatingCentres
        $this->repoMap['LicenceOperatingCentre']->shouldReceive('fetchByLicence')->with(653)->once();

        // loadTransportManagerPreviousConvictions
        $this->repoMap['PreviousConviction']->shouldReceive('fetchByTransportManager')->with(213)->once();

        // loadTransportManagerOtherLicences
        $this->repoMap['OtherLicence']->shouldReceive('fetchByTransportManager')->with(213)->once();

        // loadTransportManagerEmployements
        $this->repoMap['TmEmployment']->shouldReceive('fetchByTransportManager')->with(213)->once();

        $this->mockedSmServices[AuthorizationService::class]->shouldReceive('getIdentity->getUser')
            ->andReturn($mockUser);

        $this->sut->handleQuery($query);
    }
}
