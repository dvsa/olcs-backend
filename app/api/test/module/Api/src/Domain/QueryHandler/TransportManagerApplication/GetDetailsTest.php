<?php

namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\TransportManagerApplication;

use Dvsa\Olcs\Api\Domain\QueryHandler;
use Dvsa\Olcs\Api\Domain\Repository;
use Dvsa\Olcs\Transfer\Query\TransportManagerApplication\GetDetails as Query;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use ZfcRbac\Service\AuthorizationService;
use Mockery as m;

/**
 * @author Mat Evans <mat.evans@valtech.co.uk>
 * @covers \Dvsa\Olcs\Api\Domain\QueryHandler\TransportManagerApplication\GetDetails
 */
class GetDetailsTest extends QueryHandlerTestCase
{
    /** @var  QueryHandler\TransportManagerApplication\GetDetails  */
    protected $sut;

    public function setUp()
    {
        $this->sut = new QueryHandler\TransportManagerApplication\GetDetails();

        $this->mockRepo('TransportManagerApplication', Repository\TransportManagerApplication::class);
        $this->mockRepo('ApplicationOperatingCentre', Repository\ApplicationOperatingCentre::class);
        $this->mockRepo('LicenceOperatingCentre', Repository\LicenceOperatingCentre::class);
        $this->mockRepo('PreviousConviction', Repository\PreviousConviction::class);
        $this->mockRepo('OtherLicence', Repository\OtherLicence::class);
        $this->mockRepo('TmEmployment', Repository\TmEmployment::class);
        $this->mockRepo('SystemParameter', Repository\SystemParameter::class);

        $this->mockedSmServices = [
            AuthorizationService::class => m::mock(\ZfcRbac\Service\AuthorizationService::class),
        ];

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

        $this->repoMap['SystemParameter']->shouldReceive('getDisableGdsVerifySignatures')->once();

        $this->mockedSmServices[AuthorizationService::class]->shouldReceive('getIdentity->getUser')
            ->andReturn($mockUser);

        $this->sut->handleQuery($query);
    }
}
